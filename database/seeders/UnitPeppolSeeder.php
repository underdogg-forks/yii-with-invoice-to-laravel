<?php

namespace Database\Seeders;

use App\Models\UnitPeppol;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitPeppolSeeder extends Seeder
{
    public function run(): void
    {
        // Create Peppol data for all units
        $units = Unit::all();
        
        foreach ($units as $unit) {
            UnitPeppol::factory()->create([
                'unit_id' => $unit->id,
            ]);
        }
    }
}
