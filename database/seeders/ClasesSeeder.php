<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClasesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('clases')->insert([
            [
                'name' => '10 TKJ 1',
                'major' => 'Teknik Komputer dan Jaringan',
                'grade' => 10,
                'code' => 'TKJ',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => '11 DPIB 1',
                'major' => 'Desain Pemodelan dan Informasi Bangunan',
                'grade' => 11,
                'code' => 'DPIB',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => '12 TKP 1',
                'major' => 'Teknik Konstruksi dan Properti',
                'grade' => 12,
                'code' => 'TKP',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
