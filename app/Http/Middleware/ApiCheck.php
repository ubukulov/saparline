<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;

class ApiCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        $user = User::whereToken($request->header('token'))->first();
        if ($user){
            $request['user']= $user;
            return $next($request);
        }else{
           return response()->json('Не авторизован',401);
        }
    }
}
