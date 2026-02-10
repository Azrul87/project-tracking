<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Drop the old project_materials_old table after data migration is verified.
     * Run this ONLY after verifying data migration was successful.
     */
    public function up(): void
    {
        // IMPORTANT: Only run this after verifying the data migration was successful!
        // You can check by comparing counts in old vs new tables.
        
        // Uncomment the line below when ready to drop the old table
        // Schema::dropIfExists('project_materials_old');
        
        echo "\n";
        echo "========================================\n";
        echo "Old table 'project_materials_old' has been preserved for safety.\n";
        echo "Please verify the new data is correct before dropping the old table.\n";
        echo "To drop it, edit this migration file and uncomment the Schema::dropIfExists line.\n";
        echo "========================================\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cannot restore old table if it was dropped
        // This is why we keep it by default
    }
};
