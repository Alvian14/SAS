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
    .btn-edit-siswa {
        font-weight: bold; background-color: transparent; color: #ffc107;
        border: 2px solid #ffc107; padding: 8px 16px; display: flex;
        align-items: center; gap: 8px; transition: all 0.3s ease; border-radius: 8px;
    }
    .btn-edit-siswa:hover { background-color: #ffc107; color: #212529; }
    .summary-card {
        border-radius: 16px; padding: 18px 24px; color: white;
        display: flex; align-items: center; gap: 16px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.10); transition: transform 0.2s;
    }
    .summary-card:hover { transform: translateY(-3px); }
    .summary-card .icon-wrap {
        width: 52px; height: 52px; background: rgba(255,255,255,0.2);
        border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.7rem;
    }
    .summary-card .label { font-size: 0.85rem; opacity: 0.85; }
    .summary-card .count { font-size: 2rem; font-weight: 800; line-height: 1; }
    .filter-label { font-size: 0.8rem; font-weight: 600; color: #6c757d; margin-bottom: 4px; }
    div.dataTables_filter { margin-bottom: 1rem !important; margin-top: 0.5rem !important; }
    @media (max-width: 767.98px) {
        .btn-edit-siswa { font-size: 15px !important; padding: 10px 18px !important; width: 100% !important; }
    }
</style>

<div class="container-fluid">
    <div class="title-wrapper pt-30">
        <div class="row align-items-start">
            <div class="col-md-6">
                <div class="title">
                    <h2 style="font-weight:500;">
                        Absensi Mapel
                        @if(isset($kelas)) - {{ $kelas->name }} @endif
                    </h2>
                </div>
            </div>
            <div class="col-md-6">
                <div class="breadcrumb-wrapper">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('kelas.absensi') }}">Kelas</a></li>
                            <li class="breadcrumb-item active" aria-current="page">
                                Absensi Mapel @if(isset($kelas)) - {{ $kelas->name }} @endif
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    {{-- @php
        $totalHadir     = isset($absensi) ? $absensi->where('status','tepat_waktu')->count() : 0;
        $totalTerlambat = isset($absensi) ? $absensi->where('status','terlambat')->count() : 0;
        $totalLainnya   = isset($absensi) ? $absensi->whereNotIn('status',['tepat_waktu','terlambat'])->count() : 0;
        $totalSemua     = isset($absensi) ? $absensi->count() : 0;
    @endphp

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="summary-card" style="background:linear-gradient(135deg,#365CF5,#6a8ffd);">
                <div class="icon-wrap"><i class="fas fa-users"></i></div>
                <div><div class="label">Total</div><div class="count">{{ $totalSemua }}</div></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="summary-card" style="background:linear-gradient(135deg,#22c55e,#4ade80);">
                <div class="icon-wrap"><i class="fas fa-check-circle"></i></div>
                <div><div class="label">Hadir</div><div class="count">{{ $totalHadir }}</div></div>
            </div>
        </div>
    </div> --}}

    <!-- Card Wrapper -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0 rounded-top-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center align-items-start py-3 px-4">
            <h5 class="mb-2 mb-md-0 fw-bold text-primary">
                <i class="fas fa-book me-2"></i>
                Absensi Mapel
                @if(isset($kelas))
                    <span class="badge bg-primary ms-2 rounded-pill">{{ $kelas->name }}</span>
                @endif
            </h5>
            <div class="d-flex gap-2 flex-column flex-md-row w-100 w-md-auto justify-content-md-end mt-2 mt-md-0">
                <button class="btn btn-success btn-sm" id="btn-export-excel" type="button" title="Export ke Excel">
                    <i class="fas fa-file-excel me-1"></i> Export Excel
                </button>
                <button class="btn btn-edit-siswa btn-sm" style="font-size:14px;padding:7px 14px;" id="btn-edit-siswa" type="button">
                    <i class="fas fa-edit"></i> Edit
                </button>
            </div>
        </div>
        <div class="card-body px-4 py-3">
            <!-- Info Display -->
            <div class="row mb-4 p-3 bg-light rounded-3" style="border-left: 4px solid #365CF5; background: linear-gradient(135deg, #f8f9fa 0%, #e3eafd 100%);">
                <div class="col-md-4">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-book text-primary" style="font-size: 1.2rem;"></i>
                        <div>
                            <span class="text-muted d-block" style="font-size: 0.75rem; font-weight: 600;">Pelajaran</span>
                            <span class="fw-bold text-dark" id="info-pelajaran" style="font-size: 1rem;">-</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-calendar-alt text-primary" style="font-size: 1.2rem;"></i>
                        <div>
                            <span class="text-muted d-block" style="font-size: 0.75rem; font-weight: 600;">Bulan/Tahun</span>
                            <span class="fw-bold text-dark" id="info-bulan-tahun" style="font-size: 1rem;">{{ now()->timezone('Asia/Jakarta')->format('F Y') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-chalkboard-user text-primary" style="font-size: 1.2rem;"></i>
                        <div>
                            <span class="text-muted d-block" style="font-size: 0.75rem; font-weight: 600;">Kelas</span>
                            <span class="fw-bold text-dark" style="font-size: 1rem;">{{ $kelas->name ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter -->
            <div class="row mb-4 g-2">
                <div class="col-md-3">
                    <div class="filter-label"><i class="fas fa-book me-1"></i> Pelajaran</div>
                    <select id="filter-mapel" class="form-select rounded-3 shadow-sm">
                        <option value="">-- Pilih Pelajaran --</option>
                        @if(isset($mapelList) && count($mapelList))
                            @foreach($mapelList as $mapel)
                                <option value="{{ $mapel->id }}">{{ $mapel->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="filter-label"><i class="fas fa-calendar me-1"></i> Tanggal</div>
                    <input type="date" id="filter-tanggal" class="form-control rounded-3 shadow-sm">
                </div>
                <div class="col-md-3">
                    <div class="filter-label"><i class="fas fa-calendar-alt me-1"></i> Bulan</div>
                    <select id="filter-bulan" class="form-select rounded-3 shadow-sm">
                        <option value="">-- Pilih Bulan --</option>
                        @php
                            $bulanIndo = ['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'];
                        @endphp
                        @foreach($bulanIndo as $num => $nama)
                            <option value="{{ $num }}">{{ $nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="filter-label"><i class="fas fa-calendar-week me-1"></i> Tahun</div>
                    <select id="filter-tahun" class="form-select rounded-3 shadow-sm">
                        <option value="">-- Pilih Tahun --</option>
                        @for($y = date('Y')-5; $y <= date('Y')+1; $y++)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endfor
                    </select>
                </div>
            </div>
            <div class="table-responsive rounded-3">
                <table id="example" class="table table-hover align-middle w-100">
                    <thead class="table-custom-header">
                        <tr>
                            <th class="text-center" style="width:40px;"><input type="checkbox" id="select-all" /></th>
                            <th style="width:50px;">No</th>
                            <th>Nama Siswa</th>
                            <th>NISN</th>
                            <th>Jam Pertemuan</th>
                            <th>Tanggal Absensi</th>
                            <th>Coordinate</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody id="table-body" style="display: none;">
                        @php $rownum = 1; @endphp
                        @if(isset($absensi) && count($absensi))
                            @foreach($absensi as $idx => $item)
                            <tr data-absensi-index="{{ $idx }}">
                                <td class="text-center"><input type="checkbox" class="row-checkbox" /></td>
                                <td class="text-center fw-bold">{{ $rownum }}</td>
                                <td>
                                    <span class="fw-semibold text-dark d-block">{{ $item->student->name ?? '-' }}</span>
                                </td>
                                <td>
                                    <span class="fw-semibold">{{ $item->student->nisn ?? '-' }}</span>
                                </td>
                                 <td class="text-center">
                                    <span class="badge rounded-pill px-3 py-2" style="background:#e3eafd;color:#365CF5;font-weight:600;">
                                        {{ $item->created_at ? $item->created_at->format('H:i') : '-' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge rounded-pill px-3 py-2" style="background:#e3eafd;color:#365CF5;font-weight:600;">
                                        {{ $item->created_at ? $item->created_at->format('d/m/Y') : '-' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($item->coordinate)
                                        <a href="https://www.google.com/maps?q={{ $item->coordinate }}" target="_blank" class="text-decoration-none" title="Buka di Google Maps">
                                            <span class="fw-semibold" style="font-size:0.85rem; color:#365CF5; cursor:pointer;">
                                                <i class="fas fa-map-marker-alt me-1"></i>{{ $item->coordinate }}
                                            </span>
                                        </a>
                                    @else
                                        <span class="fw-semibold text-muted" style="font-size:0.85rem;">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($item->status == 'hadir')
                                        <span class="badge rounded-pill px-3 py-2 fw-bold" style="background:#dcfce7;color:#16a34a;font-size:0.9em;">
                                            <i class="fas fa-check-circle me-1"></i> Hadir
                                        </span>
                                    @elseif($item->status == 'izin')
                                        <span class="badge rounded-pill px-3 py-2 fw-bold" style="background:#fef9c3;color:#eab308;font-size:0.9em;">
                                            <i class="fas fa-envelope me-1"></i> Izin
                                        </span>
                                    @elseif($item->status == 'sakit')
                                        <span class="badge rounded-pill px-3 py-2 fw-bold" style="background:#f0fdf4;color:#22c55e;font-size:0.9em;">
                                            <i class="fas fa-medkit me-1"></i> Sakit
                                        </span>
                                    @elseif($item->status == 'alpha')
                                        <span class="badge rounded-pill px-3 py-2 fw-bold" style="background:#fee2e2;color:#dc2626;font-size:0.9em;">
                                            <i class="fas fa-times-circle me-1"></i> Alpha
                                        </span>
                                    @elseif($item->status == 'dispen')
                                        <span class="badge rounded-pill px-3 py-2 fw-bold" style="background:#e0e7ff;color:#6366f1;font-size:0.9em;">
                                            <i class="fas fa-user-shield me-1"></i> Dispen
                                        </span>
                                    @else
                                        <span class="badge rounded-pill px-3 py-2 fw-bold" style="background:#f3f4f6;color:#6b7280;font-size:0.9em;">
                                            {{ ucfirst($item->status) }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            @php $rownum++; @endphp
                            @endforeach
                        @endif
                        @if(isset($belumAbsen) && count($belumAbsen))
                            @foreach($belumAbsen as $idx => $siswa)
                            <tr data-absensi-index="new_{{ $idx }}">
                                <td class="text-center"><input type="checkbox" class="row-checkbox" /></td>
                                <td class="text-center fw-bold">{{ $rownum }}</td>
                                <td>
                                    <span class="fw-semibold text-dark d-block">{{ $siswa->name }}</span>
                                </td>
                                <td>
                                    <span class="fw-semibold">{{ $siswa->nisn }}</span>
                                </td>
                                <td class="text-center">-</td>
                                <td class="text-center">-</td>
                                <td class="text-center">-</td>
                                <td>
                                    <span class="badge rounded-pill px-3 py-2 fw-bold" style="background:#fff3cd;color:#b45309;font-size:0.9em;">
                                        <i class="fas fa-minus-circle me-1"></i> Belum Absen
                                    </span>
                                </td>
                            </tr>
                            @php $rownum++; @endphp
                            @endforeach
                        @endif
                    </tbody>
                </table>
                <div id="empty-message" class="alert alert-info text-center p-4" role="alert">
                    <i class="fas fa-info-circle me-2"></i> Silahkan pilih pelajaran terlebih dahulu untuk menampilkan data absensi
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function () {
            var table;
            var currentMapelId = null; // Track currently selected mapel

            // Hide table body awalnya
            $('#table-body').hide();
            $('#empty-message').show();

            $('#select-all').on('click', function () {
                $('.row-checkbox').prop('checked', this.checked);
            });

            // Handle filter mapel
            $('#filter-mapel').on('change', function() {
                const mapelText = $(this).find('option:selected').text();
                const mapelId = $(this).val();
                const classId = "{{ $kelas->id }}";
                currentMapelId = mapelId; // Store current mapel id

                if (mapelId === '') {
                    $('#info-pelajaran').text('-');
                    $('#table-body').hide();
                    $('#empty-message').show();
                    // Destroy DataTable jika ada
                    if ($.fn.DataTable.isDataTable('#example')) {
                        $('#example').DataTable().destroy();
                    }
                } else {
                    $('#info-pelajaran').text(mapelText);

                    // Fetch absensi data berdasarkan mapel
                    $.ajax({
                        url: "{{ url('/absensi-mapel/get-by-mapel') }}/" + classId + "/" + mapelId,
                        type: "GET",
                        dataType: "json",
                        success: function(response) {
                            // Update hidden data dengan response
                            window.currentAbsensi = response.absensi;
                            window.currentBelumAbsen = response.belumAbsen;

                            // Clear tbody
                            $('#table-body').html('');

                            let rownum = 1;

                            // Add existing absensi rows
                            if (response.absensi && response.absensi.length > 0) {
                                response.absensi.forEach(function(item, idx) {
                                    let statusBadge = '';
                                    if (item.status === 'hadir') {
                                        statusBadge = '<span class="badge rounded-pill px-3 py-2 fw-bold" style="background:#dcfce7;color:#16a34a;font-size:0.9em;"><i class="fas fa-check-circle me-1"></i> Hadir</span>';
                                    } else if (item.status === 'izin') {
                                        statusBadge = '<span class="badge rounded-pill px-3 py-2 fw-bold" style="background:#fef9c3;color:#eab308;font-size:0.9em;"><i class="fas fa-envelope me-1"></i> Izin</span>';
                                    } else if (item.status === 'sakit') {
                                        statusBadge = '<span class="badge rounded-pill px-3 py-2 fw-bold" style="background:#f0fdf4;color:#22c55e;font-size:0.9em;"><i class="fas fa-medkit me-1"></i> Sakit</span>';
                                    } else if (item.status === 'alpha') {
                                        statusBadge = '<span class="badge rounded-pill px-3 py-2 fw-bold" style="background:#fee2e2;color:#dc2626;font-size:0.9em;"><i class="fas fa-times-circle me-1"></i> Alpha</span>';
                                    } else if (item.status === 'dispen') {
                                        statusBadge = '<span class="badge rounded-pill px-3 py-2 fw-bold" style="background:#e0e7ff;color:#6366f1;font-size:0.9em;"><i class="fas fa-user-shield me-1"></i> Dispen</span>';
                                    } else {
                                        statusBadge = '<span class="badge rounded-pill px-3 py-2 fw-bold" style="background:#f3f4f6;color:#6b7280;font-size:0.9em;">' + (item.status.charAt(0).toUpperCase() + item.status.slice(1)) + '</span>';
                                    }

                                    const attendanceDate = item.created_at ? new Date(item.created_at).toLocaleDateString('id-ID', {day: '2-digit', month: '2-digit', year: 'numeric'}) : '-';
                                    const attendanceTime = item.created_at ? new Date(item.created_at).toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit'}) : '-';

                                    let coordinateCell = '';
                                    if (item.coordinate) {
                                        coordinateCell = `<a href="https://www.google.com/maps?q=${item.coordinate}" target="_blank" class="text-decoration-none" title="Buka di Google Maps"><span class="fw-semibold" style="font-size:0.85rem; color:#365CF5; cursor:pointer;"><i class="fas fa-map-marker-alt me-1"></i>${item.coordinate}</span></a>`;
                                    } else {
                                        coordinateCell = `<span class="fw-semibold text-muted" style="font-size:0.85rem;">-</span>`;
                                    }

                                    const row = `
                                        <tr data-absensi-index="${idx}">
                                            <td class="text-center"><input type="checkbox" class="row-checkbox" /></td>
                                            <td class="text-center fw-bold">${rownum}</td>
                                            <td><span class="fw-semibold text-dark d-block">${item.student.name || '-'}</span></td>
                                            <td><span class="fw-semibold">${item.student.nisn || '-'}</span></td>
                                            <td class="text-center"><span class="badge rounded-pill px-3 py-2" style="background:#e3eafd;color:#365CF5;font-weight:600;">${attendanceDate}</span></td>
                                            <td class="text-center"><span class="badge rounded-pill px-3 py-2" style="background:#e3eafd;color:#365CF5;font-weight:600;">${attendanceTime}</span></td>
                                            <td class="text-center">${coordinateCell}</td>
                                            <td>${statusBadge}</td>
                                        </tr>
                                    `;
                                    $('#table-body').append(row);
                                    rownum++;
                                });
                            }

                            // Add belum absen rows
                            if (response.belumAbsen && response.belumAbsen.length > 0) {
                                response.belumAbsen.forEach(function(siswa, idx) {
                                    const row = `
                                        <tr data-absensi-index="new_${idx}">
                                            <td class="text-center"><input type="checkbox" class="row-checkbox" /></td>
                                            <td class="text-center fw-bold">${rownum}</td>
                                            <td><span class="fw-semibold text-dark d-block">${siswa.name}</span></td>
                                            <td><span class="fw-semibold">${siswa.nisn}</span></td>
                                            <td class="text-center">-</td>
                                            <td class="text-center">-</td>
                                            <td class="text-center">-</td>
                                            <td><span class="badge rounded-pill px-3 py-2 fw-bold" style="background:#fff3cd;color:#b45309;font-size:0.9em;"><i class="fas fa-minus-circle me-1"></i> Belum Absen</span></td>
                                        </tr>
                                    `;
                                    $('#table-body').append(row);
                                    rownum++;
                                });
                            }

                            $('#table-body').show();
                            $('#empty-message').hide();

                            // Reinitialize DataTable
                            if ($.fn.DataTable.isDataTable('#example')) {
                                $('#example').DataTable().destroy();
                            }
                            table = $('#example').DataTable({
                                lengthChange: false,
                                language: {
                                    search: "Cari:",
                                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                                    emptyTable: "Tidak ada data absensi mapel.",
                                    paginate: { first:"Awal", last:"Akhir", next:"Berikutnya", previous:"Sebelumnya" }
                                },
                                pageLength: 10
                            });
                        },
                        error: function() {
                            alert('Gagal mengambil data absensi.');
                            $('#table-body').hide();
                            $('#empty-message').show();
                        }
                    });
                }
            });

            // Handle export button - show modal
            $('#btn-export-excel').on('click', function () {
                $('#modalExportFilter').modal('show');
            });

            // Handle export form submission
            $('#formExportFilter').on('submit', function (e) {
                e.preventDefault();
                const classId = "{{ $kelas->id ?? 0 }}";
                const bulan = $('#export-bulan').val();
                const tahun = $('#export-tahun').val();

                if (!bulan || !tahun) {
                    alert('Pilih bulan dan tahun terlebih dahulu.');
                    return;
                }

                // Build URL and redirect
                const exportUrl = `/absensi-mapel/export-excel/${classId}?bulan=${bulan}&tahun=${tahun}`;
                window.location.href = exportUrl;

                // Close modal
                $('#modalExportFilter').modal('hide');
            });

            // Inisialisasi modal Bootstrap
            var modalEditStatus = new bootstrap.Modal(document.getElementById('modalEditStatus'));

            $('#btn-edit-siswa').on('click', function () {
                const checked = $('.row-checkbox:checked');
                if (checked.length === 0) {
                    alert('Pilih data yang ingin diedit.');
                } else if (checked.length > 1) {
                    alert('Pilih hanya satu data untuk diedit.');
                } else {
                    const row = checked.closest('tr');
                    const absensiIndex = row.data('absensi-index');

                    let absensiId, status, studentId, classId, nama;

                    // Cek apakah ini record baru (belum absen) atau existing
                    if (typeof absensiIndex === 'string' && absensiIndex.startsWith('new_')) {
                        // Data dari belumAbsen
                        const belumAbsenList = window.currentBelumAbsen;
                        const belumAbsenIdx = parseInt(absensiIndex.split('_')[1]);
                        const siswaData = belumAbsenList[belumAbsenIdx];

                        absensiId = 'null';
                        status = 'belum';
                        studentId = siswaData?.id;
                        classId = "{{ $kelas->id }}";
                        nama = siswaData?.name;
                    } else {
                        // Data dari absensi
                        const absensiList = window.currentAbsensi;
                        const absensiData = absensiList[absensiIndex];

                        absensiId = absensiData?.id;
                        status = absensiData?.status;
                        studentId = absensiData?.student?.id;
                        classId = absensiData?.class?.id;
                        nama = absensiData?.student?.name;
                    }

                    // Set values ke hidden inputs
                    $('#editAbsensiId').val(absensiId);
                    $('#editStudentId').val(studentId);
                    $('#editClassId').val(classId);
                    $('#editMapelId').val(currentMapelId);
                    $('#editStatus').val(status);

                    modalEditStatus.show();
                }
            });

            $('#formEditStatus').on('submit', function (e) {
                e.preventDefault();
                const id = $('#editAbsensiId').val();
                const status = $('#editStatus').val();
                const studentId = $('#editStudentId').val();
                const classId = $('#editClassId').val();
                const mapelId = $('#editMapelId').val();

                // Siapkan data
                let data = {
                    status: status,
                    _token: '{{ csrf_token() }}'
                };

                // Jika create baru (id adalah 'null'), tambahkan id_student, id_class, dan id_subject
                if (id === 'null') {
                    data.id_student = studentId;
                    data.id_class = classId;
                    data.id_subject = mapelId;
                }

                $.ajax({
                    url: "{{ url('/absensi-mapel/edit-status') }}/" + id,
                    type: "POST",
                    data: data,
                    success: function (res) {
                        if (res.success) {
                            modalEditStatus.hide();
                            location.reload();
                        } else {
                            alert('Gagal mengupdate status.');
                        }
                    },
                    error: function () {
                        alert('Terjadi kesalahan.');
                    }
                });
            });
        });
    </script>

    <!-- Modal Export Filter -->
    <div class="modal fade" id="modalExportFilter" tabindex="-1" aria-labelledby="modalExportFilterLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="modalExportFilterLabel">
                        <i class="fas fa-file-excel me-2"></i> Export Absensi Mapel
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formExportFilter">
                    <div class="modal-body">
                        <p class="text-muted mb-3">Pilih bulan dan tahun untuk export data absensi mapel</p>
                        <div class="mb-3">
                            <label for="export-bulan" class="form-label">Bulan</label>
                            <select class="form-select" id="export-bulan" name="bulan" required>
                                <option value="">-- Pilih Bulan --</option>
                                @php
                                    $bulanIndo = ['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'];
                                    $currentMonth = now()->timezone('Asia/Jakarta')->format('m');
                                @endphp
                                @foreach($bulanIndo as $num => $nama)
                                    <option value="{{ $num }}" @selected($num === $currentMonth)>{{ $nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="export-tahun" class="form-label">Tahun</label>
                            <select class="form-select" id="export-tahun" name="tahun" required>
                                <option value="">-- Pilih Tahun --</option>
                                @php
                                    $currentYear = now()->timezone('Asia/Jakarta')->format('Y');
                                @endphp
                                @for($y = date('Y')-5; $y <= date('Y')+1; $y++)
                                    <option value="{{ $y }}" @selected($y == $currentYear)>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-download me-1"></i> Export Excel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Status -->
    <div class="modal fade" id="modalEditStatus" tabindex="-1" aria-labelledby="modalEditStatusLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditStatusLabel">Edit Status Absensi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formEditStatus">
                    <div class="modal-body">
                        <input type="hidden" id="editAbsensiId" name="id">
                        <input type="hidden" id="editStudentId" name="id_student">
                        <input type="hidden" id="editClassId" name="id_class">
                        <input type="hidden" id="editMapelId" name="id_subject">
                        <div class="mb-3">
                            <label for="editStatus" class="form-label">Status</label>
                            <select class="form-select" id="editStatus" name="status" required>
                                <option value="hadir">Hadir</option>
                                <option value="izin">Izin</option>
                                <option value="sakit">Sakit</option>
                                <option value="alpha">Alpha</option>
                                <option value="dispen">Dispen</option>
                                <option value="belum">Belum Absen</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

