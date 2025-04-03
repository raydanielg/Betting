<?php

use App\Http\Middleware\Authenticate;
use App\Http\Middleware\CheckStatus;
use App\Http\Middleware\Demo;
use App\Http\Middleware\KycMiddleware;
use App\Http\Middleware\MaintenanceMode;
use App\Http\Middleware\RedirectIfAdmin;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Middleware\RedirectIfNotAdmin;
use App\Http\Middleware\RegistrationStep;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;
use Laramin\Utility\VugiChugi;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        using: function () {
            Route::namespace('App\Http\Controllers')->middleware([VugiChugi::mdNm()])->group(function () {
                Route::middleware(['web'])
                    ->namespace('Admin')
                    ->prefix('admin')
                    ->name('admin.')
                    ->group(base_path('routes/admin.php'));

                Route::middleware(['web', 'maintenance'])
                    ->namespace('Gateway')
                    ->prefix('ipn')
                    ->name('ipn.')
                    ->group(base_path('routes/ipn.php'));

                Route::middleware(['web', 'maintenance'])->prefix('user')->group(base_path('routes/user.php'));
                Route::middleware(['web', 'maintenance'])->group(base_path('routes/web.php'));

            });
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->group('web', [
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\LanguageMiddleware::class,
            \App\Http\Middleware\ActiveTemplateMiddleware::class,
        ]);

        $middleware->alias([
            'auth.basic'            => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
            'cache.headers'         => \Illuminate\Http\Middleware\SetCacheHeaders::class,
            'can'                   => \Illuminate\Auth\Middleware\Authorize::class,
            'auth'                  => Authenticate::class,
            'guest'                 => RedirectIfAuthenticated::class,
            'password.confirm'      => \Illuminate\Auth\Middleware\RequirePassword::class,
            'signed'                => \Illuminate\Routing\Middleware\ValidateSignature::class,
            'throttle'              => \Illuminate\Routing\Middleware\ThrottleRequests::class,
            'verified'              => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,

            'admin'                 => RedirectIfNotAdmin::class,
            'admin.guest'           => RedirectIfAdmin::class,

            'check.status'          => CheckStatus::class,
            'demo'                  => Demo::class,
            'kyc'                   => KycMiddleware::class,
            'registration.complete' => RegistrationStep::class,
            'maintenance'           => MaintenanceMode::class,
        ]);

        $middleware->validateCsrfTokens(
            except: ['user/deposit', 'ipn*']
        );
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->shouldRenderJsonWhen(function () {
            if (request()->is('api/*')) {
                return true;
            }
        });
        $exceptions->respond(function (Response $response) {
            if ($response->getStatusCode() === 401) {
                if (request()->is('api/*')) {
                    $notify[] = 'Unauthorized request';
                    return response()->json([
                        'remark'  => 'unauthenticated',
                        'status'  => 'error',
                        'message' => ['error' => $notify],
                    ]);
                }
            }

            return $response;
        });
    })->create();
