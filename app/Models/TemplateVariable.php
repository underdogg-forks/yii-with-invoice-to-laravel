<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplateVariable extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'key',
        'description',
        'type', // string, number, date, array, object
        'default_value',
        'is_required',
        'applicable_to', // JSON array of template types
    ];

    protected $casts = [
        'applicable_to' => 'array',
        'is_required' => 'boolean',
    ];

    /**
     * Check if variable is applicable to a template type
     */
    public function isApplicableTo(string $templateType): bool
    {
        return in_array($templateType, $this->applicable_to ?? []);
    }
}
