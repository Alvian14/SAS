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
    .table tbody tr {
        transition: background 0.2s;
    }
    .table tbody tr:nth-child(even) {
        background: #f4f7ff !important;
    }
    .table tbody tr:hover {
        background: #e3eafd !important;
    }
    .table th, .table td {
        border: none !important;
        vertical-align: middle !important;
    }
    .table {
        border-collapse: separate !important;
        border-spacing: 0 0 !important;
        font-size: 14px;
    }
    .dataTables_paginate .paginate_button {
        background-color: transparent !important;
        border: none !important;
        color: #365CF5 !important;
    }
    .dataTables_paginate .paginate_button:hover {
        background-color: #e3eafd !important;
        color: #365CF5 !important;
        border-radius: 6px !important;
    }
    .dataTables_paginate .paginate_button.current {
        background: linear-gradient(90deg, #365CF5, #6a8ffd) !important;
        color: white !important;
        border-radius: 6px !important;
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
        border-radius: 8px;
    }
    .btn-edit-siswa:hover {
        background-color: #ffc107;
        color: #212529;
    }
    .summary-card {
        border-radius: 16px;
        padding: 18px 24px;
        color: white;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.10);
        transition: transform 0.2s;
    }
    .summary-card:hover { transform: translateY(-3px); }
    .summary-card .icon-wrap {
        width: 52px; height: 52px;
        background: rgba(255,255,255,0.2);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.7rem;
    }
    .summary-card .label { font-size: 0.85rem; opacity: 0.85; }
    .summary-card .count { font-size: 2rem; font-weight: 800; line-height: 1; }
    .filter-label {
        font-size: 0.8rem;
        font-weight: 600;
        color: #6c757d;
        margin-bottom: 4px;
    }
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
                        Absensi Harian
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
                                Absensi Harian @if(isset($kelas)) - {{ $kelas->name }} @endif
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    @php
        $totalHadir   = isset($absensi) ? $absensi->where('status','tepat_waktu')->count() : 0;
        $totalTerlambat = isset($absensi) ? $absensi->where('status','terlambat')->count() : 0;
        $totalLainnya = isset($absensi) ? $absensi->whereNotIn('status',['tepat_waktu','terlambat'])->count() : 0;
        $totalSemua   = isset($absensi) ? $absensi->count() : 0;
    @endphp

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="summary-card" style="background: linear-gradient(135deg,#365CF5,#6a8ffd);">
                <div class="icon-wrap"><i class="fas fa-users"></i></div>
                <div>
                    <div class="label">Total</div>
                    <div class="count">{{ $totalSemua }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="summary-card" style="background: linear-gradient(135deg,#22c55e,#4ade80);">
                <div class="icon-wrap"><i class="fas fa-check-circle"></i></div>
                <div>
                    <div class="label">Tepat Waktu</div>
                    <div class="count">{{ $totalHadir }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="summary-card" style="background: linear-gradient(135deg,#ef4444,#f87171);">
                <div class="icon-wrap"><i class="fas fa-clock"></i></div>
                <div>
                    <div class="label">Terlambat</div>
                    <div class="count">{{ $totalTerlambat }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="summary-card" style="background: linear-gradient(135deg,#f59e0b,#fbbf24);">
                <div class="icon-wrap"><i class="fas fa-question-circle"></i></div>
                <div>
                    <div class="label">Lainnya</div>
                    <div class="count">{{ $totalLainnya }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card Wrapper -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0 rounded-top-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center align-items-start py-3 px-4">
            <h5 class="mb-2 mb-md-0 fw-bold text-primary">
                <i class="fas fa-calendar-check me-2"></i>
                Absensi Harian
                @if(isset($kelas))
                    <span class="badge bg-primary ms-2 rounded-pill">{{ $kelas->name }}</span>
                @endif
            </h5>
            <div class="d-flex gap-2 flex-column flex-md-row w-100 w-md-auto justify-content-md-end mt-2 mt-md-0">
                <form id="exportForm" method="GET" style="display:inline;">
                    <button type="submit" class="btn btn-success btn-sm" title="Export ke Excel menggunakan PhpSpreadsheet">
                        <i class="fas fa-file-excel me-1"></i> Export Excel
                    </button>
                </form>
                <button class="btn btn-edit-siswa btn-sm" style="font-size:14px;padding:7px 14px;" id="btn-edit-siswa" type="button">
                    <i class="fas fa-edit"></i> Edit
                </button>
            </div>
        </div>
        <div class="card-body px-4 py-3">
            <!-- Filter -->
            <div class="row mb-4 g-2">
                <div class="col-md-4">
                    <div class="filter-label"><i class="fas fa-calendar me-1"></i> Tanggal</div>
                    <input type="date" id="filter-tanggal" class="form-control rounded-3 shadow-sm" value="{{ $today ?? '' }}">
                </div>
                <div class="col-md-4">
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
                <div class="col-md-4">
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
                            <th class="text-center" style="width:40px;border-radius:12px 0 0 0;">
                                <input type="checkbox" id="select-all" />
                            </th>
                            <th class="text-center" style="width:60px;">Foto</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Status</th>
                            <th style="border-radius:0 12px 0 0;">Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($absensi) && count($absensi))
                            @foreach($absensi as $idx => $item)
                            <tr data-absensi-index="{{ $idx }}">
                                <td class="text-center">
                                    <input type="checkbox" class="row-checkbox" />
                                </td>
                                <td class="text-center">
                                    <img src="{{ $item->picture ?? 'https://ui-avatars.com/api/?name=' . urlencode($item->student->name ?? '-') . '&background=365CF5&color=fff' }}"
                                        alt="Foto" class="rounded-circle shadow-sm border border-2 border-primary"
                                        style="width:42px;height:42px;object-fit:cover;">
                                </td>
                                <td>
                                    <span class="fw-semibold text-dark d-block">{{ $item->student->name ?? '-' }}</span>
                                    <span class="text-muted" style="font-size:0.8rem;">NISN: {{ $item->student->nisn ?? '-' }}</span>
                                </td>
                                <td>
                                    <span class="badge rounded-pill px-3 py-2" style="background:#e3eafd;color:#365CF5;font-weight:600;">
                                        {{ $item->class->name ?? '-' }}
                                    </span>
                                </td>
                                <td>
                                    @if($item->status == 'tepat_waktu')
                                        <span class="badge rounded-pill px-3 py-2 fw-bold" style="background:#dcfce7;color:#16a34a;font-size:0.9em;">
                                            <i class="fas fa-check-circle me-1"></i> Tepat Waktu
                                        </span>
                                    @elseif($item->status == 'terlambat')
                                        <span class="badge rounded-pill px-3 py-2 fw-bold" style="background:#fee2e2;color:#dc2626;font-size:0.9em;">
                                            <i class="fas fa-clock me-1"></i> Terlambat
                                        </span>
                                    @elseif($item->status == 'in progress')
                                        <span class="badge rounded-pill px-3 py-2 fw-bold" style="background:#f3f4f6;color:#6b7280;font-size:0.9em;">
                                            In Progress
                                        </span>
                                    @else
                                        <span class="badge rounded-pill px-3 py-2 fw-bold" style="background:#f3f4f6;color:#6b7280;font-size:0.9em;">
                                            {{ ucfirst($item->status) }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($item->status == 'in progress')
                                        <span class="text-muted">-</span>
                                    @elseif($item->created_at)
                                        @php
                                            $bulanIndo = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                                            $carbon = \Carbon\Carbon::parse($item->created_at);
                                            $bulanName = $bulanIndo[$carbon->month - 1];
                                            $formatted = $carbon->format('d') . ' ' . $bulanName . ' ' . $carbon->format('Y, H:i');
                                        @endphp
                                        <span class="fw-semibold {{ $item->status == 'tepat_waktu' ? 'text-success' : 'text-danger' }}">
                                            <i class="fas fa-clock me-1"></i>
                                            {{ $formatted }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- End Card Wrapper -->

    <!-- Modal Edit Status -->
    <div class="modal fade" id="modalEditStatus" tabindex="-1" aria-labelledby="modalEditStatusLabel" aria-hidden="true">
      <div class="modal-dialog">
        <form id="formEditStatus">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="modalEditStatusLabel">Edit Status Absensi</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <input type="hidden" id="editAbsensiId" name="id">
              <input type="hidden" id="editStudentId" name="id_student">
              <input type="hidden" id="editClassId" name="id_class">
              <div class="mb-3">
                <label for="editStatus" class="form-label">Status</label>
                <select class="form-select" id="editStatus" name="status" required>
                  <option value="in progress">In Progress</option>
                  <option value="tepat_waktu">Tepat Waktu</option>
                  <option value="terlambat">Terlambat</option>
                  <option value="izin">Izin</option>
                </select>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
              <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function () {
            var table = $('#example').DataTable({
                lengthChange: false,

                language: {
                    search: "Cari:",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    emptyTable: "Tidak ada data absensi harian.",
                    paginate: { first:"Awal", last:"Akhir", next:"Berikutnya", previous:"Sebelumnya" }
                },
                pageLength: 10
            });

            // Auto-filter untuk tanggal hari ini saat page load
            let todayDate = "{{ $today }}";
            if (todayDate) {
                let parts = todayDate.split('-');
                let day = parts[2];
                let month = parts[1];
                let year = parts[0];
                let bulanNames = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                let monthName = bulanNames[parseInt(month)];
                let searchStr = day + ' ' + monthName + ' ' + year;
                table.column(5).search(searchStr).draw();
            }

            $('#select-all').on('click', function () {
                $('.row-checkbox').prop('checked', this.checked);
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
                    const absensiList = @json($absensi);
                    const absensiData = absensiList[absensiIndex];
                    const absensiId = absensiData?.id;
                    const status = absensiData?.status;
                    const studentId = absensiData?.student?.id;
                    const classId = absensiData?.class?.id;

                    // Set values ke hidden inputs
                    $('#editAbsensiId').val(absensiId || 'null');
                    $('#editStudentId').val(studentId);
                    $('#editClassId').val(classId);
                    $('#editStatus').val(status);

                    modalEditStatus.show(); // Tampilkan modal
                }
            });

            $('#formEditStatus').on('submit', function(e) {
                e.preventDefault();
                const id = $('#editAbsensiId').val();
                const status = $('#editStatus').val();
                const studentId = $('#editStudentId').val();
                const classId = $('#editClassId').val();

                // Siapkan data
                let data = {
                    status: status,
                    _token: "{{ csrf_token() }}"
                };

                // Jika create baru (id adalah 'null'), tambahkan id_student dan id_class
                if (id === 'null') {
                    data.id_student = studentId;
                    data.id_class = classId;
                }

                $.ajax({
                    url: "{{ url('/pages/absensi/absensi_harian/edit-status') }}/" + id,
                    type: "POST",
                    data: data,
                    success: function(res) {
                        if(res.success) {
                            alert('Berhasil: ' + res.message);
                            modalEditStatus.hide();
                            location.reload();
                        } else {
                            alert('Gagal: ' + (res.message || 'Gagal mengupdate status.'));
                        }
                    },
                    error: function(xhr) {
                        let errorMsg = 'Terjadi kesalahan.';

                        // Jika ada error dari validasi
                        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        } else if (xhr.status === 404) {
                            errorMsg = 'Route tidak ditemukan';
                        } else if (xhr.status === 500) {
                            errorMsg = 'Error server: ' + (xhr.responseJSON?.message || 'Internal server error');
                        }

                        alert('Error: ' + errorMsg);
                    }
                });
            });

            // Custom filter untuk tanggal
            $('#filter-tanggal').on('change', function () {
                let selectedDate = $(this).val(); // format: YYYY-MM-DD
                if (selectedDate) {
                    let parts = selectedDate.split('-');
                    let day = parts[2];
                    let month = parts[1];
                    let year = parts[0];

                    // Konversi bulan ke nama bulan Indonesia
                    let bulanNames = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                    let monthName = bulanNames[parseInt(month)];

                    // Format pencarian: "DD BulanIndo YYYY"
                    let searchStr = day + ' ' + monthName + ' ' + year;
                    table.column(5).search(searchStr, false, false).draw();

                    // Clear month/year filters when date is selected
                    $('#filter-bulan').val('');
                    $('#filter-tahun').val('');
                } else {
                    table.column(5).search('').draw();
                }
            });

            // Custom filter untuk tanggal dan bulan/tahun
            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                let tanggal = $('#filter-tanggal').val();
                let bulan = $('#filter-bulan').val();
                let tahun = $('#filter-tahun').val();

                let waktuText = data[5]; // Kolom Waktu (index 5)

                // Jika tanggal dipilih, filter by tanggal
                if (tanggal) {
                    let parts = tanggal.split('-');
                    let day = parts[2];
                    let month = parts[1];
                    let year = parts[0];

                    let bulanNames = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                    let monthName = bulanNames[parseInt(month)];
                    let searchStr = day + ' ' + monthName + ' ' + year;

                    // Tampilkan dummy records (waktu === '-')
                    if (!waktuText || waktuText.trim() === '' || waktuText === '-' || waktuText.match(/^-\s*$/)) {
                        return true;
                    }

                    // Untuk real records, match tanggalnya
                    return waktuText.includes(searchStr);
                }

                // Jika bulan dan/atau tahun dipilih
                if (!bulan && !tahun) return true;

                // Tampilkan dummy records (waktu === '-') untuk semua filter bulan/tahun
                if (!waktuText || waktuText.trim() === '' || waktuText === '-' || waktuText.match(/^-\s*$/)) {
                    return true;  // Keep dummy records visible
                }

                // Parse format: "22 Mei 2026, 22:19" (Bahasa Indonesia) untuk real records
                let bulanNames = {
                    'Januari': '01', 'Februari': '02', 'Maret': '03', 'April': '04',
                    'Mei': '05', 'Juni': '06', 'Juli': '07', 'Agustus': '08',
                    'September': '09', 'Oktober': '10', 'November': '11', 'Desember': '12'
                };

                let dateMatch = waktuText.match(/(\d{1,2})\s+(\w+)\s+(\d{4})/);
                if (!dateMatch) return true;  // Jika tidak bisa parse, tampilkan

                let day = dateMatch[1];
                let monthName = dateMatch[2];
                let year = dateMatch[3];
                let monthNum = bulanNames[monthName];

                let match = true;

                if (bulan) {
                    match = match && (monthNum === bulan);
                }

                if (tahun) {
                    match = match && (year === tahun);
                }

                return match;
            });

            $('#filter-bulan').on('change', function () {
                // Clear tanggal filter when month is selected
                $('#filter-tanggal').val("{{ $today }}");
                table.draw();
            });

            $('#filter-tahun').on('change', function () {
                // Clear tanggal filter when year is selected
                $('#filter-tanggal').val("{{ $today }}");
                table.draw();
            });

            // Handle export form
            $('#exportForm').on('submit', function(e) {
                e.preventDefault();

                const classId = "{{ $kelas->id ?? 0 }}";
                const tanggal = $('#filter-tanggal').val() || '';
                const bulan = $('#filter-bulan').val() || '';
                const tahun = $('#filter-tahun').val() || '';

                // Build query string
                let params = new URLSearchParams();
                if (tanggal) params.append('tanggal', tanggal);
                if (bulan) params.append('bulan', bulan);
                if (tahun) params.append('tahun', tahun);

                // Redirect to export endpoint
                const exportUrl = `/pages/absensi/absensi_harian/export-excel/${classId}?${params.toString()}`;
                window.location.href = exportUrl;
            });
        });
    </script>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @endpush
@endsection

