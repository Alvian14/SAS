@extends('pages.index')
@section('admin_content')

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
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
        /* Tambahkan jarak antara search box dan tabel */
        div.dataTables_filter {
            margin-bottom: 1rem !important;
            margin-top: 0.5rem !important;
        }
</style>

<div class="container-fluid">
    <!-- ========== title-wrapper start ========== -->
    <div class="title-wrapper pt-30">
        <div class="row align-items-start">
            <div class="col-md-6">
                <div class="title">
                    <h2 style="font-weight: 500;">Absensi Mapel</h2> <!-- Kurangi ketebalan judul -->
                </div>
            </div>
            <div class="col-md-6">
                <div class="breadcrumb-wrapper">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard.index') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('kelas.absensi') }}">kelas</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                Absensi Harian
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
            <h5 class="mb-2 mb-md-0">Absensi Harian</h5>
            <div class="d-flex w-100 w-md-auto justify-content-md-end mt-2 mt-md-0">
                <div class="d-flex gap-2 flex-column flex-md-row w-100 w-md-auto">
                    <!-- Export Button -->
                    <div id="export-container"></div>
                    <button class="btn btn-edit-siswa btn-sm w-100 w-md-auto d-block d-md-inline-block" style="font-size:14px;padding:7px 14px;" id="btn-edit-siswa" type="button">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <!-- Filter Tanggal, Bulan, Tahun -->
            <div class="row mb-3">
                <div class="col-md-4 mb-2">
                    <input type="date" id="filter-tanggal" class="form-control" placeholder="Tanggal">
                </div>
                <div class="col-md-4 mb-2">
                    <select id="filter-bulan" class="form-select">
                        <option value="">-- Pilih Bulan --</option>
                        @php
                            $bulanIndo = [
                                '01' => 'Januari',
                                '02' => 'Februari',
                                '03' => 'Maret',
                                '04' => 'April',
                                '05' => 'Mei',
                                '06' => 'Juni',
                                '07' => 'Juli',
                                '08' => 'Agustus',
                                '09' => 'September',
                                '10' => 'Oktober',
                                '11' => 'November',
                                '12' => 'Desember'
                            ];
                        @endphp
                        @foreach($bulanIndo as $num => $nama)
                            <option value="{{ $num }}">{{ $nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-2">
                    <select id="filter-tahun" class="form-select">
                        <option value="">-- Pilih Tahun --</option>
                        @for($y = date('Y')-5; $y <= date('Y')+1; $y++)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endfor
                    </select>
                </div>
            </div>
            <div class="table-responsive">
                <table id="example" class="table table-hover align-middle">
                    <thead class="table-custom-header">
                        <tr>
                            <th>
                                <input type="checkbox" id="select-all" />
                            </th>
                            <th>Foto</th>
                            <th>Nama</th>
                            <th>Kelas</th>
                            <th>Status</th>
                            <th>Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <input type="checkbox" class="row-checkbox" />
                            </td>
                            <td>
                                <img src="https://ui-avatars.com/api/?name=Tiger+Nixon" alt="Foto" width="36" height="36" class="rounded-circle">
                            </td>
                            <td>System Architect</td>
                            <td>Edinburgh</td>
                            <td>
                                <span class="badge bg-success text-white border border-success fw-bold px-3 py-2" style="font-size:0.95em;">Tepat Waktu</span>
                            </td>
                            <td class="text-success fw-bold">2011-04-25</td>
                        </tr>
                        <tr>
                            <td>
                                <input type="checkbox" class="row-checkbox" />
                            </td>
                            <td>
                                <img src="https://ui-avatars.com/api/?name=Tiger+Nixon" alt="Foto" width="36" height="36" class="rounded-circle">
                            </td>
                            <td>Accountant</td>
                            <td>Tokyo</td>
                            <td>
                                <span class="badge bg-danger text-white border border-danger fw-bold px-3 py-2" style="font-size:0.95em;">Terlambat</span>
                            </td>
                            <td class="text-danger fw-bold">2011-07-25</td>
                        </tr>
                        <tr>
                            <td>
                                <input type="checkbox" class="row-checkbox" />
                            </td>
                            <td>
                                <img src="https://ui-avatars.com/api/?name=Tiger+Nixon" alt="Foto" width="36" height="36" class="rounded-circle">
                            </td>
                            <td>Junior Technical Author</td>
                            <td>San Francisco</td>
                            <td>
                                <span class="badge bg-success text-white border border-success fw-bold px-3 py-2" style="font-size:0.95em;">Tepat Waktu</span>
                            </td>
                            <td class="text-success fw-bold">2009-01-12</td>
                        </tr>
                        <tr>
                            <td>
                                <input type="checkbox" class="row-checkbox" />
                            </td>
                            <td>
                                <img src="https://ui-avatars.com/api/?name=Tiger+Nixon" alt="Foto" width="36" height="36" class="rounded-circle">
                            </td>
                            <td>Junior Technical Author</td>
                            <td>San Francisco</td>
                            <td>
                                <span class="badge bg-success text-white border border-success fw-bold px-3 py-2" style="font-size:0.95em;">Tepat Waktu</span>
                            </td>
                            <td class="text-success fw-bold">2009-01-12</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- End Card Wrapper -->

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.colVis.min.js"></script>
    <script>
        $(document).ready(function () {
            var table = $('#example').DataTable({
                lengthChange: false,
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
                pageLength: 10,
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: '<i class="fas fa-file-excel me-1"></i> Excel',
                        className: 'btn btn-success btn-sm'
                    },
                    {
                        extend: 'pdfHtml5',
                        text: '<i class="fas fa-file-pdf me-1"></i> PDF',
                        className: 'btn btn-danger btn-sm'
                    },
                    {
                        extend: 'csvHtml5',
                        text: '<i class="fas fa-file-csv me-1"></i> CSV',
                        className: 'btn btn-info btn-sm'
                    },

                    {
                        extend: 'print',
                        text: '<i class="fas fa-print me-1"></i> Print',
                        className: 'btn btn-primary btn-sm'
                    }
                ]
            });

            // Pindahkan tombol export ke kiri tombol Edit
            table.buttons().container().appendTo('#export-container');

            // Fungsi untuk memilih semua checkbox
            $('#select-all').on('click', function () {
                $('.row-checkbox').prop('checked', this.checked);
            });


            // Button edit
            $('#btn-edit-siswa').on('click', function () {
                const checked = $('.row-checkbox:checked').length;
                if (checked === 0) {
                    alert('Pilih data yang ingin diedit.');
                } else if (checked > 1) {
                    alert('Pilih hanya satu data untuk diedit.');
                } else {
                    alert('Edit data terpilih.');
                }
            });

            // Filter Tanggal, Bulan, Tahun
            $('#filter-tanggal').on('change', function () {
                let val = $(this).val();
                table.column(5).search(val).draw(); // kolom ke-6 (index 5) asumsikan kolom tanggal
            });
            $('#filter-bulan').on('change', function () {
                let bulan = $(this).val();
                if (bulan) {
                    table.column(5).search('-' + bulan + '-').draw();
                } else {
                    table.column(5).search('').draw();
                }
            });
            $('#filter-tahun').on('change', function () {
                let tahun = $(this).val();
                if (tahun) {
                    table.column(5).search('^' + tahun, true, false).draw();
                } else {
                    table.column(5).search('').draw();
                }
            });
        });
    </script>
@endsection

