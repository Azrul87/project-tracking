<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectItem extends Model
{
    protected $primaryKey = 'project_item_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'project_id',
        'item_id',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    /**
     * Get the project that owns this project item.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id', 'project_id');
    }

    /**
     * Get the item for this project item.
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id', 'item_id');
    }
}

