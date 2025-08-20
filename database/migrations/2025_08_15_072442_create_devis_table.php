<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\DevisStatus;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('devis', function (Blueprint $table) {
            $table->id();
            $table->string('quote_number', 50)->unique();
            $table->decimal('total_ht', 10, 2)->default(0);
            $table->decimal('total_ttc', 10, 2)->default(0);
            $table->decimal('tva', 10, 2)->default(20);
            $table->string('devise')->default('EUR');
            // remise
            $table->decimal('remise', 10, 2)->default(0);
            // date d'émission
            $table->date('date_emission')->nullable();
            // date devis
            $table->date('date_devis')->nullable();
            // durée de validité
            $table->integer('validity_duration')->default(30); // en jours
            $table->enum('status', array_column(DevisStatus::cases(), 'value'))->default(DevisStatus::DRAFT->value);
            $table->text('note')->nullable();
            $table->foreignId('contact_id')->constrained('contacts')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devis');
    }
};
