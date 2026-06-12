<?php

namespace App\Http\Controllers;

use App\Http\Resources\AuditLogResource;
use App\Models\AuditLog;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Get the latest 10 audit log entries.
     */
    public function index(): JsonResponse
    {
        $auditLogs = AuditLogResource::collection(
            AuditLog::query()
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
        );

        return response()->json($auditLogs);
    }

    /**
     * Get count of audit logs from the last 24 hours that the user hasn't seen.
     */
    public function unread(Request $request): JsonResponse
    {
        $lastSeen = $request->session()->get('notifications_last_seen');

        $query = AuditLog::query()
            ->where('created_at', '>=', Carbon::now()->subHours(24));

        if ($lastSeen) {
            $query->where('created_at', '>', $lastSeen);
        }

        $count = $query->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Mark notifications as read by storing the current timestamp in the session.
     */
    public function markAsRead(Request $request): JsonResponse
    {
        $request->session()->put('notifications_last_seen', Carbon::now());

        return response()->json(['success' => true]);
    }
}
