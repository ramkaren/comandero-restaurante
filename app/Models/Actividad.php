<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Actividad extends Model
{
    public const UPDATED_AT = null;

    protected $table = 'actividades';

    protected $fillable = [
        'user_id', 'modulo', 'accion', 'descripcion',
        'sujeto_type', 'sujeto_id', 'propiedades', 'ip',
    ];

    protected function casts(): array
    {
        return ['propiedades' => 'array'];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}