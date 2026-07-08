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
                            <td>
                                @if(!empty($item->approver->name))
                                    {{ $item->approver->name }}
                                @elseif(isset($item->approved_by) && $item->approved_by !== null && $item->approved_by !== '')
                                    {{ is_numeric($item->approved_by) ? 'Admin' : $item->approved_by }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($item->status === 'proses')
                                    <div class="d-flex gap-2">
                                        <form action="{{ url('permissions/' . $item->id . '/approve') }}" method="POST" class="form-approve-permission" data-student-name="{{ $item->student->name ?? $item->id_student }}">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success">
                                                <i class="fas fa-check me-1"></i> Approve
                                            </button>
                                        </form>
                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal" data-permission-id="{{ $item->id }}" data-student-name="{{ $item->student->name ?? $item->id_student }}">
                                            <i class="fas fa-times me-1"></i> Tolak
                                        </button>
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

    <!-- Reject Modal with Feedback -->
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0">
                <div class="modal-header bg-danger text-white border-0">
                    <h5 class="modal-title" id="rejectModalLabel">
                        <i class="fas fa-times-circle me-2"></i> Tolak Pengajuan Izin
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="rejectForm" action="" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p class="mb-3">
                            <strong>Siswa:</strong> <span id="studentNameDisplay"></span>
                        </p>
                        <div class="mb-3">
                            <label for="feedbackInput" class="form-label">Feedback (Alasan Penolakan)</label>
                            <textarea
                                class="form-control"
                                id="feedbackInput"
                                name="feedback"
                                rows="4"
                                placeholder="Masukkan alasan penolakan (opsional)"
                                style="border: 1px solid #dee2e6; border-radius: 8px;"
                            ></textarea>
                            <small class="text-muted d-block mt-2">Feedback ini akan dikirimkan ke siswa melalui notifikasi.</small>
                        </div>
                    </div>
                    <div class="modal-footer border-top bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-check me-1"></i> Tolak Pengajuan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Handle reject modal
        const rejectModal = document.getElementById('rejectModal');
        if (rejectModal) {
            rejectModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const permissionId = button.getAttribute('data-permission-id');
                const studentName = button.getAttribute('data-student-name');

                document.getElementById('studentNameDisplay').textContent = studentName;
                document.getElementById('rejectForm').action = '/permissions/' + permissionId + '/reject';
                document.getElementById('feedbackInput').value = '';
            });
        }

        // Konfirmasi approve via SweetAlert (mengganti window.confirm)
        document.querySelectorAll('.form-approve-permission').forEach(function (form) {
            form.addEventListener('submit', function (e) {
                if (form.dataset.confirmed === 'true') {
                    return; // sudah dikonfirmasi, lanjutkan submit
                }
                e.preventDefault();
                const studentName = form.getAttribute('data-student-name') || 'siswa ini';
                Swal.fire({
                    icon: 'question',
                    title: 'Setujui Pengajuan Izin?',
                    html: 'Anda akan menyetujui pengajuan izin dari <strong>' + studentName + '</strong>.',
                    showCancelButton: true,
                    confirmButtonText: '<i class="fas fa-check me-1"></i> Ya, Setujui',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#22c55e',
                    cancelButtonColor: '#6c757d',
                    reverseButtons: true,
                    customClass: {
                        popup: 'rounded-4 shadow-lg',
                        title: 'fw-bold',
                        confirmButton: 'fw-semibold px-4',
                        cancelButton: 'fw-semibold px-4'
                    }
                }).then(function (result) {
                    if (result.isConfirmed) {
                        form.dataset.confirmed = 'true';
                        form.submit();
                    }
                });
            });
        });

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

    {{-- Flash feedback (success / error) --}}
    <div id="page-flash"
        data-success="{{ session('success') ?? '' }}"
        data-error="{{ session('error') ?? '' }}"
        style="display:none;"></div>

    <script>
        (function () {
            const flashEl = document.getElementById('page-flash');
            if (!flashEl) return;
            const successMsg = flashEl.dataset.success || '';
            const errorMsg = flashEl.dataset.error || '';

            document.addEventListener('DOMContentLoaded', function () {
                if (successMsg) {
                    Swal.fire({
                        toast: true,
                        position: 'bottom-end',
                        icon: 'success',
                        title: successMsg,
                        showConfirmButton: false,
                        timer: 2500,
                        timerProgressBar: true
                    });
                } else if (errorMsg) {
                    Swal.fire({
                        toast: true,
                        position: 'bottom-end',
                        icon: 'error',
                        title: errorMsg,
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                }
            });
        })();
    </script>
@endsection

