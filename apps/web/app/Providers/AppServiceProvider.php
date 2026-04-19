<?php

namespace App\Providers;

use App\Repository\Auth\Dtos\User;
use App\Repository\Auth\Libraries\AuthSession;
use App\Repository\Auth\Services\ApiAuthService;
use Carbon\CarbonImmutable;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

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
        $this->configureRateLimiting();

        Model::unguard();
        Model::shouldBeStrict();

        Date::use(CarbonImmutable::class);

        Password::defaults(static fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null
        );

        RedirectResponse::macro('announce', function ($text, $type = 'ghost') {
            $this->session->push('announcements', [
                'id' => uniqid(Config::string('app.name'), true),
                'type' => $type,
                'content' => $text,
            ]);

            return $this;
        });

        Gate::define('admin', static function (User $user) {
            return $user->isAdmin();
        });

        Auth::viaRequest('backend-session', static function(): ?User {
            $token = AuthSession::get('api_token');

            if (blank($token)) {
                return null;
            }

            /** @var ApiAuthService $backend */
            $backend = resolve(ApiAuthService::class);
            $user = $backend->me($token);

            if (blank($user)) {
                AuthSession::forget(['api_token', 'auth_user']);

                return null;
            }

            return $user;
        });
    }

    private function configureRateLimiting(): void
    {
        RateLimiter::for('login', static function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input('email')).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', static function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }
}
