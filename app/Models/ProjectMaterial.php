<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'material_id',
        'quantity',
        'remark',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    /**
     * Get the project that owns this material record.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id', 'project_id');
    }

    /**
     * Get the material for this record.
     */
    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }
}
