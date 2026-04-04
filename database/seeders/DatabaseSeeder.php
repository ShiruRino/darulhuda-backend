<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $filePath = base_path('database/seeders/data.xlsx');
        
        // Memuat file excel
        $spreadsheet = IOFactory::load($filePath);
        $dataArray = $spreadsheet->getActiveSheet()->toArray();

        // Loop data (dimulai dari indeks 1 untuk melewati header di baris 0)
        foreach ($dataArray as $index => $row) {
            if ($index == 0) continue; // Lewati header

            // Pastikan baris tidak kosong
            if (!empty($row[0])) {
                DB::table('students')->insert([
                    'nik' => $row[0],
                    'nisn'   => $row[1],
                    'name'   => $row[2],
                    'gender'   => $row[3],
                    'grade'   => $row[4],
                    'admission_year'   => $row[5],
                    'created_at' => now(),
                    'updated_at' => now(),
                    // sesuaikan dengan jumlah kolom Anda
                ]);
            }
        }
        $this->call(InvoiceSeeder::class);
    }
}