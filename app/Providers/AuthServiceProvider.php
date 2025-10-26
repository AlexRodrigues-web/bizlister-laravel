<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('admin', function (User $user): bool {
            // 1) habilita admin por flag no banco
            if ((bool) ($user->is_admin ?? false)) {
                return true;
            }

            // 2) habilita admin por e-mail no .env (lista separada por vírgula)
            $list   = (string) env('ADMIN_EMAILS', 'admin@tecinfosp.local');
            $admins = array_filter(array_map(
                'strtolower',
                array_map('trim', explode(',', $list))
            ));

            return in_array(strtolower((string) $user->email), $admins, true);
        });
    }
}
