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
        Schema::create('users', function (Blueprint $table) {
            // Primary Key (String to allow "user_simon1")
            $table->string('user_id')->primary();
            
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password'); // Corresponds to password_hash
            $table->string('role')->default('Sales'); // Admin, Sales, PM, etc.
            $table->rememberToken();
            $table->timestamps(); // created_at, updated_at
        });

        // Optional default tables removed to align with requested schema
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        // Optional default tables were not created
    }
};
