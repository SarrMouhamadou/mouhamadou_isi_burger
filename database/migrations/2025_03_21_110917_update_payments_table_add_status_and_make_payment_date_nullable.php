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
        Schema::table('payments', function (Blueprint $table) {
            // Ajouter la colonne status avec une valeur par défaut
            $table->string('status')->default('pending')->after('amount');
            // Rendre payment_date nullable
            $table->dateTime('payment_date')->nullable()->change();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dateTime('payment_date')->nullable(false)->change();
        });
    }
};
