<?php

namespace App\Services\Pedidos;

use App\Models\Comanda;
use App\Repositories\Pedidos\PedidoRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PedidoService
{
    public function __construct(private PedidoRepository $repo)
    {
    }

    public function abrir(array $data, int $meseroId): Comanda
    {
        return DB::transaction(function () use ($data, $meseroId) {
        

            $this->repo->bloquearMesa($data['mesa_id']);

            if ($this->repo->mesaTieneComandaAbierta($data['mesa_id'])) {
                throw ValidationException::withMessages([
                    'mesa_id' => ['La mesa ya tiene una cuenta abierta.'],
                ]);
            }

            $comanda = $this->repo->crearComanda([
                'mesa_id'   => $data['mesa_id'],
                'mesero_id' => $meseroId,
                'estado'    => 'abierta',
                'total'     => 0,
            ]);

            $this->repo->crearCuenta($comanda, [
                'mesa_id'  => $data['mesa_id'],
                'estado'   => 'pendiente',
                'subtotal' => 0,
                'total'    => 0,
            ]);

            $this->registrarItems($comanda, $data['items']);

            return $this->repo->conRelaciones($comanda->fresh());
        });
    }


    public function agregarItems(int $comandaId, array $items): Comanda
    {
        return DB::transaction(function () use ($comandaId, $items) {
            $comanda = $this->repo->bloquearComanda($comandaId);

            if ($comanda->estado !== 'abierta' || $comanda->cuenta?->estado !== 'pendiente') {
                throw ValidationException::withMessages([
                    'comanda' => ['La cuenta ya no está abierta.'],
                ]);
            }

            $this->registrarItems($comanda, $items);

            return $this->repo->conRelaciones($comanda->fresh());
        });
    }

    private function registrarItems(Comanda $comanda, array $items): void
    {
        $productos = $this->repo->productosPorIds(array_column($items, 'producto_id'));
        $filas = [];

        foreach ($items as $i => $item) {
            $producto = $productos[$item['producto_id']];

            if (!$producto->disponible || !$producto->activo) {
                throw ValidationException::withMessages([
                    "items.$i.producto_id" => ["{$producto->nombre} no está disponible."],
                ]);
            }

            

            $filas[] = [
                'producto_id'     => $producto->id,
                'cantidad'        => $item['cantidad'],
                'precio_unitario' => $producto->precio,
                'subtotal'        => round($producto->precio * $item['cantidad'], 2),
            ];
        }

        $this->repo->crearDetalles($comanda, $filas);
        $this->repo->actualizarTotales($comanda);
    }
}