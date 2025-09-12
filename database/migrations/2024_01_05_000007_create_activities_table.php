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
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['task', 'event']);
            $table->string('statut'); // e.g., pending, completed, cancelled, scheduled
            $table->dateTime('date_debut')->nullable();
            $table->dateTime('date_fin')->nullable();
            $table->dateTime('due_date')->nullable();
            $table->boolean('prioritaire')->default(false);
            $table->foreignId('opportunity_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // User who created/assigned
            $table->foreignId('contact_id')->nullable()->constrained()->onDelete('cascade');
            $table->boolean('is_all_day')->nullable(); // Specific to events
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
