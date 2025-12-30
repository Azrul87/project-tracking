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
        Schema::create('project_files', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('project_id');
            $table->string('original_name');
            $table->string('path');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->default(0);
            $table->string('uploaded_by')->nullable();
            $table->timestamps();

            $table->foreign('project_id')
                ->references('project_id')
                ->on('projects')
                ->onDelete('cascade');

            $table->foreign('uploaded_by')
                ->references('user_id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_files');
    }
};

