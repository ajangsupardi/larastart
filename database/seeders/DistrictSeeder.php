<?php

namespace Database\Seeders;

use App\Models\District;
use App\Models\Regency;
use App\Services\KodeposParser;
use Illuminate\Database\Seeder;

class DistrictSeeder extends Seeder
{
    public function run(): void
    {
        // Skip if districts already seeded
        if (District::count() > 0) {
            $this->command?->info('Districts already seeded. Skipping.');

            return;
        }

        $parser = app(KodeposParser::class);
        $districtsByRegency = $parser->getDistricts();

        $regencyMap = Regency::pluck('id', 'name')->toArray();
        $total = 0;

        foreach ($districtsByRegency as $regName => $districtNames) {
            if (! isset($regencyMap[$regName])) {
                continue;
            }

            $regencyId = $regencyMap[$regName];

            foreach ($districtNames as $name) {
                District::updateOrCreate(
                    ['name' => $name, 'regency_id' => $regencyId],
                    []
                );
                $total++;
            }
        }

        $this->command?->info('Seeded '.$total.' districts from kodepos.');
    }
}
