<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InsurancePolicy extends Model
{
    protected $primaryKey = 'policy_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'policy_id',
        'project_id',
        'provider_name',
        'policy_number',
        'policy_date',
        'description',
    ];

    protected $casts = [
        'policy_date' => 'date',
    ];

    /**
     * Get the project that owns this insurance policy.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id', 'project_id');
    }

    /**
     * Generate a unique policy ID.
     */
    public static function generatePolicyId(): string
    {
        $prefix = 'POL-';
        $year = date('Y');
        $lastPolicy = self::where('policy_id', 'like', $prefix . $year . '%')
            ->orderBy('policy_id', 'desc')
            ->first();

        if ($lastPolicy) {
            $lastNumber = (int) substr($lastPolicy->policy_id, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . $year . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
}

