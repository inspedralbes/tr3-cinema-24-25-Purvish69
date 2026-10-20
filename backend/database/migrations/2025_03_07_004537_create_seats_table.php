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
        Schema::create('seats', function (Blueprint $table) {
            $table->id();
            $table->foreignId(column: 'movieSession_id')->constrained()->onDelete('cascade');
            $table->enum('fila', ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L']);
            $table->integer('numero')->comment('Número de butaca (1-10)');
            $table->enum('tipo', ['normal', 'vip'])->default('normal');
            $table->enum('estado', ['libre', 'ocupada'])->default('libre');
            $table->timestamps();
            
            // Una butaca solo puede existir una vez por sesión
            $table->unique(['movieSession_id', 'fila', 'numero']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seats');
    }
};