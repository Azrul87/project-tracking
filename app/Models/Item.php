<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    protected $primaryKey = 'item_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'item_id',
        'name',
        'type',
        'brand',
        'model',
        'unit',
        'warranty_details',
        'stock_total_amount',
        'stock_delivered',
        'stock_current_need',
    ];

    protected $casts = [
        'stock_total_amount' => 'integer',
        'stock_delivered' => 'integer',
        'stock_current_need' => 'integer',
    ];

    /**
     * Get all project items that use this item.
     */
    public function projectItems(): HasMany
    {
        return $this->hasMany(ProjectItem::class, 'item_id', 'item_id');
    }

    /**
     * Generate a unique item ID.
     */
    public static function generateItemId(): string
    {
        $prefix = 'ITEM-';
        $year = date('Y');
        $lastItem = self::where('item_id', 'like', $prefix . $year . '%')
            ->orderBy('item_id', 'desc')
            ->first();

        if ($lastItem) {
            $lastNumber = (int) substr($lastItem->item_id, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . $year . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
}

