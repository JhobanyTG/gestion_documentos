<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subusuario extends Model
{
    use HasFactory;

    protected $table = 'subusuarios';

    protected $fillable = [
        'user_id',
        'subgerencia_id',
        'cargo'
    ];

    public function subgerencia()
    {
        return $this->belongsTo(Subgerencia::class, 'subgerencia_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
