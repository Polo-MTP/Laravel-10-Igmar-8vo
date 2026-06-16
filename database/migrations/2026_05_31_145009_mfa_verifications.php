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
         Schema::create('mfa_verifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('mfa_method_id')->constrained('mfa_methods')->onDelete('cascade');
            $table->string('code_hash', 64)->notNull();  // SHA-256 del código OTP, nunca en texto plano
            $table->boolean('used')->default(false)->notNull();
            $table->timestamp('expires_at')->notNull();  // ventana corta, máx 120 segundos
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mfa_verifications');
    }
};
