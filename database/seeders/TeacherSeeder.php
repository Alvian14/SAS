<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Teacher;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cari user guru berdasarkan email

        Teacher::create([
            'name' => 'Alvian hidayatulloh',
            'nip' => '198001012005011001',
            'subject' => 'Matematika',
            'id_user' => 2,
        ]);

        // contoh yang benar
        // Teacher::create([
        //     'name' => 'Pramudya Putra',
        //     'nip' => '198001012005011002',
        //     'subject' => 'MTK, IPA, SENBUD', // multiple subjects as a comma-separated string, and just use codes subjects.
        //     'id_user' => 3,
        // ]);

    }
}
