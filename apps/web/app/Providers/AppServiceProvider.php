<?php

namespace App\Providers;

use App\Repository\Auth\Dtos\User;
use App\Repository\Common\Libraries\AuthSession;
use App\Repository\Common\Services\ApiAuth;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
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

            /** @var ApiAuth $backend */
            $backend = resolve(ApiAuth::class);
            $user = $backend->me($token);

            if (blank($user)) {
                AuthSession::forget(['api_token', 'auth_user']);

                return null;
            }

            return $user;
        });
    }
}
