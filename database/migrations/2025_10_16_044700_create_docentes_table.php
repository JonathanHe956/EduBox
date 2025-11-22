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
        Schema::create('docentes', function (Blueprint $table) {
            $table->id(); // Esto crea una columna `id` de tipo UNSIGNED BIGINT
            $table->string('nombre');
            $table->string('apaterno');
            $table->string('amaterno');
            $table->char('sexo', 1);
            $table->date('fecha_nacimiento');
            $table->integer('edad');
            $table->string('foto')->nullable();
            $table->string('email')->unique();
            $table->unsignedBigInteger('carrera_id');
            $table->foreign('carrera_id')->references('id')->on('carreras')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('docentes');
    }
};
