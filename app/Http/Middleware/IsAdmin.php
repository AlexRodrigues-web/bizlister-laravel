<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Se no futuro users.is_admin existir, já funciona:
        $hasFlag = ($user && property_exists($user, "is_admin") && (int)($user->is_admin) === 1);

        // Fallback: whitelist (config/admin.php)
        $whitelist = config('admin.superadmins', []);
        $inList = $user && in_array(strtolower($user->email), array_map('strtolower', $whitelist), true);

        if ($hasFlag || $inList) {
            return $next($request);
        }

        abort(403, 'Acesso restrito ao administrador.');
    }
}
