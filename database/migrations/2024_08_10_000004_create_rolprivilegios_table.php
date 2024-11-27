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
        Schema::create('rolprivilegios', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('privilegio_id');
            $table->unsignedBigInteger('rol_id');
            $table->timestamps();

            $table->foreign('privilegio_id')->references('id')->on('privilegios')->onDelete('cascade');
            $table->foreign('rol_id')->references('id')->on('rols')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rolprivilegios');
    }
};
