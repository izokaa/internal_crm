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
        Schema::create('etape_pipelines', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('icon')->nullable(); // Nouvelle colonne pour l'icône
            $table->integer('ordre');
            $table->foreignId('pipeline_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('etape_pipelines');
    }
};
