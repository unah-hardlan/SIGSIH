<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{

    protected $middleware = [

        \App\Http\Middleware\TrustProxies::class,
        \Illuminate\Http\Middleware\HandleCors::class,
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];


    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\LogCrudActions::class,
        ],

        'api' => [

            \Illuminate\Routing\Middleware\ThrottleRequests::class . ':api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\LogCrudActions::class,
        ],
    ];


    protected $middlewareAliases = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'precognitive' => \Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
        'signed' => \App\Http\Middleware\ValidateSignature::class,
        'spa.init' => \App\Http\Middleware\SpaInitializer::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,


        'jwt.auth' => \App\Http\Middleware\JwtMiddleware::class,


        'auth.jwt.web' => \App\Http\Middleware\JwtWebAuth::class,
        'jwt.refresh' => \App\Http\Middleware\JwtRefresh::class,
        'permiso' => \App\Http\Middleware\PermissionMiddleware::class,
        'auto.permiso' => \App\Http\Middleware\AutoPermissionMiddleware::class,
        'force.profile' => \App\Http\Middleware\ForceProfileCompletion::class,
        'force.password.change' => \App\Http\Middleware\ForcePasswordChange::class,
        'client.only' => \App\Http\Middleware\ClientOnly::class,
        'block.client' => \App\Http\Middleware\BlockClientFromAdmin::class,
        'admin.only' => \App\Http\Middleware\AdminOnly::class,
        'check.cliente.perfil' => \App\Http\Middleware\CheckClientePerfilCompleto::class,
    ];
}
