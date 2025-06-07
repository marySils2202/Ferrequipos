<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Usuario;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Mapeo de modelos a políticas.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Registrar servicios de autenticación y autorización.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Antes de cualquier Gate o Policy: si es admin, dar acceso total
        Gate::before(function (Usuario $user, string $ability) {
            return $user->rol === 'admin' ? true : null;
        });
    }
}
