<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InvoiceNumbering extends Model
{
    use HasFactory;

    protected $table = 'invoice_numbering';

    protected $fillable = [
        'name',
        'identifier_format',
        'next_id',
        'left_pad',
    ];

    protected $casts = [
        'next_id' => 'integer',
        'left_pad' => 'integer',
    ];

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'numbering_id');
    }

    /**
     * Generate next invoice number for this numbering scheme
     * Uses DB transaction with row-level locking to prevent race conditions
     */
    public function generateNextNumber(): string
    {
        return \DB::transaction(function () {
            // Lock the row for update to prevent concurrent access
            $numbering = self::where('id', $this->id)->lockForUpdate()->first();
            
            // Generate the number using the locked instance
            $number = str_pad((string) $numbering->next_id, $numbering->left_pad, '0', STR_PAD_LEFT);
            
            if ($numbering->identifier_format) {
                $number = str_replace('{NUMBER}', $number, $numbering->identifier_format);
                $number = str_replace('{YEAR}', date('Y'), $number);
                $number = str_replace('{MONTH}', date('m'), $number);
            }
            
            // Increment the next_id within the transaction
            $numbering->increment('next_id');
            
            // Refresh current model instance
            $this->refresh();
            
            return $number;
        });
    }
}
