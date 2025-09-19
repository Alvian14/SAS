@extends('pages.index')
@section('admin_content')

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
        .table-custom-header {
            background-color: #365CF5 !important; /* Biru tua */
            color: white !important;
        }
        .table-custom-footer {
            background-color: #DCE4F7 !important; /* Biru muda lembut */
        }
        .table {
            border: none !important; /* Hilangkan border tabel */
            font-size: 14px; /* Kurangi ukuran teks tabel */
        }
        .table th, .table td {
            border: none !important; /* Hilangkan border untuk sel */
        }
        .dataTables_paginate .paginate_button {
            background-color: transparent !important;
            border: none !important;
            color: #365CF5 !important;
        }
        .dataTables_paginate .paginate_button:hover {
            background-color: white !important;
            color: #365CF5 !important; /* Pastikan teks tetap biru */
            border-radius: 4px !important;
        }
        .dataTables_paginate .paginate_button.current {
            background-color: #365CF5 !important;
            color: white !important;
            border-radius: 4px !important;
        }
        .btn-tambah-siswa {
            font-weight: bold;
            background-color: #365CF5;
            color: white;
            padding: 8px 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.3s ease;
        }
        .btn-tambah-siswa:hover {
            color: white;
            background-color: #365CF5;
        }
        .btn-hapus-siswa {
            font-weight: bold;
            background-color: transparent;
            color: #dc3545;
            border: 2px solid #dc3545;
            padding: 8px 16px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }
        .btn-hapus-siswa:hover {
            background-color: #dc3545;
            color: white;
        }
        .btn-edit-siswa {
            font-weight: bold;
            background-color: transparent;
            color: #ffc107;
            border: 2px solid #ffc107;
            padding: 8px 16px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }
        .btn-edit-siswa:hover {
            background-color: #ffc107;
            color: #212529;
        }
         @media (min-width: 768px) {
            .btn-tambah-siswa,
            .btn-edit-siswa,
            .btn-hapus-siswa {
                font-size: 14px !important;
                padding: 7px 14px !important;
                width: auto !important;
                min-width: 100px;
            }
            .card-header .d-flex.gap-2.flex-column.flex-md-row.w-100.w-md-auto {
                width: auto !important;
            }
        }
        @media (max-width: 767.98px) {
            .btn-tambah-siswa,
            .btn-edit-siswa,
            .btn-hapus-siswa {
                font-size: 15px !important;
                padding: 10px 18px !important;
                width: 100% !important;
            }
        }
</style>

<div class="container-fluid">
    <!-- ========== title-wrapper start ========== -->
    <div class="title-wrapper pt-30">
        <div class="row align-items-start">
            <div class="col-md-6">
                <div class="title">
                    <h2 style="font-weight: 500;">Identitas Siswa</h2> <!-- Kurangi ketebalan judul -->
                </div>
            </div>
            <div class="col-md-6">
                <div class="breadcrumb-wrapper">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard.index') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                Identitas Siswa
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- ========== title-wrapper end ========== -->

    <!-- Card Wrapper -->
    <div class="card shadow-sm">
        <div class="card-header bg-gradient-primary text-white d-flex flex-column flex-md-row justify-content-between align-items-md-center align-items-start">
            <h5 class="mb-2 mb-md-0">Daftar Identitas Siswa</h5>
            <div class="d-flex w-100 w-md-auto justify-content-md-end mt-2 mt-md-0">
                <div class="d-flex gap-2 flex-column flex-md-row w-100 w-md-auto">
                    <button class="btn btn-light btn-sm btn-tambah-siswa w-100 w-md-auto d-block d-md-inline-block" style="font-size:14px;padding:7px 14px;" data-bs-toggle="modal" data-bs-target="#modalTambahSiswa">
                        <i class="fas fa-plus"></i> Tambah Siswa
                    </button>
                    <button class="btn btn-edit-siswa btn-sm w-100 w-md-auto d-block d-md-inline-block" style="font-size:14px;padding:7px 14px;" id="btn-edit-siswa" type="button">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="btn btn-hapus-siswa btn-sm w-100 w-md-auto d-block d-md-inline-block" style="font-size:14px;padding:7px 14px;" id="btn-hapus-siswa" type="button">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="example" class="table table-hover align-middle">
                    <thead class="table-custom-header">
                        <tr>
                            <th>
                                <input type="checkbox" id="select-all" />
                            </th>
                            <th>Nama</th>
                            <th>Jabatan</th>
                            <th>Kantor</th>
                            <th>Usia</th>
                            <th>Tanggal Mulai</th>
                            <th>Gaji</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <input type="checkbox" class="row-checkbox" />
                            </td>
                            <td>Tiger Nixon</td>
                            <td>System Architect</td>
                            <td>Edinburgh</td>
                            <td>61</td>
                            <td>2011-04-25</td>
                            <td>$320,800</td>
                        </tr>
                        <tr>
                            <td>
                                <input type="checkbox" class="row-checkbox" />
                            </td>
                            <td>Garrett Winters</td>
                            <td>Accountant</td>
                            <td>Tokyo</td>
                            <td>63</td>
                            <td>2011-07-25</td>
                            <td>$170,750</td>
                        </tr>
                        <tr>
                            <td>
                                <input type="checkbox" class="row-checkbox" />
                            </td>
                            <td>Ashton Cox</td>
                            <td>Junior Technical Author</td>
                            <td>San Francisco</td>
                            <td>66</td>
                            <td>2009-01-12</td>
                            <td>$86,000</td>
                        </tr>
                        <tr>
                            <td>
                                <input type="checkbox" class="row-checkbox" />
                            </td>
                            <td>Ashton Cox</td>
                            <td>Junior Technical Author</td>
                            <td>San Francisco</td>
                            <td>66</td>
                            <td>2009-01-12</td>
                            <td>$86,000</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- End Card Wrapper -->

<!-- Modal Tambah Siswa -->
<div class="modal fade" id="modalTambahSiswa" tabindex="-1" aria-labelledby="modalTambahSiswaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content shadow-lg border-0 rounded-4">
            <div class="modal-header bg-primary border-0 rounded-top-4">
                <h5 class="modal-title fw-bold text-white" id="modalTambahSiswaLabel" style="color: white;">
                    Tambah Data Siswa
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formTambahSiswa" method="POST" action="" enctype="multipart/form-data">
                <div class="modal-body p-4 bg-light">
                    <!-- Baris 1: Nama Lengkap dan Email -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">
                                Nama Lengkap
                            </label>
                            <input type="text" name="name" class="form-control border-2" placeholder="Masukkan nama lengkap" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">
                                Email
                            </label>
                            <input type="email" name="email" class="form-control border-2" placeholder="contoh@email.com" required>
                        </div>
                    </div>

                    <!-- Baris 2: Password dan NISN -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">
                                Password
                            </label>
                            <input type="password" name="password" class="form-control border-2" placeholder="Masukkan password" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">
                                NISN
                            </label>
                            <input type="number" name="nisn" class="form-control border-2" placeholder="Nomor Induk Siswa" required>
                        </div>
                    </div>

                    <!-- Baris 3: Kelas dan Tahun Masuk -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">
                                Kelas
                            </label>
                            <select name="id_class" class="form-select border-2" required>
                                <option value="">-- Pilih Kelas --</option>
                                <option value="1">TKR 1</option>
                                <option value="2">TKR 2</option>
                                <option value="3">TBSM 1</option>
                                <option value="4">TBSM 2</option>
                                <option value="5">TKJ 1</option>
                                <option value="6">TKJ 2</option>
                                <option value="7">TAV 1</option>
                                <option value="8">TAV 2</option>
                                <option value="9">PS 1</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">
                                Tahun Masuk
                            </label>
                            <input type="number" name="entry_year" class="form-control border-2" placeholder="2024" min="2020" max="2030" required>
                        </div>
                    </div>

                    <!-- Baris 4: Foto Profil (Full Width) -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold text-dark">
                                Foto Profil
                                <small class="text-muted">(opsional)</small>
                            </label>
                            <input type="file" name="profile_picture" class="form-control border-2" accept="image/*">
                            <div class="form-text text-muted">
                                Format: JPG, PNG, GIF. Maksimal 2MB.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-white border-0 p-4 gap-2">
                    <button type="button" class="btn btn-danger px-4 fw-semibold shadow-sm" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-primary px-4 fw-semibold shadow-sm">
                        <i class="fas fa-save me-2"></i>Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#example').DataTable({
                lengthChange: false, // Nonaktifkan "Show entries"
                language: {
                    search: "Cari:",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    paginate: {
                        first: "Awal",
                        last: "Akhir",
                        next: "Berikutnya",
                        previous: "Sebelumnya"
                    }
                },
                pageLength: 10 // Jumlah data default per halaman
            });

            // Fungsi untuk memilih semua checkbox
            $('#select-all').on('click', function () {
                $('.row-checkbox').prop('checked', this.checked);
            });

            // Button hapus
            $('#btn-hapus-siswa').on('click', function () {
                const checked = $('.row-checkbox:checked').length;
                if (checked === 0) {
                    alert('Pilih data yang ingin dihapus.');
                } else {
                    alert('Menghapus ' + checked + ' data terpilih.');
                }
            });

            // Button edit
            $('#btn-edit-siswa').on('click', function () {
                const checked = $('.row-checkbox:checked').length;
                if (checked === 0) {
                    alert('Pilih data yang ingin diedit.');
                } else if (checked > 1) {
                    alert('Pilih hanya satu data untuk diedit.');
                } else {
                    // Lakukan aksi edit di sini (misal: buka modal edit, dsb)
                    alert('Edit data terpilih.');
                }
            });
        });
    </script>
@endsection

