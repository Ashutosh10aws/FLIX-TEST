<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AdminCheck
{
    public function handle($request, Closure $next)
    {
        if (Auth::guard('admin')->user()->is_admin!=1) {
            return redirect('/admin/playlists');
        }
        return $next($request);
    }
}
