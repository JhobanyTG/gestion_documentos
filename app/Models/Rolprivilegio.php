<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolPrivilegio extends Model
{
    use HasFactory;

    protected $table = 'rolprivilegios';

    protected $fillable = [
        'privilegio_id',
        'rol_id',
    ];

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'rol_id');
    }

    public function privilegio()
    {
        return $this->belongsTo(Privilegio::class, 'privilegio_id');
    }
}
