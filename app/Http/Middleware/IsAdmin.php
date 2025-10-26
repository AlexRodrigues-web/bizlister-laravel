<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class IsAdmin
{
    public function handle($request, Closure $next)
    {
        $ok = Auth::check() && (
            // Flag no banco
            (bool) (Auth::user()->is_admin ?? false)
            // OU Gate por e-mail (ENV ADMIN_EMAILS)
            || Gate::allows('admin')
        );

        if (!$ok) {
            abort(403, 'Acesso negado.');
        }

        return $next($request);
    }
}
