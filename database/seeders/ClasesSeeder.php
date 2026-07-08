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
                'name' => '11 TKJ 1',
                'major' => 'Teknik Komputer dan Jaringan',
                'grade' => 11,
                'code' => 'TKJ',
                'fcm_topic' => 'kelas_11_tkj_1',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => '11 TKJ 2',
                'major' => 'Teknik Komputer dan Jaringan',
                'grade' => 11,
                'code' => 'TKJ',
                'fcm_topic' => 'kelas_11_tkj_2',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => '11 TKJ 3',
                'major' => 'Teknik Komputer dan Jaringan',
                'grade' => 11,
                'code' => 'TKJ',
                'fcm_topic' => 'kelas_11_tkj_3',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
