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
        Schema::create('examenes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('materia_id');
            $table->unsignedBigInteger('docente_id');
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->integer('cantidad_preguntas');
            $table->integer('options_per_question')->default(4);
            $table->integer('correct_answers')->default(1);
            $table->timestamps();

            $table->foreign('materia_id')->references('id')->on('materias')->onDelete('cascade');
            $table->foreign('docente_id')->references('id')->on('docentes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('examenes');
    }
};
