<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('piece_jointes', function (Blueprint $table) {
            $table->foreignId('devis_id')->nullable()->constrained('devis')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('piece_jointes', function (Blueprint $table) {
            $table->dropForeign(['devis_id']);
            $table->dropColumn(['devis_id']);
        });
    }
};
