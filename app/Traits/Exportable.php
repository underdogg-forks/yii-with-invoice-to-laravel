<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response;

trait Exportable
{
    /**
     * Export to CSV.
     */
    public function exportToCsv(Collection $data, array $columns): string
    {
        $output = fopen('php://temp', 'w');

        // Write header
        fputcsv($output, array_values($columns));

        // Write data
        foreach ($data as $row) {
            $values = [];
            foreach (array_keys($columns) as $key) {
                $values[] = $this->getExportValue($row, $key);
            }
            fputcsv($output, $values);
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }

    /**
     * Export to array.
     */
    public function exportToArray(Collection $data, array $columns): array
    {
        $result = [];

        foreach ($data as $row) {
            $values = [];
            foreach (array_keys($columns) as $key) {
                $values[$columns[$key]] = $this->getExportValue($row, $key);
            }
            $result[] = $values;
        }

        return $result;
    }

    /**
     * Download CSV response.
     */
    public function downloadCsv(Collection $data, array $columns, string $filename): Response
    {
        $csv = $this->exportToCsv($data, $columns);

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Download JSON response.
     */
    public function downloadJson(Collection $data, array $columns, string $filename): Response
    {
        $array = $this->exportToArray($data, $columns);

        return response()->json($array, 200, [
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Get export value from model.
     */
    protected function getExportValue($model, string $key): mixed
    {
        // Handle nested attributes with dot notation
        if (str_contains($key, '.')) {
            $parts = explode('.', $key);
            $value = $model;
            
            foreach ($parts as $part) {
                if (is_object($value)) {
                    $value = $value->{$part} ?? null;
                } elseif (is_array($value)) {
                    $value = $value[$part] ?? null;
                } else {
                    $value = null;
                    break;
                }
            }
            
            return $value;
        }

        return $model->{$key} ?? null;
    }

    /**
     * Get exportable columns.
     */
    public function getExportableColumns(): array
    {
        return $this->exportable ?? [];
    }
}
