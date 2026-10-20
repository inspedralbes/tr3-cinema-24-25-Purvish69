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
        Schema::create('seat_holds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('funcion_id')->constrained('movieSessions')->onDelete('cascade');
            $table->string('asiento');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('hold_token');
            $table->timestamp('expires_at');
            $table->timestamps();

            $table->unique(['funcion_id', 'asiento']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seat_holds');
    }
};
