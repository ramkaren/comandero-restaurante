<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Models\Permission;

class Areas extends Model
{
    use SoftDeletes;

    protected $table = 'areas';

    protected $primaryKey = 'area_id';

    protected $fillable = [
        'institucion_id',
        'nombre',
        'descripcion',
        'is_active'
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'area_id', 'area_id');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'area_has_permissions', 'area_id', 'permission_id');
    }
}
