<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\FactureStatus;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('factures', function (Blueprint $table) {
            $table->id();
            $table->string('numero_facture', 50)->unique()->nullable();
            $table->date('date_facture')->nullable();
            // écheance paiement
            $table->date('echeance_payment')->nullable();
            $table->decimal('montant_ht');
            $table->decimal('montnat_ttc');
            $table->decimal('tva')->default(20);
            $table->enum('status', array_column(FactureStatus::cases(), 'value'))->defaul(FactureStatus::PENDING);
            $table->foreignId('contrat_id')->constrained('contrats');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('factures');
    }
};
