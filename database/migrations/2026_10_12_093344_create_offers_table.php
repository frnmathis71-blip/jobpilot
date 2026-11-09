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
        // Table des offres d'emploi.
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            // Entreprise qui publie l'offre ; si l'entreprise est supprimée, ses offres aussi.
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete()->nullable();
            $table->integer('Salary');
            $table->string('Name');
            $table->string('Description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};
