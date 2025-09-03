<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('contrats', function (Blueprint $table) {
            $table->id();
            $table->date('date_contrat');
            $table->date('date_debut');
            $table->date('date_fin');
            $table->integer('periode_contrat');
            $table->string('periode_unite')->nullable();
            $table->decimal('montant_ht');
            $table->decimal('montant_ttc');
            $table->integer('tva');
            $table->string('devise')->default('EUR');
            $table->foreignId('client_id')->constrained('contacts')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contrats');
    }
};
