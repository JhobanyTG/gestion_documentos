<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Privilegio extends Model
{
    use HasFactory;

    protected $table = 'privilegios';

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    public function roles()
    {
        return $this->belongsToMany(Rol::class, 'rolprivilegios', 'privilegio_id', 'rol_id');
    }
}
