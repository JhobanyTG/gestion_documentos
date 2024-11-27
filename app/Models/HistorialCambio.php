<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialCambio extends Model
{
    use HasFactory;

    protected $table = 'historial_cambios'; // Nombre de la tabla

    protected $fillable = [
        'documento_id',
        'estado_anterior',
        'estado_nuevo',
        'descripcion',
        'user_id',
        'sub_usuario_id',
    ];

    // Definición de las relaciones
    public function documento()
    {
        return $this->belongsTo(Documento::class, 'documento_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->withDefault();
    }

    public function subusuario()
    {
        return $this->belongsTo(SubUsuario::class, 'sub_usuario_id')->withDefault();
    }
}
