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
        Schema::create('movieSessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('movie_id')->constrained()->onDelete('cascade');
            $table->date('fecha');
            $table->enum('hora', ['16:00', '18:00', '20:00']);
            $table->enum('estado', ['disponible', 'cancelada', 'finalizada'])->default('disponible');
            $table->boolean('dia_espectador')->default(false);
            $table->boolean('fila_vip_activa')->default(false);
            $table->timestamps();
            
            // Restricción única para evitar duplicados
            $table->unique(['fecha', 'hora']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movieSessions');
    }
};