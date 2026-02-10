<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Item;
use App\Models\ProjectMaterial;
use Illuminate\Support\Facades\DB;

class SyncInventoryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inventory:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync inventory items from project materials';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting inventory sync...');

        // Load all materials from database
        $materials = \App\Models\Material::all();

        $bar = $this->output->createProgressBar($materials->count());

        foreach ($materials as $material) {
            // Calculate total need for this material across all projects
            $totalNeeded = \App\Models\ProjectMaterial::where('material_id', $material->id)
                ->sum('quantity');

            // Find or create the item in the catalog
            $item = Item::firstOrNew(['name' => $material->name]);
            
            // Update fields
            $item->type = $material->category ?? 'Component'; // Use material category
            // Initialize stock if new (optional, keep existing if present)
            if (!$item->exists) {
                $item->stock_total_amount = 0;
                $item->stock_delivered = 0;
                $item->item_id = 'MAT-' . strtoupper(substr(md5($material->name), 0, 6)); // Generate a pseudo ID
                $item->unit = $material->unit ?? 'pcs'; // Use material unit
            }
            
            // IMPORTANT: Update the current need based on project requirements
            $item->stock_current_need = $totalNeeded;
            
            $item->save();
            
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Inventory sync completed successfully!');
    }
}
