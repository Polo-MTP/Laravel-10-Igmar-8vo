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
        Schema::create('login_attempts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // nullable por si el email no existe
            $table->string('email_attempted', 255)->nullable(); // guardar aunque el usuario no exista
            $table->string('ip_address', 45)->notNull();
            $table->text('user_agent')->nullable();
            $table->string('status', 30)->notNull();   // 'success', 'failed_password', 'failed_mfa', 'account_locked', 'account_inactive'
            $table->integer('factor_step')->default(1)->notNull();
            $table->text('failure_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('login_attempts');
    }
};
