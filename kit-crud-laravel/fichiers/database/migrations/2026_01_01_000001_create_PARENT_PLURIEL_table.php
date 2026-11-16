<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('PARENT_PLURIEL', function (Blueprint $table) {
            $table->id();
            $table->string('PARENT_CHAMP');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('PARENT_PLURIEL');
    }
};
