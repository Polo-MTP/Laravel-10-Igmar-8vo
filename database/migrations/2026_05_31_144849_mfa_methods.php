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
        Schema::create('mfa_methods', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('mfa_type_id')->constrained('mfa_types')->onDelete('restrict');
            $table->string('secret', 512)->notNull();
            $table->integer('factor_step')->notNull();
            $table->boolean('is_verified')->default(false)->notNull();
            $table->boolean('is_active')->default(true)->notNull();
            $table->timestamps();
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mfa_methods');
    }
};
