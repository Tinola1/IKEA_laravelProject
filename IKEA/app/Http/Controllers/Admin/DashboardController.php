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

        // ── KPI CARDS ──────────────────────────────────────────────────
        $totalRevenue = Order::whereNotIn('status', ['cancelled'])
            ->sum('total');

        $totalOrders = Order::count();

        $totalProducts = Product::where('is_available', true)->count();

        // Use DB query for role check to avoid Spatie dependency issues
        $totalCustomers = User::whereHas('roles', fn($q) => $q->where('name', 'customer'))->count();

        // Month-over-month revenue comparison
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

        // Orders this month vs last month
        $thisMonthOrders = Order::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();

        $lastMonthOrders = Order::whereYear('created_at', now()->subMonth()->year)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->count();

        // ── MONTHLY CHART DATA (all products combined) ──────────────────
        $monthlyData = Order::whereNotIn('status', ['cancelled'])
            ->whereYear('created_at', $year)
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(total) as revenue'),
                DB::raw('COUNT(*) as orders')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $chartLabels  = [];
        $chartRevenue = [];
        $chartOrders  = [];

        for ($m = 1; $m <= 12; $m++) {
            $chartLabels[]  = Carbon::create()->month($m)->format('M');
            $chartRevenue[] = $monthlyData->has($m) ? (float) $monthlyData[$m]->revenue : 0;
            $chartOrders[]  = $monthlyData->has($m) ? (int)   $monthlyData[$m]->orders  : 0;
        }

        // ── PER-PRODUCT MONTHLY DATA ────────────────────────────────────
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
            ->orderBy('products.name')
            ->orderBy('month')
            ->get();

        // Build structure: { productId: { name, months: [0..0, revenue..] } }
        $productChartData = [];
        foreach ($productMonthlyRaw as $row) {
            $pid = $row->product_id;
            if (!isset($productChartData[$pid])) {
                $productChartData[$pid] = [
                    'name'   => $row->product_name,
                    'months' => array_fill(1, 12, 0),
                ];
            }
            $productChartData[$pid]['months'][$row->month] = (float) $row->revenue;
        }

        // Flatten months to 0-indexed array for Chart.js
        $productChartJson = [];
        foreach ($productChartData as $pid => $data) {
            $productChartJson[] = [
                'id'     => $pid,
                'name'   => $data['name'],
                'data'   => array_values($data['months']),
            ];
        }

        // All products list for the dropdown filter
        $allProducts = Product::orderBy('name')->get(['id', 'name']);

        // ── ORDER STATUS BREAKDOWN ──────────────────────────────────────
        $statusBreakdown = Order::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        // ── RECENT ORDERS ───────────────────────────────────────────────
        $recentOrders = Order::with('user')
            ->latest()
            ->take(8)
            ->get();

        // ── LOW STOCK ───────────────────────────────────────────────────
        $lowStock = Product::with('category')
            ->where('stock', '<=', 5)
            ->orderBy('stock')
            ->get();

        // ── AVAILABLE YEARS for year picker ────────────────────────────
        $availableYears = Order::selectRaw('YEAR(created_at) as y')
            ->groupBy('y')
            ->orderByDesc('y')
            ->pluck('y')
            ->toArray();

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
            'availableYears', 'year'
        ));
    }
}