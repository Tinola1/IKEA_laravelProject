<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $year = (int) request('year', now()->year);

        // ── DATE RANGE (for the date-picker bar chart) ──────────────────
        $dateFrom = request('date_from')
            ? Carbon::parse(request('date_from'))->startOfDay()
            : now()->subDays(29)->startOfDay();

        $dateTo = request('date_to')
            ? Carbon::parse(request('date_to'))->endOfDay()
            : now()->endOfDay();

        // ── KPI CARDS ──────────────────────────────────────────────────
        $totalRevenue = Order::whereNotIn('status', ['cancelled'])->sum('total');
        $totalOrders  = Order::count();
        $totalProducts = Product::where('is_available', true)->count();
        $totalCustomers = User::whereHas('roles', fn($q) => $q->where('name', 'customer'))->count();

        $thisMonthRevenue = Order::whereNotIn('status', ['cancelled'])
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('total');

        $lastMonthRevenue = Order::whereNotIn('status', ['cancelled'])
            ->whereYear('created_at', now()->subMonth()->year)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->sum('total');

        $revenueChange = $lastMonthRevenue > 0
            ? round((($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1)
            : ($thisMonthRevenue > 0 ? 100 : 0);

        $thisMonthOrders = Order::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)->count();

        $lastMonthOrders = Order::whereYear('created_at', now()->subMonth()->year)
            ->whereMonth('created_at', now()->subMonth()->month)->count();

        // ── YEARLY CHART DATA ───────────────────────────────────────────
        $monthlyData = Order::whereNotIn('status', ['cancelled'])
            ->whereYear('created_at', $year)
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(total) as revenue'),
                DB::raw('COUNT(*) as orders')
            )
            ->groupBy('month')->orderBy('month')->get()->keyBy('month');

        $chartLabels = $chartRevenue = $chartOrders = [];
        for ($m = 1; $m <= 12; $m++) {
            $chartLabels[]  = Carbon::create()->month($m)->format('M');
            $chartRevenue[] = $monthlyData->has($m) ? (float) $monthlyData[$m]->revenue : 0;
            $chartOrders[]  = $monthlyData->has($m) ? (int)   $monthlyData[$m]->orders  : 0;
        }

        // ── PER-PRODUCT MONTHLY DATA (for individual product filter) ────
        $productMonthlyRaw = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders',   'order_items.order_id',   '=', 'orders.id')
            ->whereNotIn('orders.status', ['cancelled'])
            ->whereYear('orders.created_at', $year)
            ->select(
                'products.id   as product_id',
                'products.name as product_name',
                DB::raw('MONTH(orders.created_at) as month'),
                DB::raw('SUM(order_items.quantity * order_items.price) as revenue')
            )
            ->groupBy('products.id', 'products.name', 'month')
            ->orderBy('products.name')->orderBy('month')->get();

        $productChartData = [];
        foreach ($productMonthlyRaw as $row) {
            $pid = $row->product_id;
            if (!isset($productChartData[$pid])) {
                $productChartData[$pid] = ['name' => $row->product_name, 'months' => array_fill(1, 12, 0)];
            }
            $productChartData[$pid]['months'][$row->month] = (float) $row->revenue;
        }

        $productChartJson = [];
        foreach ($productChartData as $pid => $data) {
            $productChartJson[] = ['id' => $pid, 'name' => $data['name'], 'data' => array_values($data['months'])];
        }

        $allProducts = Product::orderBy('name')->get(['id', 'name']);

        // ── DATE RANGE CHART DATA ───────────────────────────────────────
        // Group by day if range ≤ 31 days, by week if ≤ 90, by month otherwise
        $diffDays = $dateFrom->diffInDays($dateTo);

        if ($diffDays <= 31) {
            $groupFormat  = '%Y-%m-%d';
            $labelFormat  = 'M d';
            $carbonFormat = 'Y-m-d';
        } elseif ($diffDays <= 90) {
            $groupFormat  = '%x-%v'; // ISO year-week
            $labelFormat  = null;    // handled below
            $carbonFormat = null;
        } else {
            $groupFormat  = '%Y-%m';
            $labelFormat  = 'M Y';
            $carbonFormat = 'Y-m';
        }

        $rangeRaw = Order::whereNotIn('status', ['cancelled'])
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->select(
                DB::raw("DATE_FORMAT(created_at, '{$groupFormat}') as period"),
                DB::raw('SUM(total) as revenue'),
                DB::raw('COUNT(*) as orders')
            )
            ->groupBy('period')->orderBy('period')->get();

        $rangeLabels  = [];
        $rangeRevenue = [];
        $rangeOrders  = [];

        foreach ($rangeRaw as $row) {
            if ($diffDays <= 31) {
                $rangeLabels[] = Carbon::parse($row->period)->format($labelFormat);
            } elseif ($diffDays <= 90) {
                $rangeLabels[] = 'Wk ' . explode('-', $row->period)[1];
            } else {
                $rangeLabels[] = Carbon::createFromFormat('Y-m', $row->period)->format($labelFormat);
            }
            $rangeRevenue[] = (float) $row->revenue;
            $rangeOrders[]  = (int)   $row->orders;
        }

        // ── PIE CHART: Revenue per product ─────────────────────────────
        $pieData = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders',   'order_items.order_id',   '=', 'orders.id')
            ->whereNotIn('orders.status', ['cancelled'])
            ->select(
                'products.name',
                DB::raw('SUM(order_items.quantity * order_items.price) as revenue')
            )
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('revenue')
            ->get();

        $totalPieRevenue = $pieData->sum('revenue') ?: 1;

        $pieLabels     = [];
        $pieRevenues   = [];
        $piePercentages = [];

        // Show top 8 products, group the rest as "Others"
        $topProducts = $pieData->take(8);
        $others      = $pieData->skip(8);

        foreach ($topProducts as $row) {
            $pieLabels[]      = $row->name;
            $pieRevenues[]    = (float) $row->revenue;
            $piePercentages[] = round(($row->revenue / $totalPieRevenue) * 100, 1);
        }

        if ($others->count() > 0) {
            $othersRevenue    = $others->sum('revenue');
            $pieLabels[]      = 'Others (' . $others->count() . ' products)';
            $pieRevenues[]    = (float) $othersRevenue;
            $piePercentages[] = round(($othersRevenue / $totalPieRevenue) * 100, 1);
        }

        // ── ORDER STATUS BREAKDOWN ──────────────────────────────────────
        $statusBreakdown = Order::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')->pluck('total', 'status');

        // ── RECENT ORDERS + LOW STOCK ───────────────────────────────────
        $recentOrders = Order::with('user')->latest()->take(8)->get();
        $lowStock     = Product::with('category')->where('stock', '<=', 5)->orderBy('stock')->get();

        // ── AVAILABLE YEARS ─────────────────────────────────────────────
        $availableYears = Order::selectRaw('YEAR(created_at) as y')
            ->groupBy('y')->orderByDesc('y')->pluck('y')->toArray();

        if (empty($availableYears)) {
            $availableYears = [now()->year];
        }

        return view('admin.dashboard', compact(
            'totalRevenue', 'totalOrders', 'totalProducts', 'totalCustomers',
            'thisMonthRevenue', 'lastMonthRevenue', 'revenueChange',
            'thisMonthOrders', 'lastMonthOrders',
            'chartLabels', 'chartRevenue', 'chartOrders',
            'productChartJson', 'allProducts',
            'statusBreakdown', 'recentOrders', 'lowStock',
            'availableYears', 'year',
            // Date range chart
            'rangeLabels', 'rangeRevenue', 'rangeOrders',
            'dateFrom', 'dateTo',
            // Pie chart
            'pieLabels', 'pieRevenues', 'piePercentages'
        ));
    }
}