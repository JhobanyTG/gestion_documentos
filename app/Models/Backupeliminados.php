<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BackupEliminado extends Model
{
    use HasFactory;

    protected $table = 'backupeliminados';

    protected $fillable = [
        'user_id',
        'titulo_documento',
        'motivo',
    ];
}
