<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTipoDocumentoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Crear tabla tipo_documento
        Schema::create('tipodocumento', function (Blueprint $table) {
            $table->id();  // Primary key
            $table->string('nombre');  // Nombre del tipo de documento
            $table->string('descripcion')->nullable();  // Descripción (opcional)
            $table->timestamps();  // Para created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tipodocumento');
    }
}
