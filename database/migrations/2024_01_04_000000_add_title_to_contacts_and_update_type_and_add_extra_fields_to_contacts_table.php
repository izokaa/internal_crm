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
        Schema::table('contacts', function (Blueprint $table) {
            $table->enum('title', ['Mr', 'Mrs', 'Ms', 'Dr', 'Prof'])->nullable();
            if (Schema::hasColumn('contacts', 'type')) {
                $table->dropColumn('type');
            }
            $table->enum('type', ['prospect', 'client', 'partner', 'fournisseur'])->default('prospect');
            $table->string('adresse')->nullable()->after('telephone');
            $table->string('profile_picture')->nullable();
            $table->enum('company_type', ['individual', 'corporate'])->default('individual');
            $table->string('company_name')->nullable()->after('company_type');
            $table->json('custom_fields')->nullable()->after('company_name'); 
            $table->string('website')->nullable()->after('custom_fields');  
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            //
        });
    }
};
