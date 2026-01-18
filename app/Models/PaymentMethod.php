<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'code',
        'fee_percentage',
        'fee_fixed',
        'fee_type',
        'description',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'fee_percentage' => 'decimal:2',
        'fee_fixed' => 'decimal:2',
    ];

    /**
     * Calculate fee based on payment amount
     */
    public function calculateFee($amount)
    {
        $fee = 0;

        switch ($this->fee_type) {
            case 'percentage':
                $fee = ($amount * $this->fee_percentage) / 100;
                break;
            case 'fixed':
                $fee = $this->fee_fixed;
                break;
            case 'both':
                $fee = (($amount * $this->fee_percentage) / 100) + $this->fee_fixed;
                break;
        }

        return round($fee, 2);
    }

    /**
     * Get total amount including fee
     */
    public function getTotalWithFee($amount)
    {
        return $amount + $this->calculateFee($amount);
    }
}
