<?php

namespace Database\Seeders;

use App\Models\Province;
use App\Models\Regency;
use App\Services\KodeposParser;
use Illuminate\Database\Seeder;

class RegencySeeder extends Seeder
{
    public function run(): void
    {
        // Skip if regencies already seeded
        if (Regency::count() > 0) {
            $this->command?->info('Regencies already seeded. Skipping.');

            return;
        }

        $parser = app(KodeposParser::class);
        $regenciesByProvince = $parser->getRegencies();

        $provinceMap = Province::pluck('id', 'name')->toArray();
        $total = 0;

        foreach ($regenciesByProvince as $provName => $regencyNames) {
            if (! isset($provinceMap[$provName])) {
                continue;
            }

            $provinceId = $provinceMap[$provName];

            foreach ($regencyNames as $name) {
                Regency::updateOrCreate(
                    ['name' => $name, 'province_id' => $provinceId],
                    []
                );
                $total++;
            }
        }

        $this->command?->info('Seeded '.$total.' regencies from kodepos.');
    }
}
