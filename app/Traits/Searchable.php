<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Searchable
{
    /**
     * Get searchable fields.
     */
    public function getSearchableFields(): array
    {
        return $this->searchable ?? [];
    }

    /**
     * Scope for searching.
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        if (empty($term)) {
            return $query;
        }

        $fields = $this->getSearchableFields();

        if (empty($fields)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term, $fields) {
            foreach ($fields as $field) {
                $q->orWhere($field, 'LIKE', "%{$term}%");
            }
        });
    }

    /**
     * Scope for weighted search.
     */
    public function scopeWeightedSearch(Builder $query, string $term): Builder
    {
        if (empty($term)) {
            return $query;
        }

        $fields = $this->getSearchableFields();

        if (empty($fields)) {
            return $query;
        }

        // Build weighted search query
        $selectRaw = '';
        $whereConditions = [];

        foreach ($fields as $index => $field) {
            $weight = count($fields) - $index; // Higher weight for earlier fields
            
            if ($selectRaw !== '') {
                $selectRaw .= ' + ';
            }
            
            $selectRaw .= "CASE WHEN {$field} LIKE ? THEN {$weight} ELSE 0 END";
            $whereConditions[] = "%{$term}%";
        }

        $query->selectRaw("{$this->getTable()}.*, ({$selectRaw}) as relevance", $whereConditions);

        // Add where clause
        $query->where(function (Builder $q) use ($term, $fields) {
            foreach ($fields as $field) {
                $q->orWhere($field, 'LIKE', "%{$term}%");
            }
        });

        return $query->orderBy('relevance', 'desc');
    }

    /**
     * Highlight search terms in text.
     */
    public function highlight(string $text, string $term): string
    {
        if (empty($term)) {
            return $text;
        }

        return preg_replace(
            "/({$term})/i",
            '<mark>$1</mark>',
            $text
        );
    }
}
