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
        Schema::create('intento_examenes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('examen_id');
            $table->unsignedBigInteger('alumno_id');
            $table->integer('puntuacion')->default(0);
            $table->integer('total')->default(0);
            $table->enum('estado', ['en_progreso', 'en_revision', 'calificado'])->default('en_progreso');
            $table->unsignedBigInteger('revisado_por')->nullable();
            $table->timestamp('fecha_revision')->nullable();
            $table->boolean('version_anterior')->default(false);
            $table->timestamps();

            $table->foreign('examen_id')->references('id')->on('exams')->onDelete('cascade');
            $table->foreign('alumno_id')->references('id')->on('alumnos')->onDelete('cascade');
            $table->foreign('revisado_por')->references('id')->on('docentes')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('intento_examenes');
    }
};
