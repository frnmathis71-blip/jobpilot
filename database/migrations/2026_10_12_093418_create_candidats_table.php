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
        // Table des candidats.
        // ⚠ Pas de colonne "fullname" pour l'instant, alors que le code l'utilise.
        Schema::create('candidats', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            // Offre à laquelle le candidat est rattaché (facultatif).
            $table->foreignId('offer_id')->nullable()->constrained('offers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidats');
    }
};
