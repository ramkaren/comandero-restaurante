<?php

namespace App\Providers;

use App\Models\Comanda;
use App\Models\DetalleComanda;
use App\Models\Mesa;
use App\Models\Pago;
use App\Models\Producto;
use App\Models\User;
use App\Observers\ActividadObserver;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        foreach ([Comanda::class, DetalleComanda::class, Mesa::class, Pago::class, Producto::class, User::class] as $modelo) {
    $modelo::observe(ActividadObserver::class);
}
        Gate::before(function ($user, string $ability): ?bool {
            return $user->hasRole('super_usuario') ? true : null;
        });
    }
}
