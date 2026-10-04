<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Comanda extends Model
{
    use HasFactory;

    protected $table = 'comandas';

    protected $fillable = ['mesa_id', 'mesero_id', 'estado', 'total'];

    protected function casts(): array
    {
        return ['total' => 'decimal:2'];
    }

    public function mesa(): BelongsTo
    {
        return $this->belongsTo(Mesa::class);
    }

    public function mesero(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mesero_id');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(DetalleComanda::class);
    }

    public function cuenta(): HasOne
    {
        return $this->hasOne(Cuenta::class);
    }

    public function recalcularTotal(): void
    {
        $this->update(['total' => $this->detalles()->sum('subtotal')]);
    }
}