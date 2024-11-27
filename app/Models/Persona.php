<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    use HasFactory;

    protected $table = 'personas';

    protected $fillable = [
        'dni',
        'nombres',
        'apellido_p',
        'apellido_m',
        'f_nacimiento',
        'celular',
        'direccion',
        'avatar',
    ];

    protected static function booted()
    {
        static::deleting(function ($persona) {
            $persona->user()->delete();
        });
    }

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }

}
