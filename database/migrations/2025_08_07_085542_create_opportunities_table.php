<?php

use App\Enums\OpportunityStatut;
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
        Schema::create('opportunities', function (Blueprint $table) {
            $table->id();
            $table->string('titre')->nullable();
            $table->text('note')->nullable();
            $table->decimal('montant_estime')->nullable();
            $table->enum('devise', ['MAD', 'EUR', 'USD'])->default('EUR');
            $table->date('date_echeance');
            $table->integer('probabilite')->nullable();
            $table->enum('status', array_column(OpportunityStatut::cases(), 'vlaue'))->default(OpportunityStatut::OPEN->value);
            $table->string('prefix')->nullable(); // Réajouté
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
