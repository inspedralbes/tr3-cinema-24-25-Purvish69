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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('movieSessions')->constrained()->onDelete('cascade');
            $table->string('reference_id')->unique()->comment('Referencia de pago');
            $table->decimal('monto_total', 8, 2);
            $table->enum('metodo', ['tarjeta', 'paypal', 'efectivo']);
            $table->enum('estado', ['pagado', 'pendiente', 'rechazado'])->default('pendiente');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};