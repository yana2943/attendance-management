<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Contracts\LogoutResponse;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Http\Requests\LoginRequest as FortifyLoginRequest;
use App\Http\Requests\CustomLoginRequest;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
        \Laravel\Fortify\Http\Requests\LoginRequest::class,
        \App\Http\Requests\CustomLoginRequest::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        RateLimiter::for('login', function (Request $request) {
            $email = (string)$request->email;
            return Limit::perMinute(10)->by($email . '|' . $request->ip());
        });

        // 2FA
        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        // View
        Fortify::registerView(fn() => view('auth.register'));
        Fortify::loginView(fn() => view('auth.login'));

        // ログイン認証処理
        Fortify::authenticateUsing(function ($request) {
    $user = \App\Models\User::where('email', $request->email)->first();

    if ($user && \Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
        // 管理者ログインの場合、セッションにフラグをセット
        if ($request->input('is_admin_login')) {
            session(['is_admin_login' => true]);
        } else {
            // 一般ログイン時は必ず false にしておく
            session(['is_admin_login' => false]);
        }
        return $user;
    }

    throw \Illuminate\Validation\ValidationException::withMessages([
                'email' => ['ログイン情報が登録されていません'],
            ]);
        });

        // ログイン後リダイレクト
       $this->app->singleton(LoginResponse::class, function () {
    return new class implements \Laravel\Fortify\Contracts\LoginResponse {
        public function toResponse($request)
        {
            $isAdmin = $request->session()->get('is_admin_login', false);

            return $isAdmin
                ? redirect()->route('admin.attendance.list')
                : redirect()->route('attendance.index');
        }
    };
});

        // ログアウト後リダイレクト
        $this->app->singleton(LogoutResponse::class, function () {
            return new class implements LogoutResponse {
                public function toResponse($request)
                {
                    $isAdmin = $request->session()->get('is_admin_login', false);

                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    return $isAdmin
                        ? redirect()->route('admin.login')
                        : redirect()->route('login');
                }
            };
        });
    }
}
