<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('historial_cambios', function (Blueprint $table) {
            $table->id();

            // Llave foránea a la tabla de documentos
            $table->foreignId('documento_id')->constrained()->onDelete('cascade');

            // Estados anterior y nuevo del documento
            $table->string('estado_anterior');
            $table->string('estado_nuevo');

            // Descripción del cambio de estado
            $table->text('descripcion');

            // Llaves foráneas opcionales para usuarios y subusuarios
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('sub_usuario_id')->nullable();

            // Timestamps para saber cuándo se realizó el cambio
            $table->timestamps();

            // Relaciones con la tabla 'users' y 'subusuarios'
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('sub_usuario_id')->references('id')->on('subusuarios')->onDelete('set null')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historial_cambios');
    }
};
