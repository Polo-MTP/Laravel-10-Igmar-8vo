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
        Schema::create('user_sessions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('token_hash', 64)->unique()->notNull(); // SHA-256 del token, nunca en texto plano
            $table->integer('factors_completed')->default(0)->notNull();
            $table->boolean('is_fully_authenticated')->default(false)->notNull();
            $table->timestamp('expires_at')->notNull();
            $table->timestamp('last_activity_at')->notNull();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_sessions');
    }
};
