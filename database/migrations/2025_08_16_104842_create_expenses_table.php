<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\ExpenseStatus;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_id')->nullable()->constrained()->cascadeOnDelete(); // optionnel
            $table->foreignId('opportunity_id')->nullable()->constrained('opportunities')->cascadeOnDelete(); // optionnel
            $table->decimal('montant_ht');
            $table->decimal('montant_ttc');
            $table->integer('tva');
            $table->string('devise')->default('EUR');
            $table->date('date_expense');
            $table->string('description')->nullable();
            // category_id
            $table->foreignId('category_id')->nullable()->constrained('expense_categories')->cascadeOnDelete(); // optionnel
            $table->enum('status', array_column(ExpenseStatus::cases(), 'value'))->default(ExpenseStatus::DRAFT->value);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
