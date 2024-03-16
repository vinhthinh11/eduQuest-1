<?php

namespace App\Http\Middleware;

use Closure;
use Facade\FlareClient\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class checkadminLogin
{
    public function handle(Request $request, Closure $next)
    {
        if (session()->has('login') && session()->get('login') == true) {
            return $next($request);
        } else {
            return redirect('/admin/login');
        }

    }
}
