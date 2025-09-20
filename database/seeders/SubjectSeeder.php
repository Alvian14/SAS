<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subjects = [
            ['code' => 'MTK',    'name' => 'Matematika',        'type' => 'umum'],
            ['code' => 'SENBUD', 'name' => 'Seni Kebudayaan',   'type' => 'umum'],
            ['code' => 'SEJ',    'name' => 'Sejarah',           'type' => 'umum'],
            ['code' => 'BD',     'name' => 'Bahasa Daerah',     'type' => 'umum'],
            ['code' => 'KIM',    'name' => 'Kimia',             'type' => 'jurusan'],
        ];

        foreach ($subjects as $subject) {
            DB::table('subjects')->insert([
                'code'       => $subject['code'],
                'name'       => $subject['name'],
                'type'       => $subject['type'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
