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
        Schema::table('projects', function (Blueprint $table) {
            $table->decimal('total_invoiced', 15, 2)->nullable()->after('payment_status');
            $table->decimal('total_paid', 15, 2)->nullable()->after('total_invoiced');
            $table->string('payment_type')->nullable()->after('payment_method'); // Self finance, Loan, etc.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['total_invoiced', 'total_paid', 'payment_type']);
        });
    }
};
