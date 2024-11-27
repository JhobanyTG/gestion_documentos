<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('backupacciones', function (Blueprint $table) {
            $table->id()->nullable(false); // El ID primario no puede ser null
            $table->string('admin_id', 100)->nullable();          // ID del administrador que realizó la acción
            $table->string('admin_nombre', 255)->nullable();      // Nombre del administrador que realizó la acción
            $table->string('tipo_peticion', 10)->nullable();      // POST, GET, PUT, DELETE
            $table->string('accion', 50)->nullable();            // CREATE, UPDATE, DELETE, etc.
            $table->text('descripcion')->nullable();             // Descripción detallada de la acción
            $table->string('usuario_afectado_id', 100)->nullable(); // ID del usuario afectado
            $table->string('usuario_afectado_nombre', 255)->nullable(); // Nombre completo del usuario afectado
            $table->text('detalles_cambios')->nullable();        // JSON con los detalles de los cambios realizados
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('backupacciones');
    }
};
