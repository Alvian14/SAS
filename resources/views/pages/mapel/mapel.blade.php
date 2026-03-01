@extends('pages.index')

@section('admin_content')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
    .table-custom-header th {
        background: linear-gradient(90deg, #365CF5 0%, #6a8ffd 100%) !important;
        color: white !important; font-weight: 600; letter-spacing: 0.5px;
        padding: 14px 12px !important; border: none !important; white-space: nowrap;
    }
    .table tbody tr { transition: background 0.2s; }
    .table tbody tr:nth-child(even) { background: #f4f7ff !important; }
    .table tbody tr:hover { background: #e3eafd !important; }
    .table th, .table td { border: none !important; vertical-align: middle !important; }
    .table { border-collapse: separate !important; border-spacing: 0 !important; font-size: 14px; }
    .summary-card { border-radius: 16px; padding: 18px 24px; color: white; display: flex; align-items: center; gap: 16px; box-shadow: 0 4px 16px rgba(0,0,0,0.10); transition: transform 0.2s; }
    .summary-card:hover { transform: translateY(-3px); }
    .summary-card .icon-wrap { width: 52px; height: 52px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.7rem; }
    .summary-card .label { font-size: 0.85rem; opacity: 0.85; }
    .summary-card .count { font-size: 2rem; font-weight: 800; line-height: 1; }
    .form-card { border-radius: 16px; border: none; box-shadow: 0 4px 20px rgba(54,92,245,0.08); }
    .form-card .card-header { background: linear-gradient(135deg, #365CF5 0%, #6a8ffd 100%); border-radius: 16px 16px 0 0; padding: 1.2rem 1.5rem; }
</style>

<div class="container-fluid">
    <div class="title-wrapper pt-30">
        <div class="row align-items-start">
            <div class="col-md-6">
                <h2 style="font-weight:500;"><i class="fas fa-book me-2 text-primary"></i>Mata Pelajaran</h2>
            </div>
            <div class="col-md-6">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb justify-content-md-end" style="font-size: 0.90rem;">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Mata Pelajaran</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    @php
        $totalMapel  = $subjects->count();
        $totalUmum   = $subjects->where('type','umum')->count();
        $totalJurusan = $subjects->where('type','jurusan')->count();
    @endphp

    <!-- Summary Cards -->
    <div class="row g-3 mb-4 mt-3">
        <div class="col-6 col-md-4">
            <div class="summary-card" style="background:linear-gradient(135deg,#365CF5,#6a8ffd);">
                <div class="icon-wrap"><i class="fas fa-book"></i></div>
                <div><div class="label">Total Mapel</div><div class="count">{{ $totalMapel }}</div></div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="summary-card" style="background:linear-gradient(135deg,#22c55e,#4ade80);">
                <div class="icon-wrap"><i class="fas fa-globe"></i></div>
                <div><div class="label">Umum</div><div class="count">{{ $totalUmum }}</div></div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="summary-card" style="background:linear-gradient(135deg,#8b5cf6,#a78bfa);">
                <div class="icon-wrap"><i class="fas fa-tools"></i></div>
                <div><div class="label">Jurusan</div><div class="count">{{ $totalJurusan }}</div></div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm mb-4">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Form Tambah Mapel -->
        <div class="col-lg-4">
            <div class="card form-card">
                <div class="card-header">
                    <h5 class="mb-0 text-white fw-bold"><i class="fas fa-plus-circle me-2"></i>Tambah Mata Pelajaran</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('mapel.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark"><i class="fas fa-book me-1 text-primary"></i> Nama Mapel</label>
                            <input type="text" class="form-control rounded-3" name="name" placeholder="Contoh: Matematika" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark"><i class="fas fa-tag me-1 text-primary"></i> Tipe</label>
                            <select class="form-select rounded-3" name="type" required>
                                <option value="">Pilih Tipe</option>
                                <option value="umum">Umum</option>
                                <option value="jurusan">Jurusan</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark"><i class="fas fa-code me-1 text-primary"></i> Kode Mapel</label>
                            <input type="text" class="form-control rounded-3" name="code" placeholder="Contoh: MTK01" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 fw-bold rounded-3 py-2">
                            <i class="fas fa-save me-2"></i>Simpan Mapel
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Daftar Mapel -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 rounded-top-4 py-3 px-4">
                    <h5 class="mb-0 fw-bold text-primary"><i class="fas fa-list me-2"></i>Daftar Mata Pelajaran</h5>
                </div>
                <div class="card-body px-4 py-3">
                    <div class="table-responsive rounded-3">
                        <table class="table table-hover align-middle w-100">
                            <thead class="table-custom-header">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Mapel</th>
                                    <th>Tipe</th>
                                    <th>Kode</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($subjects as $i => $subject)
                                <tr>
                                    <td><span class="fw-semibold text-muted">{{ $i+1 }}</span></td>
                                    <td><span class="fw-semibold text-dark">{{ $subject->name }}</span></td>
                                    <td>
                                        @if($subject->type == 'umum')
                                            <span class="badge rounded-pill px-3 py-2 fw-bold" style="background:#dcfce7;color:#16a34a;">
                                                <i class="fas fa-globe me-1"></i> Umum
                                            </span>
                                        @else
                                            <span class="badge rounded-pill px-3 py-2 fw-bold" style="background:#ede9fe;color:#7c3aed;">
                                                <i class="fas fa-tools me-1"></i> Jurusan
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge rounded-pill px-3 py-2" style="background:#e3eafd;color:#365CF5;font-weight:600;">
                                            {{ $subject->code }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex gap-1 justify-content-center">
                                            <button class="btn btn-sm rounded-pill px-2" style="background:#fef9c3;color:#b45309;" title="Edit"
                                                data-bs-toggle="modal" data-bs-target="#editMapelModal"
                                                onclick="setEditMapel('{{ $subject->id }}','{{ $subject->name }}','{{ $subject->type }}','{{ $subject->code }}')">
                                                <i class="fas fa-pencil-alt"></i>
                                            </button>
                                            <button class="btn btn-sm rounded-pill px-2" style="background:#fee2e2;color:#dc2626;" title="Hapus"
                                                data-bs-toggle="modal" data-bs-target="#deleteMapelModal"
                                                onclick="setDeleteMapel('{{ $subject->id }}','{{ $subject->name }}')">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <i class="fas fa-book fa-2x mb-2 d-block opacity-50"></i>
                                        Belum ada data mata pelajaran.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Edit Mapel --}}
    <div class="modal fade" id="editMapelModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form class="modal-content shadow-lg border-0 rounded-4" id="editMapelForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header border-0 rounded-top-4" style="background:linear-gradient(135deg,#f59e0b,#fbbf24);">
                    <h5 class="modal-title fw-bold text-dark"><i class="fas fa-pencil-alt me-2"></i>Edit Mata Pelajaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body bg-light p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold"><i class="fas fa-book me-1 text-primary"></i> Nama Mapel</label>
                        <input type="text" class="form-control rounded-3" id="edit_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold"><i class="fas fa-tag me-1 text-primary"></i> Tipe</label>
                        <select class="form-select rounded-3" id="edit_type" name="type" required>
                            <option value="">Pilih Tipe</option>
                            <option value="umum">Umum</option>
                            <option value="jurusan">Jurusan</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold"><i class="fas fa-code me-1 text-primary"></i> Kode Mapel</label>
                        <input type="text" class="form-control rounded-3" id="edit_code" name="code" required>
                    </div>
                </div>
                <div class="modal-footer bg-white border-0 p-3 gap-2">
                    <button type="button" class="btn btn-secondary rounded-3 px-4 fw-semibold" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-warning rounded-3 px-4 fw-semibold">
                        <i class="fas fa-save me-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Hapus Mapel --}}
    <div class="modal fade" id="deleteMapelModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form class="modal-content shadow-lg border-0 rounded-4" id="deleteMapelForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header border-0 rounded-top-4 bg-danger">
                    <h5 class="modal-title fw-bold text-white"><i class="fas fa-trash-alt me-2"></i>Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body bg-light p-4 text-center">
                    <div class="mb-3"><i class="fas fa-exclamation-triangle fa-3x text-danger opacity-75"></i></div>
                    <p class="fw-semibold text-dark mb-1">Anda yakin ingin menghapus mapel:</p>
                    <p class="badge rounded-pill px-4 py-2 fw-bold fs-6" style="background:#fee2e2;color:#dc2626;" id="deleteMapelName"></p>
                    <p class="text-muted small mt-2">Tindakan ini tidak dapat dibatalkan.</p>
                </div>
                <div class="modal-footer bg-white border-0 p-3 gap-2">
                    <button type="button" class="btn btn-secondary rounded-3 px-4 fw-semibold" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-danger rounded-3 px-4 fw-semibold">
                        <i class="fas fa-trash me-1"></i> Hapus
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function setEditMapel(id, name, type, code) {
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_type').value = type;
        document.getElementById('edit_code').value = code;
        document.getElementById('editMapelForm').action = "{{ url('/pages/mapel/mapel') }}/" + id;
    }
    function setDeleteMapel(id, name) {
        document.getElementById('deleteMapelName').innerText = name;
        document.getElementById('deleteMapelForm').action = "{{ url('/pages/mapel/mapel') }}/" + id;
    }
    </script>
</div>
@endsection
