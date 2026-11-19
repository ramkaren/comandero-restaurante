<?php

namespace App\Observers;

use App\Models\Comanda;
use App\Models\DetalleComanda;
use App\Models\Mesa;
use App\Models\Pago;
use App\Models\Producto;
use App\Models\User;
use App\Services\Actividad\ActividadService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class ActividadObserver
{

    private const OCULTOS = ['password', 'remember_token', 'created_at', 'updated_at'];

    public function __construct(private ActividadService $actividad)
    {
    }

    public function created(Model $m): void
    {
        $this->guardar($m, $this->creado($m));
    }

    public function updated(Model $m): void
    {
        $this->guardar($m, $this->actualizado($m), true);
    }

    public function deleted(Model $m): void
    {
        $this->guardar($m, $this->eliminado($m));
    }

    private function guardar(Model $m, ?array $datos, bool $conCambios = false): void
    {
        if ($datos === null) {
            return;
        }

        [$modulo, $accion, $texto] = $datos;

        $this->actividad->registrar(
            $modulo,
            $accion,
            $texto,
            $m,
            $conCambios ? $this->propiedades($m) : []
        );
    }

    private function creado(Model $m): ?array
    {
        return match (true) {
            $m instanceof Pago => ['cobros', 'cobro', sprintf(
                'Cobró %s (%s) de la cuenta #%s', $this->dinero($m->monto), $m->metodo, $m->cuenta_id
            )],
            $m instanceof Comanda => ['pedidos', 'pedido_abierto', sprintf(
                'Abrió el pedido #%s en la mesa %s', $m->id, $m->mesa?->numero ?? '—'
            )],
            $m instanceof DetalleComanda => ['pedidos', 'pedido_producto', sprintf(
                'Agregó %s× %s al pedido #%s', $m->cantidad, $m->producto?->nombre ?? 'un producto', $m->comanda_id
            )],
            $m instanceof Mesa => ['mesas', 'mesa_creada', sprintf(
                'Agregó la mesa %s (capacidad %s)', $m->numero, $m->capacidad
            )],
            $m instanceof Producto => ['productos', 'producto_creado', sprintf(
                'Agregó el producto «%s» a %s', $m->nombre, $this->dinero($m->precio)
            )],
            $m instanceof User => ['usuarios', 'usuario_creado', sprintf('Creó el usuario %s', $m->name)],
            default => null,
        };
    }

    private function actualizado(Model $m): ?array
    {
        $cambios = $m->getChanges();

        
        if (!Arr::except($cambios, self::OCULTOS)) {
            return $m instanceof User && array_key_exists('password', $cambios)
                ? ['usuarios', 'usuario_password', sprintf('Cambió la contraseña del usuario %s', $m->name)]
                : null;
        }

        if ($m instanceof Mesa) {
            if (array_key_exists('estado', $cambios)) {
                $antes = $m->getOriginal('estado');

                return match (true) {
                    $antes === 'disponible' && $m->estado === 'ocupada'
                        => ['mesas', 'mesa_ocupada', sprintf('Ocupó la mesa %s', $m->numero)],
                    $antes === 'ocupada' && $m->estado === 'disponible'
                        => ['mesas', 'mesa_liberada', sprintf('Liberó la mesa %s', $m->numero)],
                    default => ['mesas', 'mesa_estado', sprintf(
                        'Mesa %s: %s → %s', $m->numero, $this->estado($antes), $this->estado($m->estado)
                    )],
                };
            }

            return ['mesas', 'mesa_editada', sprintf('Modificó la mesa %s', $m->numero)];
        }

        if ($m instanceof Comanda) {
            if (array_key_exists('estado', $cambios)) {
                return ['pedidos', 'pedido_estado', sprintf(
                    'Pedido #%s: %s → %s', $m->id, $this->estado($m->getOriginal('estado')), $this->estado($m->estado)
                )];
            }

            return null;
        }

        if ($m instanceof Pago) {
            return array_key_exists('estado', $cambios)
                ? ['cobros', 'pago_estado', sprintf(
                    'Pago #%s: %s → %s', $m->id, $this->estado($m->getOriginal('estado')), $this->estado($m->estado)
                )]
                : null;
        }

        if ($m instanceof Producto) {
            if (array_key_exists('activo', $cambios)) {
                return ['productos', 'producto_estado', sprintf(
                    '%s el producto «%s»', $m->activo ? 'Activó' : 'Desactivó', $m->nombre
                )];
            }

            if (array_key_exists('precio', $cambios)) {
                return ['productos', 'producto_precio', sprintf(
                    'Cambió el precio de «%s»: %s → %s',
                    $m->nombre, $this->dinero($m->getOriginal('precio')), $this->dinero($m->precio)
                )];
            }

            return ['productos', 'producto_editado', sprintf('Modificó el producto «%s»', $m->nombre)];
        }

        if ($m instanceof User) {
            return ['usuarios', 'usuario_editado', sprintf('Modificó el usuario %s', $m->name)];
        }

        return null;
    }

    private function eliminado(Model $m): ?array
    {
        return match (true) {
            $m instanceof Mesa => ['mesas', 'mesa_eliminada', sprintf('Eliminó la mesa %s', $m->numero)],
            $m instanceof Producto => ['productos', 'producto_eliminado', sprintf('Eliminó el producto «%s»', $m->nombre)],
            $m instanceof User => ['usuarios', 'usuario_eliminado', sprintf('Eliminó el usuario %s', $m->name)],
            $m instanceof Comanda => ['pedidos', 'pedido_eliminado', sprintf('Eliminó el pedido #%s', $m->id)],
            default => null,
        };
    }

    
    private function propiedades(Model $m): array
    {
        $despues = Arr::except($m->getChanges(), self::OCULTOS);
        $antes   = [];

        foreach (array_keys($despues) as $campo) {
            $antes[$campo] = $m->getOriginal($campo);
        }

        return $despues ? ['antes' => $antes, 'despues' => $despues] : [];
    }

    private function dinero(mixed $valor): string
    {
        return '$' . number_format((float) $valor, 2);
    }

    private function estado(mixed $estado): string
    {
        return str_replace('_', ' ', (string) $estado);
    }
}