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
        Schema::create('movies', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('descripcion');
            $table->decimal('calificacion', 3, 1); // Cambiado para que coincida con los datos de prueba
            $table->string('director')->nullable();
            $table->json('actores');
            $table->integer('duracion'); // En minutos
            $table->string('clasificacion');
            $table->string('genero');
            $table->string('lenguaje');
            $table->string('imagen')->nullable();
            $table->string('poster')->nullable();
            $table->string('trailer')->nullable();
            $table->string('omdb_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movies');
    }
};