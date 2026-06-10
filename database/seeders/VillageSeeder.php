<?php

namespace Database\Seeders;

use App\Models\District;
use App\Models\Regency;
use App\Models\Village;
use App\Services\KodeposParser;
use Illuminate\Database\Seeder;

class VillageSeeder extends Seeder
{
    public function run(): void
    {
        // Skip if villages already seeded
        if (Village::count() > 0) {
            $this->command?->info('Villages already seeded. Skipping.');

            return;
        }

        $parser = app(KodeposParser::class);
        $villagesByDistrict = $parser->getVillages();

        $regencyMap = Regency::pluck('id', 'name')->toArray();
        $total = 0;

        // Deduplicate by name + district_id
        $seen = [];
        $chunk = [];
        $chunkSize = 1000;

        foreach ($villagesByDistrict as $key => $villageData) {
            $parts = explode('|', $key);
            $regName = $parts[0] ?? '';
            $distName = $parts[1] ?? '';

            if (! isset($regencyMap[$regName])) {
                continue;
            }

            $district = District::where('name', $distName)
                ->where('regency_id', $regencyMap[$regName])
                ->first();

            if (! $district) {
                continue;
            }

            foreach ($villageData as $village) {
                $uniqueKey = $district->id.'|'.$village['name'];

                if (isset($seen[$uniqueKey])) {
                    continue;
                }

                $seen[$uniqueKey] = true;

                $chunk[] = [
                    'district_id' => $district->id,
                    'name' => $village['name'],
                    'postal_code' => $village['postal_code'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $total++;

                if (count($chunk) >= $chunkSize) {
                    Village::insert($chunk);
                    $chunk = [];
                }
            }
        }

        // Insert remaining
        if ($chunk !== []) {
            Village::insert($chunk);
        }

        $this->command?->info('Seeded '.$total.' villages from kodepos.');
    }
}
