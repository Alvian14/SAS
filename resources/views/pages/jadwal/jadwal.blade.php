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
        .btn-tambah-guru {
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
        .btn-tambah-guru:hover {
            color: white;
            background-color: #365CF5;
        }
        .btn-hapus-guru {
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
        .btn-hapus-guru:hover {
            background-color: #dc3545;
            color: white;
        }
        .btn-edit-guru {
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
        .btn-edit-guru:hover {
            background-color: #ffc107;
            color: #212529;
        }
         @media (min-width: 768px) {
            .btn-tambah-guru,
            .btn-edit-guru,
            .btn-hapus-guru {
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
            .btn-tambah-guru,
            .btn-edit-guru,
            .btn-hapus-guru {
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
                    <h2 style="font-weight: 500;">Jadwal Mapel</h2> <!-- Kurangi ketebalan judul -->
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
                                Jadwal Mapel
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
            <h5 class="mb-2 mb-md-0">Daftar Jadwal Mapel</h5>
            <div class="d-flex w-100 w-md-auto justify-content-md-end mt-2 mt-md-0">
                <div class="d-flex gap-2 flex-column flex-md-row w-100 w-md-auto">
                    <button class="btn btn-light btn-sm btn-tambah-guru w-100 w-md-auto d-block d-md-inline-block" style="font-size:14px;padding:7px 14px;" data-bs-toggle="modal" data-bs-target="#modalTambahGuru">
                        <i class="fas fa-plus"></i> Tambah Jadwal
                    </button>
                    <button class="btn btn-edit-guru btn-sm w-100 w-md-auto d-block d-md-inline-block" style="font-size:14px;padding:7px 14px;" id="btn-edit-guru" type="button">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="btn btn-hapus-guru btn-sm w-100 w-md-auto d-block d-md-inline-block" style="font-size:14px;padding:7px 14px;" id="btn-hapus-guru" type="button">
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
                            <th><input type="checkbox" id="select-all" /></th>
                            <th>ID</th>
                            <th>Hari</th>
                            <th>Jam Ke Awal</th>
                            <th>Jam Ke Akhir</th>
                            <th>Jam Mulai</th>
                            <th>Jam Selesai</th>
                            <th>Kode</th>
                            <th>Kelas</th>
                            <th>Mapel</th>
                            <th>Guru</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><input type="checkbox" class="row-checkbox" /></td>
                            <td>1</td>
                            <td>Senin</td>
                            <td>1</td>
                            <td>2</td>
                            <td>07:00</td>
                            <td>08:30</td>
                            <td>MAT01</td>
                            <td>X IPA 1</td>
                            <td>Matematika</td>
                            <td>Pak Budi</td>
                        </tr>
                        <tr>
                            <td><input type="checkbox" class="row-checkbox" /></td>
                            <td>2</td>
                            <td>Selasa</td>
                            <td>3</td>
                            <td>4</td>
                            <td>08:30</td>
                            <td>10:00</td>
                            <td>ING01</td>
                            <td>X IPA 1</td>
                            <td>Bahasa Inggris</td>
                            <td>Bu Sari</td>
                        </tr>
                        <tr>
                            <td><input type="checkbox" class="row-checkbox" /></td>
                            <td>3</td>
                            <td>Rabu</td>
                            <td>1</td>
                            <td>2</td>
                            <td>07:00</td>
                            <td>08:30</td>
                            <td>FIS01</td>
                            <td>X IPA 2</td>
                            <td>Fisika</td>
                            <td>Pak Andi</td>
                        </tr>
                        <tr>
                            <td><input type="checkbox" class="row-checkbox" /></td>
                            <td>4</td>
                            <td>Kamis</td>
                            <td>3</td>
                            <td>4</td>
                            <td>08:30</td>
                            <td>10:00</td>
                            <td>KIM01</td>
                            <td>X IPA 2</td>
                            <td>Kimia</td>
                            <td>Bu Rina</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- End Card Wrapper -->

<!-- Modal Tambah Jadwal -->
<div class="modal fade" id="modalTambahJadwal" tabindex="-1" aria-labelledby="modalTambahJadwalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content shadow-lg border-0 rounded-4">
            <div class="modal-header bg-primary border-0 rounded-top-4">
                <h5 class="modal-title fw-bold text-white" id="modalTambahJadwalLabel" style="color: white;">
                    Tambah Data Jadwal
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formTambahJadwal" method="POST" action="" enctype="multipart/form-data">
                <div class="modal-body p-4 bg-light">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-dark">
                                Hari
                            </label>
                            <input type="text" name="day_of_week" class="form-control border-2" placeholder="Contoh: Senin" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-dark">
                                Jam Ke Awal
                            </label>
                            <input type="number" name="period_start" class="form-control border-2" placeholder="Contoh: 1" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-dark">
                                Jam Ke Akhir
                            </label>
                            <input type="number" name="period_end" class="form-control border-2" placeholder="Contoh: 2" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">
                                Jam Mulai
                            </label>
                            <input type="time" name="start_time" class="form-control border-2" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">
                                Jam Selesai
                            </label>
                            <input type="time" name="end_time" class="form-control border-2" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-dark">
                                Kode Jadwal
                            </label>
                            <input type="text" name="code" class="form-control border-2" placeholder="Kode Jadwal" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-dark">
                                Kelas
                            </label>
                            <input type="text" name="class" class="form-control border-2" placeholder="Nama Kelas" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-dark">
                                Mata Pelajaran
                            </label>
                            <input type="text" name="subject" class="form-control border-2" placeholder="Nama Mapel" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label fw-semibold text-dark">
                                Guru
                            </label>
                            <input type="text" name="teacher" class="form-control border-2" placeholder="Nama Guru" required>
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
            $('#btn-hapus-guru').on('click', function () {
                const checked = $('.row-checkbox:checked').length;
                if (checked === 0) {
                    alert('Pilih data yang ingin dihapus.');
                } else {
                    alert('Menghapus ' + checked + ' data terpilih.');
                }
            });

            // Button edit
            $('#btn-edit-guru').on('click', function () {
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

