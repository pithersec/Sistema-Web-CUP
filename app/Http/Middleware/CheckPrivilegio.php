<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPrivilegio
{
    public function handle(Request $request, Closure $next, string ...$privilegios)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->tienePrivilegio('sistema.total')) {
            return $next($request);
        }

        foreach ($privilegios as $privilegio) {
            if ($user->tienePrivilegio($privilegio)) {
                return $next($request);
            }
        }

        abort(403, 'No tienes permiso para acceder a esta sección.');
    }
}