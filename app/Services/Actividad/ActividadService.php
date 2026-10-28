<?php

namespace App\Services\Actividad;

use App\Models\Actividad;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ActividadService
{
    
    public function registrar(
        string $modulo,
        string $accion,
        string $descripcion,
        ?Model $sujeto = null,
        array $propiedades = [],
        ?int $userId = null
    ): void {
    
        if (app()->runningInConsole()) {
            return;
        }

        try {
            Actividad::create([
                'user_id'     => $userId ?? $this->actorId(),
                'modulo'      => $modulo,
                'accion'      => $accion,
                'descripcion' => $descripcion,
                'sujeto_type' => $sujeto?->getMorphClass(),
                'sujeto_id'   => $sujeto?->getKey(),
                'propiedades' => $propiedades ?: null,
                'ip'          => request()->ip(),
            ]);
        } catch (\Throwable $e) {
            report($e);
        }
    }

    /**  movimiento: usuario de la API (JWT) o de la sesión web. */
    private function actorId(): ?int
    {
        try {
            return Auth::guard('api')->id() ?? Auth::id();
        } catch (\Throwable) {
            return Auth::id();
        }
    }
}