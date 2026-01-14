<?php

namespace App\Models;

use App\Enums\ReportTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Report extends Model
{
    use HasFactory, SoftDeletes;

    public $timestamps = true;

    protected $casts = [
        'generated_at' => 'datetime',
        'type' => ReportTypeEnum::class,
    ];

    protected $guarded = [];

    #region Static Methods
    /*
    |--------------------------------------------------------------------------
    | Static Methods
    |--------------------------------------------------------------------------
    */

    #endregion

    #region Relationships
    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Get the user who generated this report
     */
    public function generator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    /**
     * Get all parameters for this report
     */
    public function parameters(): HasMany
    {
        return $this->hasMany(ReportParameter::class);
    }

    #endregion

    #region Accessors
    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    #endregion

    #region Mutators
    /*
    |--------------------------------------------------------------------------
    | Mutators
    |--------------------------------------------------------------------------
    */

    #endregion

    #region Scopes
    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Scope: Filter by type
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope: Filter by date range
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('generated_at', [$startDate, $endDate]);
    }

    /**
     * Scope: Recent reports
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('generated_at', '>=', now()->subDays($days));
    }

    #endregion

    #region Custom Methods
    /*
    |--------------------------------------------------------------------------
    | Custom Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Get parameter value
     */
    public function getParameter(string $key, $default = null)
    {
        $param = $this->parameters()->where('key', $key)->first();
        return $param ? $param->value : $default;
    }

    /**
     * Check if report file exists
     */
    public function fileExists(): bool
    {
        return $this->file_path && file_exists(storage_path('app/' . $this->file_path));
    }

    #endregion
}
