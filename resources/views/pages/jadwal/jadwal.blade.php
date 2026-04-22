@extends('pages.index')

@section('admin_content')

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
    .table-custom-header th {
        background: linear-gradient(90deg, #365CF5 0%, #6a8ffd 100%) !important;
        color: white !important;
        font-weight: 600;
        letter-spacing: 0.5px;
        padding: 14px 12px !important;
        border: none !important;
        white-space: nowrap;
    }
    .table tbody tr { transition: background 0.2s; }
    .table tbody tr:nth-child(even) { background: #f4f7ff !important; }
    .table tbody tr:hover { background: #e3eafd !important; }
    .table th, .table td { border: none !important; vertical-align: middle !important; }
    .table { border-collapse: separate !important; border-spacing: 0 !important; font-size: 14px; }
    .dataTables_paginate .paginate_button { background-color: transparent !important; border: none !important; color: #365CF5 !important; }
    .dataTables_paginate .paginate_button:hover { background-color: #e3eafd !important; color: #365CF5 !important; border-radius: 6px !important; }
    .dataTables_paginate .paginate_button.current { background: linear-gradient(90deg, #365CF5, #6a8ffd) !important; color: white !important; border-radius: 6px !important; }
    .btn-tambah-guru { font-weight: bold; background-color: #365CF5; color: white; padding: 8px 16px; display: flex; align-items: center; gap: 8px; transition: all 0.3s ease; border-radius: 8px; }
    .btn-tambah-guru:hover { color: white; background-color: #2346d6; }
    .btn-hapus-guru { font-weight: bold; background-color: transparent; color: #dc3545; border: 2px solid #dc3545; padding: 8px 16px; display: flex; align-items: center; gap: 8px; transition: all 0.3s ease; border-radius: 8px; }
    .btn-hapus-guru:hover { background-color: #dc3545; color: white; }
    .btn-edit-guru { font-weight: bold; background-color: transparent; color: #ffc107; border: 2px solid #ffc107; padding: 8px 16px; display: flex; align-items: center; gap: 8px; transition: all 0.3s ease; border-radius: 8px; }
    .btn-edit-guru:hover { background-color: #ffc107; color: #212529; }
    .day-badge { display: inline-block; padding: 4px 14px; border-radius: 20px; font-weight: 600; font-size: 0.85em; }
    .day-senin    { background:#dbeafe; color:#1d4ed8; }
    .day-selasa   { background:#dcfce7; color:#15803d; }
    .day-rabu     { background:#fef9c3; color:#b45309; }
    .day-kamis    { background:#ede9fe; color:#7c3aed; }
    .day-jumat    { background:#fee2e2; color:#b91c1c; }
    .day-sabtu    { background:#f1f5f9; color:#475569; }
    .day-minggu   { background:#fce7f3; color:#be185d; }
    .summary-card { border-radius: 16px; padding: 18px 24px; color: white; display: flex; align-items: center; gap: 16px; box-shadow: 0 4px 16px rgba(0,0,0,0.10); transition: transform 0.2s; }
    .summary-card:hover { transform: translateY(-3px); }
    .summary-card .icon-wrap { width: 52px; height: 52px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.7rem; }
    .summary-card .label { font-size: 0.85rem; opacity: 0.85; }
    .summary-card .count { font-size: 2rem; font-weight: 800; line-height: 1; }
    @media (max-width: 767.98px) {
        .btn-tambah-guru, .btn-edit-guru, .btn-hapus-guru { font-size: 15px !important; padding: 10px 18px !important; width: 100% !important; }
    }
</style>

<div class="container-fluid">
    <div class="title-wrapper pt-30">
        <div class="row align-items-start">
            <div class="col-md-6">
                <div class="title">
                    <h2 style="font-weight:500;">Jadwal Mapel</h2>
                </div>
            </div>
            <div class="col-md-6">
                <div class="breadcrumb-wrapper">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Jadwal Mapel</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    @php
        $totalJadwal = isset($jadwal) ? count($jadwal) : 0;
        $hariList = isset($jadwal) ? collect($jadwal)->groupBy('day_of_week')->keys()->count() : 0;
        $mapelList = isset($jadwal) ? collect($jadwal)->groupBy('id_subject')->keys()->count() : 0;
        $guruList = isset($jadwal) ? collect($jadwal)->groupBy('id_teacher')->keys()->count() : 0;
    @endphp

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="summary-card" style="background:linear-gradient(135deg,#365CF5,#6a8ffd);">
                <div class="icon-wrap"><i class="fas fa-calendar-alt"></i></div>
                <div><div class="label">Total Jadwal</div><div class="count">{{ $totalJadwal }}</div></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="summary-card" style="background:linear-gradient(135deg,#22c55e,#4ade80);">
                <div class="icon-wrap"><i class="fas fa-calendar-day"></i></div>
                <div><div class="label">Hari Aktif</div><div class="count">{{ $hariList }}</div></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="summary-card" style="background:linear-gradient(135deg,#f59e0b,#fbbf24);">
                <div class="icon-wrap"><i class="fas fa-book"></i></div>
                <div><div class="label">Mata Pelajaran</div><div class="count">{{ $mapelList }}</div></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="summary-card" style="background:linear-gradient(135deg,#8b5cf6,#a78bfa);">
                <div class="icon-wrap"><i class="fas fa-chalkboard-teacher"></i></div>
                <div><div class="label">Guru</div><div class="count">{{ $guruList }}</div></div>
            </div>
        </div>
    </div>

    <!-- Card Wrapper -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0 rounded-top-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center align-items-start py-3 px-4">
            <h5 class="mb-2 mb-md-0 fw-bold text-primary">
                <i class="fas fa-calendar-check me-2"></i> Daftar Jadwal Mapel
            </h5>
            <div class="d-flex gap-2 flex-column flex-md-row w-100 w-md-auto justify-content-md-end mt-2 mt-md-0">
                <button class="btn btn-tambah-guru btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahJadwal">
                    <i class="fas fa-plus"></i> Tambah Jadwal
                </button>
                <button class="btn btn-edit-guru btn-sm" id="btn-edit-guru" type="button">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button class="btn btn-hapus-guru btn-sm" id="btn-hapus-guru" type="button">
                    <i class="fas fa-trash"></i> Hapus
                </button>
            </div>
        </div>
        <div class="card-body px-4 py-3">
            <div class="table-responsive rounded-3">
                <table id="example" class="table table-hover align-middle w-100">
                    <thead class="table-custom-header">
                        <tr>
                            <th class="text-center" style="width:40px;"><input type="checkbox" id="select-all" /></th>
                            <th>Hari</th>
                            <th>Jam Ke</th>
                            <th>Jam Mulai</th>
                            <th>Jam Selesai</th>
                            <th>Kelas</th>
                            <th>Mapel</th>
                            <th>Guru</th>
                            <th class="text-center">QR Code</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($jadwal as $item)
                        @php
                            $dayClass = 'day-' . strtolower($item->day_of_week ?? '');
                        @endphp
                        <tr>
                            <td class="text-center">
                                <input type="checkbox" class="row-checkbox"
                                    data-id="{{ $item->id }}"
                                    data-day_of_week="{{ $item->day_of_week }}"
                                    data-period_start="{{ $item->period_start }}"
                                    data-period_end="{{ $item->period_end }}"
                                    data-start_time="{{ $item->start_time }}"
                                    data-end_time="{{ $item->end_time }}"
                                    data-id_class="{{ $item->class ? $item->class->id : '' }}"
                                    data-id_subject="{{ $item->subject ? $item->subject->id : '' }}"
                                    data-id_teacher="{{ $item->teacher ? $item->teacher->id : '' }}"
                                    data-id_academic_periods="{{ $item->id_academic_periods }}"
                                    data-code="{{ $item->code }}"
                                />
                            </td>
                            <td>
                                <span class="day-badge {{ $dayClass }}">
                                    <i class="fas fa-calendar-day me-1"></i>{{ $item->day_of_week }}
                                </span>
                            </td>
                            <td>
                                <span class="badge rounded-pill px-3 py-2" style="background:#e3eafd;color:#365CF5;font-weight:600;">
                                    {{ $item->period_start }} - {{ $item->period_end }}
                                </span>
                            </td>
                            <td>
                                <span class="fw-semibold text-success"><i class="fas fa-clock me-1"></i>{{ $item->start_time }}</span>
                            </td>
                            <td>
                                <span class="fw-semibold text-danger"><i class="fas fa-clock me-1"></i>{{ $item->end_time }}</span>
                            </td>
                            <td>
                                <span class="badge rounded-pill px-3 py-2" style="background:#f0fdf4;color:#15803d;font-weight:600;">
                                    {{ $item->class ? $item->class->name : '-' }}
                                </span>
                            </td>
                            <td>
                                <span class="fw-semibold text-dark d-block">{{ $item->subject ? $item->subject->name : '-' }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($item->teacher->name ?? '-') }}&background=365CF5&color=fff"
                                        class="rounded-circle" style="width:30px;height:30px;object-fit:cover;">
                                    <span class="fw-semibold text-dark">{{ $item->teacher ? $item->teacher->name : '-' }}</span>
                                </div>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('jadwal.qr', $item->id) }}" class="btn btn-sm rounded-pill px-3" style="background:#e3eafd;color:#365CF5;font-weight:600;">
                                    <i class="fas fa-qrcode me-1"></i> QR
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

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
                                <input type="hidden" name="id_academic_periods" id="edit_id_academic_periods">
                                <div class="form-control border-2 bg-light" style="display: flex; align-items: center;">
                                    <span class="fw-semibold text-dark" id="edit_periode_display">-</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark">Hari</label>
                                <select name="day_of_week" id="edit_day_of_week" class="form-control border-2" required>
                                    <option value="">Pilih Hari</option>
                                    <option value="senin">Senin</option>
                                    <option value="selasa">Selasa</option>
                                    <option value="rabu">Rabu</option>
                                    <option value="kamis">Kamis</option>
                                    <option value="jumat">Jumat</option>
                                    <option value="sabtu">Sabtu</option>
                                    <option value="minggu">Minggu</option>
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
                                <input type="text" name="code" id="edit_code" class="form-control border-2" readonly>
                                <small class="text-muted">Kode akan otomatis berubah jika ada perubahan data.</small>
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
                                        <option value="{{ $gr->id }}" data-subjects="{{ json_encode($gr->subject_ids) }}">{{ $gr->name }}</option>
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
                                <input type="hidden" name="id_academic_periods" value="{{ $periode_aktif->id ?? '' }}">
                                <div class="form-control border-2 bg-light" style="display: flex; align-items: center;">
                                    <span class="fw-semibold text-dark">{{ $periode_aktif->name ?? '-' }}</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark">
                                    Hari
                                </label>
                                <select name="day_of_week" class="form-control border-2" required>
                                    <option value="">Pilih Hari</option>
                                    <option value="senin">Senin</option>
                                    <option value="selasa">Selasa</option>
                                    <option value="rabu">Rabu</option>
                                    <option value="kamis">Kamis</option>
                                    <option value="jumat">Jumat</option>
                                    <option value="sabtu">Sabtu</option>
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
                                <input type="text" name="code" class="form-control border-2" placeholder="Kosongkan Kode !!" value="{{ old('code') ? md5(old('code')) : '' }}" readonly>
                                <small class="text-muted">Biarkan kosong untuk kode otomatis</small>
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
                                <select name="id_teacher" id="tambah_id_teacher" class="form-control border-2" required>
                                    <option value="">Pilih Guru</option>
                                    @foreach($guru as $gr)
                                        <option value="{{ $gr->id }}" data-subjects="{{ json_encode($gr->subject_ids) }}">{{ $gr->name }}</option>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrious/4.0.2/qrious.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#example').DataTable({
                lengthChange: false,
                language: {
                    search: "Cari:",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    emptyTable: "Tidak ada data jadwal.",
                    paginate: { first:"Awal", last:"Akhir", next:"Berikutnya", previous:"Sebelumnya" }
                },
                pageLength: 10
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
                    const periodeAktifId = cb.data('id_academic_periods');
                    const periodeAktifName = getPeriodeNameById(periodeAktifId);
                    $('#edit_id_academic_periods').val(periodeAktifId);
                    $('#edit_periode_display').text(periodeAktifName);
                    // Filter guru berdasarkan mapel yang dipilih
                    filterGuru(cb.data('id_subject'), 'edit_id_teacher');
                    // Set action form
                    $('#formEditJadwal').attr('action', '/pages/jadwal/jadwal/' + cb.data('id'));
                    // Buka modal edit
                    $('#modalEditJadwal').modal('show');
                }
            });

            // Function untuk mendapatkan nama periode berdasarkan ID
            function getPeriodeNameById(periodeId) {
                const periodes = @json($periodes ?? []);
                const periode = periodes.find(p => p.id == periodeId);
                return periode ? `${periode.name}` : '-';
            }

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

         const PERIODS = @json(config('periods.periods'));

        // Filter guru berdasarkan mapel yang dipilih
        function filterGuru(mapelId, guruSelectId) {
            const guruSelect = $(`#${guruSelectId}`);
            const options = guruSelect.find('option');

            if (!mapelId) {
                // Jika tidak ada mapel yang dipilih, sembunyikan semua guru
                options.each(function() {
                    if ($(this).val() !== '') {
                        $(this).hide();
                    }
                });
                guruSelect.val('');
            } else {
                // Filter guru yang mengampu mapel ini
                options.each(function() {
                    if ($(this).val() === '') {
                        $(this).show();
                    } else {
                        const subjects = JSON.parse($(this).attr('data-subjects') || '[]');
                        if (subjects.includes(parseInt(mapelId))) {
                            $(this).show();
                        } else {
                            $(this).hide();
                        }
                    }
                });
            }
        }

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

            // Inisialisasi: sembunyikan guru sampai mapel dipilih
            filterGuru('', 'tambah_id_teacher');

            // Event untuk modal Tambah - filter guru berdasarkan mapel
            $('#formTambahJadwal').find('select[name="id_subject"]').on('change', function() {
                filterGuru($(this).val(), 'tambah_id_teacher');
            });

            // Event untuk modal Edit - filter guru berdasarkan mapel
            $('#formEditJadwal').find('select[name="id_subject"]').on('change', function() {
                filterGuru($(this).val(), 'edit_id_teacher');
            });
        });
    </script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>
@endsection

