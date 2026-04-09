<?php

declare(strict_types=1);

namespace App\Providers;

use App\Dtos\Modules\MorphTypesItems;
use App\Models\User;
use Carbon\CarbonImmutable;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind('morph_types', fn ($app): Collection => collect());
        $this->app->bind('module_records', fn ($app): Collection => collect());
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();
        $this->configureDefaults();
        $this->loadMorphRelations();

        Gate::define('viewApiDocs', static function (?User $user): bool {
            if (blank($user)) {
                return false;
            }

            return $user->isAdmin();
        });
    }

    /**
     * Configure the rate limiters for the application.
     */
    private function configureRateLimiting(): void
    {
        // Default API rate limiter - 60 requests per minute
        RateLimiter::for('api', static fn (Request $request) => Limit::perMinute(60)->by($request->user()?->id ?: $request->ip()));

        // Auth endpoints - more restrictive (prevent brute force)
        RateLimiter::for('auth', static fn (Request $request) => Limit::perMinute(5)->by($request->ip()));

        // Authenticated user requests - higher limit
        RateLimiter::for('authenticated', static fn (Request $request) => $request->user()
            ? Limit::perMinute(120)->by($request->user()->id)
            : Limit::perMinute(60)->by($request->ip()));
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    private function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Model::unguard();
        Model::shouldBeStrict();

        Password::defaults(static fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->uncompromised()
            : null,
        );
    }

    private function loadMorphRelations(): void
    {
        /** @var Collection<int, MorphTypesItems> $relations */
        $relations = resolve('morph_types');
        if (! $relations instanceof Collection) {
            return;
        }

        if ($relations->isEmpty()) {
            return;
        }

        Relation::morphMap(
            $relations->pluck('class', 'key')->toArray()
        );
    }
}
