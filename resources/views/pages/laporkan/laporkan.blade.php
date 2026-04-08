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
                    <h2 style="font-weight:500;">Laporkan</h2>
                </div>
            </div>
            <div class="col-md-6">
                <div class="breadcrumb-wrapper">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Laporkan</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    @php
        $totalReport = isset($reports) ? $reports->count() : 0;
        $resolvedReport = isset($reports) ? $reports->where('marked_as_resolved', true)->count() : 0;
        $unresolvedReport = isset($reports) ? $reports->where('marked_as_resolved', false)->count() : 0;
        $jenisReport = isset($reports) ? $reports->whereNotNull('disrepancy_type')->groupBy('disrepancy_type')->count() : 0;
    @endphp

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="summary-card" style="background:linear-gradient(135deg,#365CF5,#6a8ffd);">
                <div class="icon-wrap"><i class="fas fa-flag"></i></div>
                <div><div class="label">Total Laporan</div><div class="count">{{ $totalReport }}</div></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="summary-card" style="background:linear-gradient(135deg,#22c55e,#4ade80);">
                <div class="icon-wrap"><i class="fas fa-check-circle"></i></div>
                <div><div class="label">Sudah Selesai</div><div class="count">{{ $resolvedReport }}</div></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="summary-card" style="background:linear-gradient(135deg,#f59e0b,#fbbf24);">
                <div class="icon-wrap"><i class="fas fa-hourglass-half"></i></div>
                <div><div class="label">Belum Selesai</div><div class="count">{{ $unresolvedReport }}</div></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="summary-card" style="background:linear-gradient(135deg,#8b5cf6,#a78bfa);">
                <div class="icon-wrap"><i class="fas fa-layer-group"></i></div>
                <div><div class="label">Jenis Laporan</div><div class="count">{{ $jenisReport }}</div></div>
            </div>
        </div>
    </div>

    <!-- Card Wrapper -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0 rounded-top-4 py-3 px-4">
            <h5 class="mb-2 mb-md-0 fw-bold text-primary">
                <i class="fas fa-file-alt me-2"></i> Daftar Laporan Disrepancy
            </h5>
        </div>
        <div class="card-body px-4 py-3">
            <div class="table-responsive rounded-3">
                <table id="example" class="table table-hover align-middle w-100">
                    <thead class="table-custom-header">
                        <tr>
                            <th>Nama Siswa</th>
                            <th>Tanggal</th>
                            <th>Jenis</th>
                            <th>Deskripsi</th>
                            <th>Kelas</th>
                            <th>Pelapor</th>
                            <th>Status</th>
                            <th>ID Absensi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reports as $item)
                        <tr>
                            <td>{{ $item->student_name }}</td>
                            <td>{{ $item->attendance_date }}</td>
                            <td>{{ $item->disrepancy_type ?? '-' }}</td>
                            <td>{{ $item->description ?? '-' }}</td>
                            <td>
                                <span class="badge rounded-pill px-3 py-2" style="background:#f0fdf4;color:#15803d;font-weight:600;">
                                    {{ $item->id_class }}
                                </span>
                            </td>
                            <td>{{ $item->reported_by }}</td>
                            <td>
                                @if($item->marked_as_resolved)
                                    <span class="badge bg-success">Selesai</span>
                                @else
                                    <span class="badge bg-warning text-dark">Belum Selesai</span>
                                @endif
                            </td>
                             <td>{{ $item->id_attendance_history }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#example').DataTable({
                lengthChange: false,
                language: {
                    search: "Cari:",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    emptyTable: "Tidak ada data laporan.",
                    paginate: { first:"Awal", last:"Akhir", next:"Berikutnya", previous:"Sebelumnya" }
                },
                pageLength: 10
            });
        });
    </script>
@endsection

