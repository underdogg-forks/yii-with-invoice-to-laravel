<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InvoiceGroup extends Model
{
    use HasFactory;

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
        return $this->hasMany(Invoice::class, 'group_id');
    }

    /**
     * Generate next invoice number for this group
     */
    public function generateNextNumber(): string
    {
        $number = str_pad((string) $this->next_id, $this->left_pad, '0', STR_PAD_LEFT);
        
        if ($this->identifier_format) {
            $number = str_replace('{NUMBER}', $number, $this->identifier_format);
            $number = str_replace('{YEAR}', date('Y'), $number);
            $number = str_replace('{MONTH}', date('m'), $number);
        }
        
        $this->increment('next_id');
        
        return $number;
    }
}
