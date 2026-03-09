<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    /**
     * Confiar en todos los proxies reversos (nginx, Apache, load balancers).
     * Sin esto, $request->ip() devuelve siempre la IP del proxy y el
     * RateLimiter agrupa a todos los usuarios bajo una sola IP, generando 429.
     *
     * En producción restringir a la IP concreta del proxy si se conoce,
     * p.ej.: protected $proxies = ['10.0.0.1'];
     */
    protected $proxies = '*';


    protected $headers =
    Request::HEADER_X_FORWARDED_FOR |
        Request::HEADER_X_FORWARDED_HOST |
        Request::HEADER_X_FORWARDED_PORT |
        Request::HEADER_X_FORWARDED_PROTO |
        Request::HEADER_X_FORWARDED_AWS_ELB;
}
