<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $action = $request->get('action');
        $userId = $request->get('user_id');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $query = AuditLog::with('user');

        if ($action) {
            $query->where('action', 'like', "%{$action}%");
        }

        if ($userId) {
            $query->where('user_id', $userId);
        }

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        $logs = $query->orderByDesc('id')->paginate(20)->withQueryString();

        $users = User::orderBy('name')->get(['id', 'name', 'role']);
        $distinctActions = AuditLog::select('action')->distinct()->pluck('action');

        return view('settings.audit_logs', compact(
            'logs',
            'users',
            'distinctActions',
            'action',
            'userId',
            'startDate',
            'endDate'
        ));
    }
}
