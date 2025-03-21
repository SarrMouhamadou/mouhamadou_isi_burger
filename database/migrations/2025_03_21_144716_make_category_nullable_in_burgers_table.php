<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('burgers', function (Blueprint $table) {
            $table->string('category')->nullable()->default(null)->change();
        });
    }

    public function down(): void
    {
        Schema::table('burgers', function (Blueprint $table) {
            $table->string('category')->nullable(false)->default('Classique')->change();
        });
    }
};
