<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $primaryKey = 'payment_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'payment_id',
        'project_id',
        'description',
        'invoice_date',
        'invoice_amount',
        'payment_date',
        'payment_amount',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'invoice_amount' => 'decimal:2',
        'payment_date' => 'date',
        'payment_amount' => 'decimal:2',
    ];

    /**
     * Get the project that owns this payment.
     */
    public function project(): Belong
    {
        return $this->belongsTo(Project::class, 'project_id', 'project_id');
    }

    /**
     * Generate a unique payment ID.
     */
    public static function generatePaymentId(): string
    {
        $prefix = 'PAY-';
        $year = date('Y');
        $lastPayment = self::where('payment_id', 'like', $prefix . $year . '%')
            ->orderBy('payment_id', 'desc')
            ->first();

        if ($lastPayment) {
            $lastNumber = (int) substr($lastPayment->payment_id, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . $year . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
}

