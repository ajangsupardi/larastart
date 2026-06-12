<?php

namespace App\Http\Controllers;

use App\Http\Resources\AuditLogResource;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AuditController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->input('search');

        $auditLogs = AuditLogResource::collection(
            AuditLog::query()
                ->with('user')
                ->when($search, fn ($query, $search) => $query
                    ->where(function ($q) use ($search) {
                        $q->whereHas('user', fn ($q) => $q
                            ->whereRaw('LOWER(name) LIKE ?', ['%'.strtolower($search).'%']))
                            ->orWhereRaw('LOWER(auditable_type) LIKE ?', ['%'.strtolower($search).'%']);
                    }))
                ->orderBy('created_at', 'desc')
                ->paginate(15)
                ->withQueryString()
        );

        return Inertia::render('AuditLog/Index', [
            'auditLogs' => $auditLogs,
            'filters' => $request->only('search'),
        ]);
    }
}
