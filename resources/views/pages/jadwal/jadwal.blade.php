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
                    <button class="btn btn-light btn-sm btn-tambah-guru w-100 w-md-auto d-block d-md-inline-block" style="font-size:14px;padding:7px 14px;" data-bs-toggle="modal" data-bs-target="#modalTambahJadwal">
                        <i class="fas fa-plus"></i> Tambah Jadwal
                    </button>
                    <button class="btn btn-edit-guru btn-sm w-100 w-md-auto d-block d-md-inline-block" style="font-size:14px;padding:7px 14px;"  type="button" id="btn-edit-guru">
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
                        @foreach($jadwal as $item)
                        <tr>
                            <td>
                                <input type="checkbox" class="row-checkbox"
                                    data-id="{{ $item->id }}"
                                    data-day_of_week="{{ $item->day_of_week }}"
                                    data-period_start="{{ $item->period_start }}"
                                    data-period_end="{{ $item->period_end }}"
                                    data-start_time="{{ $item->start_time }}"
                                    data-end_time="{{ $item->end_time }}"
                                    data-code="{{ $item->code }}"
                                    data-id_class="{{ $item->class ? $item->class->id : '' }}"
                                    data-id_subject="{{ $item->subject ? $item->subject->id : '' }}"
                                    data-id_teacher="{{ $item->teacher ? $item->teacher->id : '' }}"
                                    data-id_academic_periods="{{ $item->id_academic_periods }}"
                                />
                            </td>
                            <td>{{ $item->day_of_week }}</td>
                            <td>{{ $item->period_start }}</td>
                            <td>{{ $item->period_end }}</td>
                            <td>{{ $item->start_time }}</td>
                            <td>{{ $item->end_time }}</td>
                            <td>{{ $item->code }}</td>
                            <td>{{ $item->class ? $item->class->name : '-' }}</td>
                            <td>{{ $item->subject ? $item->subject->name : '-' }}</td>
                            <td>{{ $item->teacher ? $item->teacher->name : '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- End Card Wrapper -->

<!-- Modal Edit Jadwal -->
<div class="modal fade" id="modalEditJadwal" tabindex="-1" aria-labelledby="modalEditJadwalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content shadow-lg border-0 rounded-4">
            <div class="modal-header bg-warning border-0 rounded-top-4">
                <h5 class="modal-title fw-bold text-dark" id="modalEditJadwalLabel">
                    Edit Data Jadwal
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditJadwal" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4 bg-light">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-dark">Periode</label>
                            <select name="id_academic_periods" id="edit_id_academic_periods" class="form-control border-2">
                                <option value="">Pilih Periode</option>
                                @if(isset($periodes))
                                    @foreach($periodes as $prd)
                                        <option value="{{ $prd->id }}">{{ $prd->name }} ({{ $prd->start_date }} - {{ $prd->end_date }})</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-dark">Hari</label>
                            <select name="day_of_week" id="edit_day_of_week" class="form-control border-2" required>
                                <option value="">Pilih Hari</option>
                                <option value="Senin">Senin</option>
                                <option value="Selasa">Selasa</option>
                                <option value="Rabu">Rabu</option>
                                <option value="Kamis">Kamis</option>
                                <option value="Jumat">Jumat</option>
                                <option value="Sabtu">Sabtu</option>
                                <option value="Minggu">Minggu</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-dark">Jam Ke Awal</label>
                            <input type="number" name="period_start" id="edit_period_start" class="form-control border-2" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-dark">Jam Ke Akhir</label>
                            <input type="number" name="period_end" id="edit_period_end" class="form-control border-2" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">Jam Mulai</label>
                            <input type="time" name="start_time" id="edit_start_time" class="form-control border-2" required id="tambah_start_time" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">Jam Selesai</label>
                            <input type="time" name="end_time" id="edit_end_time" class="form-control border-2" required id="tambah_end_time" readonly>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-dark">Kode Jadwal</label>
                            <input type="text" name="code" id="edit_code" class="form-control border-2" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-dark">Kelas</label>
                            <select name="id_class" id="edit_id_class" class="form-control border-2" required>
                                <option value="">Pilih Kelas</option>
                                @foreach($kelas as $kls)
                                    <option value="{{ $kls->id }}">{{ $kls->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-dark">Mata Pelajaran</label>
                            <select name="id_subject" id="edit_id_subject" class="form-control border-2" required>
                                <option value="">Pilih Mapel</option>
                                @foreach($mapel as $mpl)
                                    <option value="{{ $mpl->id }}">{{ $mpl->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label fw-semibold text-dark">Guru</label>
                            <select name="id_teacher" id="edit_id_teacher" class="form-control border-2" required>
                                <option value="">Pilih Guru</option>
                                @foreach($guru as $gr)
                                    <option value="{{ $gr->id }}">{{ $gr->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-white border-0 p-4 gap-2">
                    <button type="button" class="btn btn-danger px-4 fw-semibold shadow-sm" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-warning px-4 fw-semibold shadow-sm">
                        <i class="fas fa-save me-2"></i>Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

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
            <form id="formTambahJadwal" method="POST" action="{{ route('jadwal.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4 bg-light">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-dark">
                                Periode
                            </label>
                            <select name="id_academic_periods" class="form-control border-2">
                                <option value="">Pilih Periode</option>
                                @if(isset($periodes))
                                    @foreach($periodes as $prd)
                                        <option value="{{ $prd->id }}" @if(isset($periode_aktif) && $periode_aktif->id == $prd->id) selected @endif>
                                            {{ $prd->name }} ({{ $prd->start_date }} - {{ $prd->end_date }})
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-dark">
                                Hari
                            </label>
                            <select name="day_of_week" class="form-control border-2" required>
                                <option value="">Pilih Hari</option>
                                <option value="Senin">Senin</option>
                                <option value="Selasa">Selasa</option>
                                <option value="Rabu">Rabu</option>
                                <option value="Kamis">Kamis</option>
                                <option value="Jumat">Jumat</option>
                                <option value="Sabtu">Sabtu</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-dark">
                                Jam Ke Awal
                            </label>
                            <input type="number" name="period_start" class="form-control border-2" placeholder="Contoh: 1" required id="tambah_period_start">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-dark">
                                Jam Ke Akhir
                            </label>
                            <input type="number" name="period_end" class="form-control border-2" placeholder="Contoh: 2" required id="tambah_period_end">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">
                                Jam Mulai
                            </label>
                            <input type="time" name="start_time" class="form-control border-2" required id="tambah_start_time" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">
                                Jam Selesai
                            </label>
                            <input type="time" name="end_time" class="form-control border-2" required id="tambah_end_time" readonly>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-dark">
                                Kode Jadwal
                            </label>
                            <input type="text" name="code" class="form-control border-2" placeholder="Kode Jadwal" required value="JDW{{ date('YmdHis') }}">
                            <small class="text-muted">Kode otomatis, bisa diubah jika perlu.</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-dark">
                                Kelas
                            </label>
                            <select name="id_class" class="form-control border-2" required>
                                <option value="">Pilih Kelas</option>
                                @foreach($kelas as $kls)
                                    <option value="{{ $kls->id }}">{{ $kls->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-dark">
                                Mata Pelajaran
                            </label>
                            <select name="id_subject" class="form-control border-2" required>
                                <option value="">Pilih Mapel</option>
                                @foreach($mapel as $mpl)
                                    <option value="{{ $mpl->id }}">{{ $mpl->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label fw-semibold text-dark">
                                Guru
                            </label>
                            <select name="id_teacher" class="form-control border-2" required>
                                <option value="">Pilih Guru</option>
                                @foreach($guru as $gr)
                                    <option value="{{ $gr->id }}">{{ $gr->name }}</option>
                                @endforeach
                            </select>
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

<!-- Modal Konfirmasi Hapus Jadwal -->
<div class="modal fade" id="modalHapusJadwal" tabindex="-1" aria-labelledby="modalHapusJadwalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content shadow-lg border-0 rounded-4">
            <div class="modal-header bg-danger border-0 rounded-top-4">
                <h5 class="modal-title fw-bold text-white" id="modalHapusJadwalLabel">
                    Konfirmasi Hapus Jadwal
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <p class="fw-semibold text-dark mb-0">Anda yakin ingin menghapus <span id="hapus-count"></span> jadwal terpilih?</p>
            </div>
            <div class="modal-footer bg-white border-0 p-4 gap-2">
                <button type="button" class="btn btn-secondary px-4 fw-semibold shadow-sm" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Batal
                </button>
                <button type="button" class="btn btn-danger px-4 fw-semibold shadow-sm" id="btn-konfirmasi-hapus">
                    <i class="fas fa-trash me-2"></i>Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Form hapus jadwal (hidden) -->
<form id="formHapusJadwal" method="POST" action="{{ route('jadwal.destroy') }}" style="display:none;">
    @csrf
    @method('DELETE')
    <input type="hidden" name="ids" id="hapus_ids">
</form>

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

            $('#select-all').on('click', function () {
                $('.row-checkbox').prop('checked', this.checked);
            });

            // Hapus jadwal menggunakan modal
            let jadwalIdsToDelete = [];
            $('#btn-hapus-guru').on('click', function () {
                const checked = $('.row-checkbox:checked').length;
                if (checked === 0) {
                    alert('Pilih data yang ingin dihapus.');
                } else {
                    jadwalIdsToDelete = [];
                    $('.row-checkbox:checked').each(function() {
                        jadwalIdsToDelete.push($(this).data('id'));
                    });
                    $('#hapus-count').text(checked);
                    $('#modalHapusJadwal').modal('show');
                }
            });

            $('#btn-konfirmasi-hapus').on('click', function () {
                $('#hapus_ids').val(jadwalIdsToDelete.join(','));
                $('#formHapusJadwal').submit();
                $('#modalHapusJadwal').modal('hide');
            });

            $('#btn-edit-guru').on('click', function () {
                const checked = $('.row-checkbox:checked').length;
                if (checked === 0) {
                    alert('Pilih data yang ingin diedit.');
                } else if (checked > 1) {
                    alert('Pilih hanya satu data untuk diedit.');
                } else {
                    // Ambil data dari checkbox terpilih
                    const cb = $('.row-checkbox:checked').first();
                    $('#edit_id').val(cb.data('id'));
                    $('#edit_day_of_week').val(cb.data('day_of_week'));
                    $('#edit_period_start').val(cb.data('period_start'));
                    $('#edit_period_end').val(cb.data('period_end'));
                    $('#edit_start_time').val(cb.data('start_time'));
                    $('#edit_end_time').val(cb.data('end_time'));
                    $('#edit_code').val(cb.data('code'));
                    $('#edit_id_class').val(cb.data('id_class'));
                    $('#edit_id_subject').val(cb.data('id_subject'));
                    $('#edit_id_teacher').val(cb.data('id_teacher'));
                    $('#edit_id_academic_periods').val(cb.data('id_academic_periods'));
                    // Set action form
                    $('#formEditJadwal').attr('action', '/pages/jadwal/jadwal/' + cb.data('id'));
                    // Buka modal edit
                    $('#modalEditJadwal').modal('show');
                }
            });

            // // Otomatis isi jam mulai pada modal tambah jadwal
            // function updateStartTime() {
            //     let periodStart = parseInt($('#tambah_period_start').val());
            //     if (!isNaN(periodStart) && periodStart > 0) {
            //         // Jam ke-1 mulai 07:00, jam ke-2 mulai 08:00, dst
            //         let hour = 7 + (periodStart - 1);
            //         let hourStr = hour.toString().padStart(2, '0');
            //         $('#tambah_start_time').val(hourStr + ':00');
            //     }
            // }
            // $('#tambah_period_start').on('input', updateStartTime);
            // $('#tambah_period_start').on('change', updateStartTime);

            // // Otomatis isi jam selesai pada modal tambah jadwal
            // function updateEndTime() {
            //     let periodEnd = parseInt($('#tambah_period_end').val());
            //     if (!isNaN(periodEnd) && periodEnd > 0) {
            //         let hour = 7 + (periodEnd - 1);
            //         let hourStr = hour.toString().padStart(2, '0');
            //         $('input[name="end_time"]').val(hourStr + ':00');
            //     }
            // }
            // $('#tambah_period_end').on('input', updateEndTime);
            // $('#tambah_period_end').on('change', updateEndTime);

            // ========== Tambahan untuk Modal Edit ==========
            function updateEditStartTime() {
                let periodStart = parseInt($('#edit_period_start').val());
                if (!isNaN(periodStart) && periodStart > 0) {
                    let hour = 7 + (periodStart - 1);
                    let hourStr = hour.toString().padStart(2, '0');
                    $('#edit_start_time').val(hourStr + ':00');
                }
            }
            $('#edit_period_start').on('input', updateEditStartTime);
            $('#edit_period_start').on('change', updateEditStartTime);

            function updateEditEndTime() {
                let periodEnd = parseInt($('#edit_period_end').val());
                if (!isNaN(periodEnd) && periodEnd > 0) {
                    let hour = 7 + (periodEnd - 1);
                    let hourStr = hour.toString().padStart(2, '0');
                    $('#edit_end_time').val(hourStr + ':00');
                }
            }
            $('#edit_period_end').on('input', updateEditEndTime);
            $('#edit_period_end').on('change', updateEditEndTime);
        });
    </script>

    <script>
        // PERIODS mapping from server config; keys are period numbers
        const PERIODS = @json(config('periods.periods'));

        // standalone functions for Tambah (add) modal
        function updateAddStartTime() {
            const start = parseInt($('#tambah_period_start').val());
            if (!start) {
                $('#tambah_start_time').val('');
                return;
            }
            if (!PERIODS[start]) {
                $('#tambah_start_time').val('');
                return;
            }
            $('#tambah_start_time').val(PERIODS[start].start);
        }

        function updateAddEndTime() {
            const end = parseInt($('#tambah_period_end').val());
            if (!end) {
                $('#tambah_end_time').val('');
                return;
            }
            if (!PERIODS[end]) {
                $('#tambah_end_time').val('');
                return;
            }
            $('#tambah_end_time').val(PERIODS[end].end);
        }

        // bind events after DOM ready (only for add functions - edit handlers already exist above)
        $(function(){
            $('#tambah_period_start').on('input change', updateAddStartTime);
            $('#tambah_period_end').on('input change', updateAddEndTime);
        });
    </script>
    
@endsection

