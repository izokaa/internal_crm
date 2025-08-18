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
            $table->foreignId('expense_id')->nullable()->constrained('expenses')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('piece_jointes', function (Blueprint $table) {
            $table->dropForeign(['expense_id']);
            $table->dropColumn('expense_id');
        });
    }
};
