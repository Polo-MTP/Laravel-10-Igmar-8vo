<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();
                if ($user && $user->role) {
                    if ($user->role->name === 'Administrador') {
                        return redirect()->route('dashboard-admin');
                    } elseif ($user->role->name === 'Invitado') {
                        return redirect()->route('dashboard-invitado');
                    }
                }
                return redirect()->route('dashboard');
            }
        }

        return $next($request);
    }
}
