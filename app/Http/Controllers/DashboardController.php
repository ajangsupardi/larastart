<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        $totalUsers = User::count();
        $verifiedUsers = User::whereNotNull('email_verified_at')->count();
        $totalRoles = Role::count();
        $recentUsers = User::where('created_at', '>=', now()->subDays(30))->count();

        return Inertia::render('Dashboard', [
            'stats' => [
                'users' => ['label' => 'Total Users', 'value' => $totalUsers, 'trend' => 0],
                'verified' => ['label' => 'Verified', 'value' => $verifiedUsers, 'trend' => 0],
                'roles' => ['label' => 'Total Roles', 'value' => $totalRoles, 'trend' => 0],
                'recent' => ['label' => 'New (30d)', 'value' => $recentUsers, 'trend' => 0],
            ],
        ]);
    }
}
