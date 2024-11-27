<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Rol;
use App\Models\Privilegio;
use App\Models\RolPrivilegio;
use App\Models\Persona;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Crear personas
        $persona = Persona::create([
            'dni' => '12345678',
            'nombres' => 'Juan',
            'apellido_p' => 'Pérez',
            'apellido_m' => 'Gonzales',
            'f_nacimiento' => '1990-01-01',
            'celular' => '987654321',
            'direccion' => 'Calle Falsa 123',
        ]);

        // Crear privilegios
        $privilegios = Privilegio::insert([
            [
                'nombre' => 'Acceso Total',
                'descripcion' => 'Permite acceso a todas las áreas del sistema.',
            ],
            [
                'nombre' => 'Acceso a Gerencia',
                'descripcion' => 'Tiene toda la funcionalidad de Gerencia, Subgerencia y Documentos.',
            ],
            [
                'nombre' => 'Acceso a Subgerencia',
                'descripcion' => 'Tiene acceso a Subgerencia y a Documentos.',
            ],
            [
                'nombre' => 'Acceso a Subusuario',
                'descripcion' => 'Tiene acceso a Subusuario y a Documentos.',
            ],
            [
                'nombre' => 'Acceso a Documentos',
                'descripcion' => 'Tiene acceso a crear, validar y publicar documentos.',
            ],
            [
                'nombre' => 'Acceso a Crear Documento',
                'descripcion' => 'El usuario puede crear documentos.',
            ],
            [
                'nombre' => 'Acceso a Validar Documento',
                'descripcion' => 'El usuario puede validar el documento.',
            ],
            [
                'nombre' => 'Acceso a Publicar Documento',
                'descripcion' => 'El usuario puede publicar el documento.',
            ],
        ]);

        // Crear roles
        $roles = Rol::insert([
            [
                'nombre' => 'SuperAdmin',
                'descripcion' => 'Rol con acceso completo al sistema.',
            ],
            [
                'nombre' => 'Gerente',
                'descripcion' => 'Rol con acceso a funcionalidades de gerencia, subgerencia y documentos.',
            ],
            [
                'nombre' => 'SubGerente',
                'descripcion' => 'Rol con acceso a funcionalidades de subgerencia y documentos.',
            ],
            [
                'nombre' => 'SubUsuario',
                'descripcion' => 'Rol con acceso limitado a funcionalidades específicas.',
            ],
        ]);

        // Crear un rol y asociar privilegios
        $rol = Rol::where('nombre', 'SuperAdmin')->first();
        $privilegio = Privilegio::where('nombre', 'Acceso Total')->first();

        // Asociar el privilegio al rol
        RolPrivilegio::create([
            'privilegio_id' => $privilegio->id,
            'rol_id' => $rol->id,
        ]);

        // Crear un usuario
        User::create([
            'nombre_usuario' => 'Admin Juan',
            'email' => 'SAGDocuemntos@gmail.com',
            'password' => bcrypt('SAGDocuemntos2024'),
            'estado' => 'activo',
            'rol_id' => $rol->id,
            'persona_id' => $persona->id,
        ]);
    }
}
