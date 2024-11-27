<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    use HasFactory;

    protected $table = 'rols';

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    public function privilegios()
    {
        return $this->belongsToMany(Privilegio::class, 'rolprivilegios', 'rol_id', 'privilegio_id');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
