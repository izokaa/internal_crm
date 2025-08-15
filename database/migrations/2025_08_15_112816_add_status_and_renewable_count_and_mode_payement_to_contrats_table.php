<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\ContratStatus;
use App\Enums\ModePayment;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('contrats', function (Blueprint $table) {
            $table->enum('status', array_column(ContratStatus::cases(), 'value'))->default(ContratStatus::ACTIVE->value);
            $table->enum('mode_payment', array_column(ModePayment::cases(), 'value'))->default(ModePayment::BANK_TRANSFER->value);
            $table->integer('renewable_count')->default(0)->comment('Nombre de renouvellements autorisés pour le contrat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contrats', function (Blueprint $table) {
            $table->dropColumn(['status', 'mode_payment', 'renewable_count']);
        });
    }
};
