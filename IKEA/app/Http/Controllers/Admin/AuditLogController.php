<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Collection;

class AuditLogController extends Controller
{
    public function index()
    {
        // Placeholder — returns empty collection until spatie/laravel-activitylog is installed
        // Install with: composer require spatie/laravel-activitylog
        // Then replace this with: $logs = \Spatie\Activitylog\Models\Activity::with('causer')->latest()->paginate(50);
        $logs  = collect();
        $users = User::orderBy('name')->get(['id', 'name', 'email']);

        return view('admin.audit-logs.index', compact('logs', 'users'));
    }
}