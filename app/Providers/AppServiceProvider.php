<?php

namespace App\Providers;

use App\Models\District;
use App\Models\Occupation;
use App\Models\Province;
use App\Models\Regency;
use App\Models\Role;
use App\Models\User;
use App\Models\Village;
use App\Observers\AuditObserver;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Inertia\ExceptionResponse;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        $this->configureInertiaErrorHandling();
        $this->registerObservers();
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }

    /**
     * Configure Inertia error handling to render custom error pages.
     */
    protected function configureInertiaErrorHandling(): void
    {
        Inertia::handleExceptionsUsing(function (ExceptionResponse $response) {
            if (in_array($response->statusCode(), [403, 404, 500, 503])) {
                return $response->render('Error', [
                    'status' => $response->statusCode(),
                    'name' => config('app.name'),
                ]);
            }
        });
    }

    /**
     * Register Eloquent model observers for audit logging.
     */
    protected function registerObservers(): void
    {
        User::observe(AuditObserver::class);
        Role::observe(AuditObserver::class);
        Province::observe(AuditObserver::class);
        Regency::observe(AuditObserver::class);
        District::observe(AuditObserver::class);
        Village::observe(AuditObserver::class);
        Occupation::observe(AuditObserver::class);
    }
}
