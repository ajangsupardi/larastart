<?php

namespace Database\Seeders;

use App\Models\Province;
use App\Services\KodeposParser;
use Illuminate\Database\Seeder;

class ProvinceSeeder extends Seeder
{
    public function run(): void
    {
        // Skip if provinces already seeded
        if (Province::count() > 0) {
            $this->command?->info('Provinces already seeded. Skipping.');

            return;
        }

        $parser = app(KodeposParser::class);
        $provinces = $parser->getProvinces();

        $isoMap = [
            'Aceh' => 'AC', 'Bali' => 'BA', 'Banten' => 'BT', 'Sumatera Utara' => 'SU',
            'Sumatera Barat' => 'SB', 'Riau' => 'RI', 'Jambi' => 'JA', 'Sumatera Selatan' => 'SS',
            'Bengkulu' => 'BE', 'Lampung' => 'LA', 'Kepulauan Bangka Belitung' => 'BB',
            'Kepulauan Riau' => 'KR', 'DKI Jakarta' => 'JK', 'Jawa Barat' => 'JB',
            'Jawa Tengah' => 'JT', 'DI Yogyakarta' => 'YO', 'Jawa Timur' => 'JI',
            'Nusa Tenggara Barat' => 'NB', 'Nusa Tenggara Timur' => 'NT',
            'Kalimantan Barat' => 'KB', 'Kalimantan Tengah' => 'KT', 'Kalimantan Selatan' => 'KS',
            'Kalimantan Timur' => 'KI', 'Kalimantan Utara' => 'KU', 'Sulawesi Utara' => 'SA',
            'Sulawesi Tengah' => 'ST', 'Sulawesi Selatan' => 'SN', 'Sulawesi Tenggara' => 'SG',
            'Gorontalo' => 'GO', 'Sulawesi Barat' => 'SR', 'Maluku' => 'MA', 'Maluku Utara' => 'MU',
            'Papua Barat' => 'PB', 'Papua' => 'PA', 'Papua Selatan' => 'PS', 'Papua Tengah' => 'PT',
            'Papua Pegunungan' => 'PP', 'Papua Barat Daya' => 'PD',
        ];

        foreach ($provinces as $name) {
            $code = $isoMap[$name] ?? strtoupper(substr($name, 0, 2));

            Province::updateOrCreate(
                ['code' => $code],
                ['name' => $name]
            );
        }

        $this->command?->info('Seeded '.count($provinces).' provinces from kodepos.');
    }
}
