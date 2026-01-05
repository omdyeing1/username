<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Invoice extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'party_id',
        'invoice_number',
        'invoice_date',
        'subtotal',
        'gst_percent',
        'gst_amount',
        'tds_percent',
        'tds_amount',
        'discount_type',
        'discount_value',
        'discount_amount',
        'final_amount',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'invoice_date' => 'date',
        'subtotal' => 'decimal:2',
        'gst_percent' => 'decimal:2',
        'gst_amount' => 'decimal:2',
        'tds_percent' => 'decimal:2',
        'tds_amount' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'final_amount' => 'decimal:2',
    ];

    /**
     * Discount types.
     */
    public const DISCOUNT_TYPES = [
        'fixed' => 'Fixed Amount',
        'percentage' => 'Percentage',
    ];

    /**
     * Get the party that owns the invoice.
     */
    public function party(): BelongsTo
    {
        return $this->belongsTo(Party::class);
    }

    /**
     * Get the challans included in this invoice.
     */
    public function challans(): BelongsToMany
    {
        return $this->belongsToMany(Challan::class, 'invoice_challans')
                    ->withTimestamps();
    }

    /**
     * Calculate all amounts based on subtotal.
     * 
     * @param float $subtotal The base subtotal amount
     * @param float $gstPercent GST percentage
     * @param float $tdsPercent TDS percentage
     * @param string $discountType 'fixed' or 'percentage'
     * @param float $discountValue Discount value
     * @return array Calculated amounts
     */
    public static function calculateAmounts(
        float $subtotal,
        float $gstPercent = 0,
        float $tdsPercent = 0,
        string $discountType = 'fixed',
        float $discountValue = 0
    ): array {
        // GST calculation
        $gstAmount = round($subtotal * ($gstPercent / 100), 2);
        
        // TDS calculation
        $tdsAmount = round($subtotal * ($tdsPercent / 100), 2);
        
        // Discount calculation
        if ($discountType === 'percentage') {
            $discountAmount = round($subtotal * ($discountValue / 100), 2);
        } else {
            $discountAmount = round(min($discountValue, $subtotal), 2);
        }
        
        // Final amount = Subtotal + GST - TDS - Discount
        $finalAmount = round($subtotal + $gstAmount - $tdsAmount - $discountAmount, 2);
        
        return [
            'subtotal' => $subtotal,
            'gst_amount' => $gstAmount,
            'tds_amount' => $tdsAmount,
            'discount_amount' => $discountAmount,
            'final_amount' => max(0, $finalAmount),
        ];
    }

    /**
     * Generate a unique invoice number.
     */
    public static function generateInvoiceNumber(): string
    {
        $prefix = 'INV';
        $year = date('Y');
        $lastInvoice = static::whereYear('created_at', $year)
                            ->orderBy('id', 'desc')
                            ->first();
        
        if ($lastInvoice) {
            $lastNumber = (int) substr($lastInvoice->invoice_number, -6);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . $year . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Get all items from associated challans.
     */
    public function getAllItems()
    {
        return $this->challans->flatMap(function ($challan) {
            return $challan->items;
        });
    }
}
