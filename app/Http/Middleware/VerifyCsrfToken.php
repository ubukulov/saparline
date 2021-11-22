<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Closure;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        //''
    ];
	
	public function handle($request, Closure $next)
	{
		if ($request->getHost() == 'http://localhost:8080/') {
			// skip CSRF check
			return $next($request);
		}

		return parent::handle($request, $next);
	}
}
