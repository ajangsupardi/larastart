<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class DownloadKodepos extends Command
{
    protected $signature = 'app:download-kodepos';

    protected $description = 'Download all kodepos data from Pos Indonesia and save as CSV';

    public function handle(): int
    {
        $this->info('Downloading kodepos data from Pos Indonesia...');

        $response = Http::timeout(60)
            ->connectTimeout(10)
            ->retry(3, 1000)
            ->withHeaders([
                'User-Agent' => 'Mozilla/5.0 (compatible; Larastart/1.0)',
            ])
            ->asForm()
            ->post('https://kodepos.posindonesia.co.id/CariKodepos', [
                'kodepos' => ' ',
            ]);

        if ($response->failed()) {
            $this->error('Failed to download kodepos data.');

            return self::FAILURE;
        }

        $html = $response->body();

        // Parse HTML table
        preg_match_all('/<td>([^<]+)<\/td>/', $html, $matches);
        $cells = $matches[1];

        $rows = [];
        for ($i = 0; $i < count($cells); $i += 6) {
            if ($i + 5 >= count($cells)) {
                break;
            }

            $rows[] = [
                'kodepos' => trim($cells[$i + 1]),
                'desa' => trim($cells[$i + 2]),
                'kecamatan' => trim($cells[$i + 3]),
                'kab' => trim($cells[$i + 4]),
                'provinsi' => trim($cells[$i + 5]),
            ];
        }

        // Save as CSV
        $csvPath = storage_path('app/kodepos/kodepos.csv');
        $dir = dirname($csvPath);

        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $handle = fopen($csvPath, 'w');

        // Header
        fputcsv($handle, ['kodepos', 'desa', 'kecamatan', 'kab', 'provinsi']);

        // Data
        foreach ($rows as $row) {
            fputcsv($handle, $row);
        }

        fclose($handle);

        $this->info("Downloaded and saved {$csvPath}");
        $this->info('Total rows: '.count($rows));
        $this->info('Unique provinsi: '.count(array_unique(array_column($rows, 'provinsi'))));
        $this->info('Unique kab: '.count(array_unique(array_column($rows, 'kab'))));
        $this->info('Unique kecamatan: '.count(array_unique(array_column($rows, 'kecamatan'))));
        $this->info('Unique desa: '.count(array_unique(array_column($rows, 'desa'))));

        return self::SUCCESS;
    }
}
