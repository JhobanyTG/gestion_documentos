<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BackupAccion extends Model
{
    use HasFactory;

    protected $table = 'backupacciones';

    protected $fillable = [
        'admin_id',
        'admin_nombre',
        'tipo_peticion',
        'accion',
        'descripcion',
        'usuario_afectado_id',
        'usuario_afectado_nombre',
        'detalles_cambios'
    ];

    protected $casts = [
        'detalles_cambios' => 'array'
    ];
}
