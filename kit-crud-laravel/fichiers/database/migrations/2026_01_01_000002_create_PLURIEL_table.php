<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('PLURIEL', function (Blueprint $table) {
            $table->id();
            $table->string('CHAMP1');
            $table->decimal('CHAMP2', 8, 2);
            $table->foreignId('PARENT_SINGULIER_id')->constrained('PARENT_PLURIEL')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('PLURIEL');
    }
};
