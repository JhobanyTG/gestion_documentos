<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subgerencia extends Model
{
    // use HasFactory;

    // protected $fillable = [
    //     'usuario_id', 'gerencia_id', 'nombre', 'descripcion', 'telefono', 'direccion', 'estado'
    // ];

    // public function gerencia()
    // {
    //     return $this->belongsTo(Gerencia::class);
    // }

    // public function user()
    // {
    //     return $this->belongsTo(User::class, 'usuario_id');
    // }

    use HasFactory;

    protected $fillable = [
        'usuario_id',
        'gerencia_id',
        'nombre',
        'descripcion',
        'telefono',
        'direccion',
        'estado',
    ];

    public function gerencia()
    {
        return $this->belongsTo(Gerencia::class, 'gerencia_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function subusuarios()
    {
        return $this->hasMany(Subusuario::class, 'subgerencia_id');
    }

    public function documentos()
    {
        return $this->hasMany(Documento::class);
    }
}
