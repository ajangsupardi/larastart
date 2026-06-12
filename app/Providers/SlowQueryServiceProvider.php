<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class SlowQueryServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        DB::listen(function (QueryExecuted $event): void {
            $time = $event->time;

            if ($time > 100) {
                $timestamp = Carbon::now()->toDateTimeString();
                $query = $event->sql;
                $bindings = $event->bindings;
                $bindingsFormatted = implode(', ', array_map(fn ($b) => is_null($b) ? 'NULL' : (string) $b, $bindings));
                $logEntry = "[{$timestamp}] {$time}ms - {$query} - [{$bindingsFormatted}]\n";

                file_put_contents(
                    storage_path('logs/slow-queries.log'),
                    $logEntry,
                    FILE_APPEND | LOCK_EX,
                );
            }
        });
    }
}
