<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Material extends Model
{
    protected $fillable = [
        'code',
        'name',
        'category',
        'unit',
        'description',
    ];

    /**
     * Get all projects that use this material.
     */
    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_materials')
            ->withPivot('quantity', 'remark')
            ->withTimestamps();
    }

    /**
     * Get total quantity needed across all projects.
     */
    public function getTotalNeededAttribute(): int
    {
        return $this->projects()->sum('project_materials.quantity');
    }

    /**
     * Scope to filter by category.
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope to order by category and name.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('category')->orderBy('name');
    }
}
