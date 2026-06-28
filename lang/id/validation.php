<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Baris bahasa berikut digunakan oleh class validator. Beberapa baris
    | memiliki banyak versi seperti rule size. Ubah sesuai kebutuhan aplikasi.
    |
    */

    'accepted' => ':attribute harus diterima.',
    'accepted_if' => ':attribute harus diterima ketika :other adalah :value.',
    'active_url' => ':attribute bukan URL yang valid.',
    'after' => ':attribute harus berisi tanggal setelah :date.',
    'after_or_equal' => ':attribute harus berisi tanggal setelah atau sama dengan :date.',
    'alpha' => ':attribute hanya boleh berisi huruf.',
    'alpha_dash' => ':attribute hanya boleh berisi huruf, angka, strip, dan garis bawah.',
    'alpha_num' => ':attribute hanya boleh berisi huruf dan angka.',
    'array' => ':attribute harus berupa array.',
    'ascii' => ':attribute hanya boleh berisi karakter dan simbol alfanumerik single-byte.',
    'before' => ':attribute harus berisi tanggal sebelum :date.',
    'before_or_equal' => ':attribute harus berisi tanggal sebelum atau sama dengan :date.',
    'between' => [
        'array' => ':attribute harus berisi antara :min sampai :max item.',
        'file' => ':attribute harus berukuran antara :min sampai :max kilobyte.',
        'numeric' => ':attribute harus bernilai antara :min sampai :max.',
        'string' => ':attribute harus berisi antara :min sampai :max karakter.',
    ],
    'boolean' => ':attribute harus bernilai true atau false.',
    'can' => ':attribute berisi nilai yang tidak diizinkan.',
    'confirmed' => 'Konfirmasi :attribute tidak cocok.',
    'contains' => ':attribute kehilangan nilai yang dibutuhkan.',
    'current_password' => 'Password salah.',
    'date' => ':attribute bukan tanggal yang valid.',
    'date_equals' => ':attribute harus berisi tanggal yang sama dengan :date.',
    'date_format' => ':attribute tidak sesuai dengan format :format.',
    'decimal' => ':attribute harus memiliki :decimal angka desimal.',
    'declined' => ':attribute harus ditolak.',
    'declined_if' => ':attribute harus ditolak ketika :other bernilai :value.',
    'different' => ':attribute dan :other harus berbeda.',
    'digits' => ':attribute harus terdiri :digits angka.',
    'digits_between' => ':attribute harus terdiri dari :min sampai :max angka.',
    'dimensions' => ':attribute memiliki dimensi gambar yang tidak valid.',
    'distinct' => ':attribute memiliki nilai yang duplikat.',
    'doesnt_end_with' => ':attribute tidak boleh diakhiri dengan salah satu berikut: :values.',
    'doesnt_start_with' => ':attribute tidak boleh diawali dengan salah satu berikut: :values.',
    'email' => ':attribute harus berupa alamat email yang valid.',
    'ends_with' => ':attribute harus diakhiri salah satu dari berikut: :values.',
    'enum' => ':attribute yang dipilih tidak valid.',
    'exists' => ':attribute yang dipilih tidak valid.',
    'extensions' => ':attribute harus memiliki salah satu ekstensi berikut: :values.',
    'file' => ':attribute harus berupa berkas.',
    'filled' => ':attribute wajib diisi.',
    'gt' => [
        'array' => ':attribute harus berisi lebih dari :value item.',
        'file' => ':attribute harus berukuran lebih dari :value kilobyte.',
        'numeric' => ':attribute harus bernilai lebih dari :value.',
        'string' => ':attribute harus berisi lebih dari :value karakter.',
    ],
    'gte' => [
        'array' => ':attribute harus berisi :value item atau lebih.',
        'file' => ':attribute harus berukuran lebih besar atau sama dengan :value kilobyte.',
        'numeric' => ':attribute harus bernilai lebih besar atau sama dengan :value.',
        'string' => ':attribute harus berisi lebih besar atau sama dengan :value karakter.',
    ],
    'hex_color' => ':attribute harus berupa warna heksadesimal yang valid.',
    'image' => ':attribute harus berupa gambar.',
    'in' => ':attribute yang dipilih tidak valid.',
    'in_array' => ':attribute tidak terdapat dalam :other.',
    'integer' => ':attribute harus berupa bilangan bulat.',
    'ip' => ':attribute harus berupa alamat IP yang valid.',
    'ipv4' => ':attribute harus berupa alamat IPv4 yang valid.',
    'ipv6' => ':attribute harus berupa alamat IPv6 yang valid.',
    'json' => ':attribute harus berupa JSON string yang valid.',
    'list' => ':attribute harus berupa list.',
    'lowercase' => ':attribute harus berupa huruf kecil.',
    'lt' => [
        'array' => ':attribute harus berisi kurang dari :value item.',
        'file' => ':attribute harus berukuran kurang dari :value kilobyte.',
        'numeric' => ':attribute harus bernilai kurang dari :value.',
        'string' => ':attribute harus berisi kurang dari :value karakter.',
    ],
    'lte' => [
        'array' => ':attribute harus berisi tidak lebih dari :value item.',
        'file' => ':attribute harus berukuran kurang dari atau sama dengan :value kilobyte.',
        'numeric' => ':attribute harus bernilai kurang dari atau sama dengan :value.',
        'string' => ':attribute harus berisi kurang dari atau sama dengan :value karakter.',
    ],
    'mac_address' => ':attribute harus berupa MAC address yang valid.',
    'max' => [
        'array' => ':attribute tidak boleh berisi lebih dari :max item.',
        'file' => ':attribute tidak boleh berukuran lebih dari :max kilobyte.',
        'numeric' => ':attribute tidak boleh bernilai lebih dari :max.',
        'string' => ':attribute tidak boleh berisi lebih dari :max karakter.',
    ],
    'max_digits' => ':attribute tidak boleh memiliki lebih dari :max angka.',
    'mimes' => ':attribute harus berupa berkas dengan format: :values.',
    'mimetypes' => ':attribute harus berupa berkas dengan format: :values.',
    'min' => [
        'array' => ':attribute harus berisi minimal :min item.',
        'file' => ':attribute harus berukuran minimal :min kilobyte.',
        'numeric' => ':attribute harus bernilai minimal :min.',
        'string' => ':attribute harus berisi minimal :min karakter.',
    ],
    'min_digits' => ':attribute harus memiliki minimal :min angka.',
    'missing' => ':attribute tidak boleh ada.',
    'missing_if' => ':attribute tidak boleh ada ketika :other bernilai :value.',
    'missing_unless' => ':attribute tidak boleh ada kecuali :other bernilai :value.',
    'missing_with' => ':attribute tidak boleh ada ketika :values ada.',
    'missing_with_all' => ':attribute tidak boleh ada ketika :values ada.',
    'multiple_of' => ':attribute harus kelipatan dari :value.',
    'not_in' => ':attribute yang dipilih tidak valid.',
    'not_regex' => 'Format :attribute tidak valid.',
    'numeric' => ':attribute harus berupa angka.',
    'password' => [
        'letters' => ':attribute harus berisi setidaknya satu huruf.',
        'mixed' => ':attribute harus berisi setidaknya satu huruf besar dan satu huruf kecil.',
        'numbers' => ':attribute harus berisi setidaknya satu angka.',
        'symbols' => ':attribute harus berisi setidaknya satu simbol.',
        'uncompromised' => ':attribute yang dimasukkan telah muncul dalam kebocoran data. Mohon pilih :attribute lain.',
    ],
    'present' => ':attribute wajib ada.',
    'present_if' => ':attribute wajib ada ketika :other bernilai :value.',
    'present_unless' => ':attribute wajib ada kecuali :other bernilai :value.',
    'present_with' => ':attribute wajib ada ketika :values ada.',
    'present_with_all' => ':attribute wajib ada ketika semua :values ada.',
    'prohibited' => ':attribute tidak diperbolehkan.',
    'prohibited_if' => ':attribute tidak diperbolehkan ketika :other bernilai :value.',
    'prohibited_unless' => ':attribute tidak diperbolehkan kecuali :other terdapat dalam :values.',
    'prohibits' => ':attribute melarang :other untuk ada.',
    'regex' => 'Format :attribute tidak valid.',
    'required' => ':attribute wajib diisi.',
    'required_array_keys' => 'Bidang :attribute harus berisi entri untuk: :values.',
    'required_if' => ':attribute wajib diisi ketika :other adalah :value.',
    'required_if_accepted' => ':attribute wajib diisi ketika :other diterima.',
    'required_if_declined' => ':attribute wajib diisi ketika :other ditolak.',
    'required_unless' => ':attribute wajib diisi kecuali :other terdapat dalam :values.',
    'required_with' => ':attribute wajib diisi ketika :values ada.',
    'required_with_all' => ':attribute wajib diisi ketika :values ada.',
    'required_without' => ':attribute wajib diisi ketika :values tidak ada.',
    'required_without_all' => ':attribute wajib diisi ketika tidak ada :values yang tersedia.',
    'same' => ':attribute dan :other harus sama.',
    'size' => [
        'array' => ':attribute harus berisi :size item.',
        'file' => ':attribute harus berukuran :size kilobyte.',
        'numeric' => ':attribute harus bernilai :size.',
        'string' => ':attribute harus berisi :size karakter.',
    ],
    'starts_with' => ':attribute harus diawali salah satu dari berikut: :values.',
    'string' => ':attribute harus berupa string.',
    'timezone' => ':attribute harus berupa zona waktu yang valid.',
    'unique' => ':attribute sudah digunakan.',
    'uploaded' => ':attribute gagal diunggah.',
    'uppercase' => ':attribute harus berupa huruf besar.',
    'url' => ':attribute harus berupa URL yang valid.',
    'ulid' => ':attribute harus berupa ULID yang valid.',
    'uuid' => ':attribute harus berupa UUID yang valid.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Di sini Anda dapat menentukan pesan validasi khusus untuk atribut dengan
    | menggunakan konvensi "attribute.rule" untuk penamaan baris. Hal ini
    | mempermudah penentuan baris bahasa khusus untuk aturan atribut tertentu.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | Baris bahasa berikut digunakan untuk menukar placeholder atribut dengan
    | sesuatu yang lebih ramah pembaca seperti "Alamat E-Mail" alih-alih
    | "email". Hal ini membantu kita membuat pesan menjadi lebih ekspresif.
    |
    */

    'attributes' => [
        'name' => 'Nama',
        'email' => 'Email',
        'password' => 'Password',
        'password_confirmation' => 'Konfirmasi Password',
        'current_password' => 'Password Saat Ini',
        'new_password' => 'Password Baru',
        'new_password_confirmation' => 'Konfirmasi Password Baru',
        'nip' => 'NIP',
        'nisn' => 'NISN',
        'id_class' => 'Kelas',
        'entry_year' => 'Tahun Masuk',
        'subjects' => 'Mata Pelajaran',
        'subjects.*' => 'Mata Pelajaran',
        'profile_picture' => 'Foto Profil',
        'pictures' => 'Foto Webcam',
        'pictures_edit' => 'Foto Webcam',
        'token' => 'Token',
        'form_type' => 'Tipe Form',
        'delete_webcam_photo' => 'Hapus Foto Webcam',
    ],

];
