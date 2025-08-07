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
        Schema::create('opportunities', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->text('description');
            $table->text('note');
            $table->decimal('montant_estime');
            $table->date('date_echeance');
            $table->integer('probabilite');
            $table->text('brief');
            $table->enum('status', ['ouvert', 'ferme', 'en retard', 'annule']);
            $table->string('prefix');
            $table->foreignId('contact_id')->constrained()->cascadeOnDelete();
            $table->foreignId('source_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pipeline_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('etape_pipeline_id')->nullable()->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opportunities');
    }
};
