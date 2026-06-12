<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Services\GeoCacheService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

class SettingController extends Controller
{
    public function index(): Response
    {
        $settings = [
            'name' => Setting::get('app_name', config('app.name')),
            'timezone' => Setting::get('app_timezone', config('app.timezone')),
            'email_notifications' => (bool) Setting::get('email_notifications', true),
            'activity_log_days' => (int) Setting::get('activity_log_days', 30),
        ];

        $system = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'os' => PHP_OS,
            'disk_total' => @disk_total_space(base_path()) ?: 'N/A',
            'disk_free' => @disk_free_space(base_path()) ?: 'N/A',
            'db_size' => $this->getDatabaseSize(),
            'app_url' => config('app.url'),
            'app_env' => config('app.env'),
        ];

        return Inertia::render('Settings/Index', [
            'settings' => $settings,
            'system' => $system,
        ]);
    }

    public function updateGeneral(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'timezone' => ['required', 'string'],
        ]);

        Setting::set('app_name', $validated['name']);
        Setting::set('app_timezone', $validated['timezone']);

        return to_route('settings.index')
            ->with('success', 'Settings updated.');
    }

    public function updateAppearance(Request $request): RedirectResponse
    {
        $request->validate([
            'theme' => ['required', 'in:light,dark,system'],
        ]);

        return to_route('settings.index')
            ->with('success', 'Appearance settings updated.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = $request->user();

        if (! Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors([
                'current_password' => 'The provided password does not match your current password.',
            ]);
        }

        $user->forceFill(['password' => $validated['password']])->save();

        return to_route('settings.index')
            ->with('success', 'Password updated.');
    }

    public function updateNotifications(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email_notifications' => ['required', 'boolean'],
            'activity_log_days' => ['required', 'integer', 'min:7', 'max:365'],
        ]);

        Setting::set('email_notifications', $validated['email_notifications'] ? '1' : '0');
        Setting::set('activity_log_days', $validated['activity_log_days']);

        return to_route('settings.index')
            ->with('success', 'Notification settings updated.');
    }

    public function clearCache(): RedirectResponse
    {
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        GeoCacheService::flush();

        return back()->with('success', 'Cache cleared successfully.');
    }

    public function systemInfo(): Response
    {
        return response()->json([
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'os' => PHP_OS,
            'disk_total' => @disk_total_space(base_path()) ?: 'N/A',
            'disk_free' => @disk_free_space(base_path()) ?: 'N/A',
            'db_size' => $this->getDatabaseSize(),
            'app_url' => config('app.url'),
            'app_env' => config('app.env'),
        ]);
    }

    private function getDatabaseSize(): string
    {
        $driver = DB::getDriverName();

        return match ($driver) {
            'sqlite' => $this->getSqliteDbSize(),
            'mysql', 'mariadb' => $this->getMysqlDbSize(),
            'pgsql' => $this->getPgsqlDbSize(),
            default => 'N/A',
        };
    }

    private function getSqliteDbSize(): string
    {
        $path = config('database.connections.sqlite.database');

        if (! file_exists($path)) {
            return 'N/A';
        }

        $bytes = filesize($path);

        return $this->formatBytes($bytes);
    }

    private function getMysqlDbSize(): string
    {
        $database = DB::getDatabaseName();
        $result = DB::selectOne(
            'SELECT ROUND(SUM(data_length + index_length), 0) AS size FROM information_schema.tables WHERE table_schema = ?',
            [$database],
        );

        return $this->formatBytes((int) ($result->size ?? 0));
    }

    private function getPgsqlDbSize(): string
    {
        $database = DB::getDatabaseName();
        $result = DB::selectOne('SELECT pg_database_size(?) AS size', [$database]);

        return $this->formatBytes((int) ($result->size ?? 0));
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes <= 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = (int) floor(log($bytes, 1024));

        return round($bytes / (1024 ** $i), 2).' '.$units[$i];
    }
}
