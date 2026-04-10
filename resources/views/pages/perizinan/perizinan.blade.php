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
                    <h2 style="font-weight:500;">Perizinan</h2>
                </div>
            </div>
            <div class="col-md-6">
                <div class="breadcrumb-wrapper">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Perizinan</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    @php
        $permissionsData = $permissions ?? $reports ?? collect();
        $totalPermission = $permissionsData->count();
        $approvedPermission = $permissionsData->where('status', 'diterima')->count();
        $processPermission = $permissionsData->where('status', 'proses')->count();
        $rejectedPermission = $permissionsData->where('status', 'ditolak')->count();
    @endphp

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="summary-card" style="background:linear-gradient(135deg,#365CF5,#6a8ffd);">
                <div class="icon-wrap"><i class="fas fa-flag"></i></div>
                <div><div class="label">Total Perizinan</div><div class="count">{{ $totalPermission }}</div></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="summary-card" style="background:linear-gradient(135deg,#22c55e,#4ade80);">
                <div class="icon-wrap"><i class="fas fa-check-circle"></i></div>
                <div><div class="label">Diterima</div><div class="count">{{ $approvedPermission }}</div></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="summary-card" style="background:linear-gradient(135deg,#f59e0b,#fbbf24);">
                <div class="icon-wrap"><i class="fas fa-hourglass-half"></i></div>
                <div><div class="label">Diproses</div><div class="count">{{ $processPermission }}</div></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="summary-card" style="background:linear-gradient(135deg,#8b5cf6,#a78bfa);">
                <div class="icon-wrap"><i class="fas fa-times-circle"></i></div>
                <div><div class="label">Ditolak</div><div class="count">{{ $rejectedPermission }}</div></div>
            </div>
        </div>
    </div>

    <!-- Card Wrapper -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0 rounded-top-4 py-3 px-4">
            <h5 class="mb-2 mb-md-0 fw-bold text-primary">
                <i class="fas fa-file-alt me-2"></i> Daftar Perizinan
            </h5>
        </div>
        <div class="card-body px-4 py-3">
            <div class="table-responsive rounded-3">
                <table id="example" class="table table-hover align-middle w-100">
                    <thead class="table-custom-header">
                        <tr>
                            <th>Siswa</th>
                            <th>Alasan</th>
                            <th>Informasi</th>
                            <th>Bukti</th>
                            <th>Status</th>
                            <th>Feedback</th>
                            <th>Tanggal Izin</th>
                            <th>Jam/Periode</th>
                            <th>Disetujui Oleh</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($permissionsData as $item)
                        <tr>
                            <td>{{ $item->student->name ?? $item->id_student }}</td>
                            <td class="text-capitalize">{{ $item->reason }}</td>
                            <td>{{ $item->information ?? '-' }}</td>
                            <td>
                                @if($item->evidence)
                                    <a href="{{ asset($item->evidence) }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-paperclip me-1"></i>Lihat
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($item->status === 'diterima')
                                    <span class="badge bg-success">Diterima</span>
                                @elseif($item->status === 'ditolak')
                                    <span class="badge bg-danger">Ditolak</span>
                                @else
                                    <span class="badge bg-warning text-dark">Diproses</span>
                                @endif
                            </td>
                            <td>{{ $item->feedback ?? '-' }}</td>
                            <td>{{ $item->date_permission }}</td>
                            <td>{{ $item->time_period }}</td>
                            <td>{{ $item->approver->name ?? $item->approved_by ?? '-' }}</td>
                            <td>
                                @if($item->status === 'proses')
                                    <div class="d-flex gap-2">
                                        <form action="{{ url('permissions/' . $item->id . '/approve') }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Setujui pengajuan ini?')">Approve</button>
                                        </form>
                                        <form action="{{ url('permissions/' . $item->id . '/reject') }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Tolak pengajuan ini?')">Tolak</button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
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
            if (!$('#example').length) {
                return;
            }

            $('#example').DataTable({
                lengthChange: false,
                language: {
                    search: "Cari:",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    emptyTable: "Tidak ada data perizinan.",
                    paginate: { first:"Awal", last:"Akhir", next:"Berikutnya", previous:"Sebelumnya" }
                },
                pageLength: 10
            });
        });
    </script>
@endsection

