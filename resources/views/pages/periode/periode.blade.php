@extends('pages.index')

@php
  use Carbon\Carbon;
@endphp

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
    .table { border-collapse: separate !important; border-spacing: 0 !important; font-size: 15px; }
    .summary-card { border-radius: 16px; padding: 18px 24px; color: white; display: flex; align-items: center; gap: 16px; box-shadow: 0 4px 16px rgba(0,0,0,0.10); transition: transform 0.2s; }
    .summary-card:hover { transform: translateY(-3px); }
    .summary-card .icon-wrap { width: 52px; height: 52px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.7rem; }
    .summary-card .label { font-size: 0.78rem; opacity: 0.85; }
    .summary-card .count { font-size: 1.6rem; font-weight: 800; line-height: 1; }
    .form-card { border-radius: 16px; border: none; box-shadow: 0 4px 20px rgba(54,92,245,0.08); }
    .form-card .card-header { background: linear-gradient(135deg, #365CF5 0%, #6a8ffd 100%); border-radius: 16px 16px 0 0; padding: 1.2rem 1.5rem; }
</style>

<div class="container-fluid">
  <div class="title-wrapper pt-30">
    <div class="row align-items-start">
      <div class="col-md-6">
        <h2 style="font-weight:500;"><i class="fas fa-calendar-alt me-2 text-primary"></i>Periode Absensi</h2>
      </div>
      <div class="col-md-6">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb justify-content-md-end" style="font-size: 0.90rem;">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Periode Absensi</li>
          </ol>
        </nav>
      </div>
    </div>
  </div>

  @php
      $totalPeriode  = $periods->count();
      $periodeAktif  = $periods->where('is_active', true)->count();
      $periodeNonAktif = $periods->where('is_active', false)->count();
  @endphp

  <!-- Summary Cards -->
  <div class="row g-3 mb-4 mt-3">
      <div class="col-6 col-md-4">
          <div class="summary-card" style="background:linear-gradient(135deg,#365CF5,#6a8ffd);">
              <div class="icon-wrap"><i class="fas fa-calendar-alt"></i></div>
              <div><div class="label">Total Periode</div><div class="count">{{ $totalPeriode }}</div></div>
          </div>
      </div>
      <div class="col-6 col-md-4">
          <div class="summary-card" style="background:linear-gradient(135deg,#22c55e,#4ade80);">
              <div class="icon-wrap"><i class="fas fa-toggle-on"></i></div>
              <div><div class="label">Aktif</div><div class="count">{{ $periodeAktif }}</div></div>
          </div>
      </div>
      <div class="col-6 col-md-4">
          <div class="summary-card" style="background:linear-gradient(135deg,#94a3b8,#cbd5e1);">
              <div class="icon-wrap"><i class="fas fa-toggle-off"></i></div>
              <div><div class="label">Tidak Aktif</div><div class="count">{{ $periodeNonAktif }}</div></div>
          </div>
      </div>
  </div>

  @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
          <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
  @endif
  @if($errors->any())
      <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
          <i class="fas fa-exclamation-circle me-2"></i>
          <ul class="mb-0">@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
  @endif

  <div class="row g-4">
      <!-- Form Tambah Periode -->
      <div class="col-lg-4">
          <div class="card form-card">
              <div class="card-header">
                  <h5 class="mb-0 text-white fw-bold"><i class="fas fa-plus-circle me-2"></i>Tambah Periode Baru</h5>
              </div>
              <div class="card-body p-4">
                  <form action="{{ route('periode.store') }}" method="POST">
                      @csrf
                      <div class="mb-3">
                          <label class="form-label fw-semibold text-dark"><i class="fas fa-calendar me-1 text-primary"></i> Tahun Ajaran</label>
                          <input type="text" class="form-control rounded-3" placeholder="2023/2024" name="tahun_ajaran" value="{{ old('tahun_ajaran') }}">
                      </div>
                      <div class="mb-3">
                          <label class="form-label fw-semibold text-dark"><i class="fas fa-book me-1 text-primary"></i> Semester</label>
                          <select class="form-select rounded-3" name="semester">
                              <option value="ganjil" {{ old('semester') == 'ganjil' ? 'selected' : '' }}>Ganjil</option>
                              <option value="genap" {{ old('semester') == 'genap' ? 'selected' : '' }}>Genap</option>
                          </select>
                      </div>
                      <div class="mb-3">
                          <label class="form-label fw-semibold text-dark"><i class="fas fa-calendar-day me-1 text-primary"></i> Tanggal Mulai</label>
                          <input type="date" class="form-control rounded-3" name="start_date" value="{{ old('start_date') }}">
                      </div>
                      <div class="mb-3">
                          <label class="form-label fw-semibold text-dark"><i class="fas fa-calendar-check me-1 text-primary"></i> Tanggal Selesai</label>
                          <input type="date" class="form-control rounded-3" name="end_date" value="{{ old('end_date') }}">
                      </div>
                      <button type="submit" class="btn btn-primary w-100 fw-bold rounded-3 py-2">
                          <i class="fas fa-save me-2"></i>Simpan Periode
                      </button>
                  </form>
              </div>
          </div>
      </div>

      <!-- Daftar Periode -->
      <div class="col-lg-8">
          <div class="card border-0 shadow-sm rounded-4">
              <div class="card-header bg-white border-0 rounded-top-4 py-3 px-4">
                  <h5 class="mb-0 fw-bold text-primary"><i class="fas fa-list me-2"></i>Daftar Periode</h5>
              </div>
              <div class="card-body px-4 py-3">
                  <div class="table-responsive rounded-3">
                      <table class="table table-hover align-middle w-100">
                          <thead class="table-custom-header">
                              <tr>
                                  <th>No</th>
                                  <th>Semester</th>
                                  <th>Tanggal Mulai</th>
                                  <th>Tanggal Selesai</th>
                                  <th>Status</th>
                                  <th class="text-center">Aksi</th>
                              </tr>
                          </thead>
                          <tbody>
                              @forelse($periods as $i => $p)
                              <tr>
                                  <td><span class="fw-semibold text-muted">{{ $i+1 }}</span></td>
                                  <td>
                                      <span class="fw-semibold text-dark d-block">{{ $p->name }}</span>
                                  </td>
                                  <td>
                                      <span class="badge rounded-pill px-3 py-2" style="background:#e3eafd;color:#365CF5;font-weight:600;">
                                          <i class="fas fa-calendar-day me-1"></i>{{ Carbon::parse($p->start_date)->format('d M Y') }}
                                      </span>
                                  </td>
                                  <td>
                                      <span class="badge rounded-pill px-3 py-2" style="background:#fef9c3;color:#b45309;font-weight:600;">
                                          <i class="fas fa-calendar-check me-1"></i>{{ Carbon::parse($p->end_date)->format('d M Y') }}
                                      </span>
                                  </td>
                                  <td>
                                      @if($p->is_active)
                                          <span class="badge rounded-pill px-3 py-2 fw-bold" style="background:#dcfce7;color:#16a34a;">
                                              <i class="fas fa-check-circle me-1"></i> Aktif
                                          </span>
                                      @else
                                          <span class="badge rounded-pill px-3 py-2 fw-bold" style="background:#f3f4f6;color:#6b7280;">
                                              <i class="fas fa-times-circle me-1"></i> Tidak Aktif
                                          </span>
                                      @endif
                                  </td>
                                  <td class="text-center">
                                      <div class="d-flex gap-1 justify-content-center">
                                          <form action="{{ route('periode.activate', $p->id) }}" method="POST" style="display:inline;">
                                              @csrf
                                              <button type="submit" class="btn btn-sm rounded-pill px-2" style="background:#dcfce7;color:#16a34a;" {{ $p->is_active ? 'disabled' : '' }} title="Aktifkan">
                                                  <i class="fas fa-toggle-on"></i>
                                              </button>
                                          </form>
                                          <button class="btn btn-sm rounded-pill px-2" style="background:#fef9c3;color:#b45309;" title="Edit"
                                              data-bs-toggle="modal" data-bs-target="#editPeriodeModal"
                                              onclick="setEditPeriode('{{ $p->id }}','{{ explode(' ', $p->name, 2)[1] ?? '' }}','{{ strtolower(explode(' ', $p->name, 2)[0] ?? '') }}','{{ Carbon::parse($p->start_date)->format('Y-m-d') }}','{{ Carbon::parse($p->end_date)->format('Y-m-d') }}',{{ $p->is_active ? 'true' : 'false' }})">
                                              <i class="fas fa-pencil-alt"></i>
                                          </button>
                                          <button class="btn btn-sm rounded-pill px-2" style="background:#fee2e2;color:#dc2626;" title="Hapus"
                                              data-bs-toggle="modal" data-bs-target="#deletePeriodeModal"
                                              onclick="setDeletePeriode('{{ $p->id }}', '{{ $p->name }}')">
                                              <i class="fas fa-trash-alt"></i>
                                          </button>
                                      </div>
                                  </td>
                              </tr>
                              @empty
                              <tr>
                                  <td colspan="6" class="text-center text-muted py-4">
                                      <i class="fas fa-calendar-times fa-2x mb-2 d-block opacity-50"></i>
                                      Belum ada data periode.
                                  </td>
                              </tr>
                              @endforelse
                          </tbody>
                      </table>
                  </div>
                  <small class="text-muted"><i class="fas fa-info-circle me-1"></i> Hanya satu periode yang bisa aktif dalam satu waktu.</small>
              </div>
          </div>
      </div>
  </div>

  {{-- Modal Edit Periode --}}
  <div class="modal fade" id="editPeriodeModal" tabindex="-1" aria-labelledby="editPeriodeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <form class="modal-content" id="editPeriodeForm" method="POST">
        @csrf
        @method('PUT')
        <input type="hidden" id="edit_id" name="id">
        <div class="modal-header">
          <h5 class="modal-title" id="editPeriodeModalLabel">Edit Periode</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="edit_tahun_ajaran" class="form-label">Tahun Ajaran</label>
            <input type="text" class="form-control" id="edit_tahun_ajaran" name="tahun_ajaran">
          </div>
          <div class="mb-3">
            <label for="edit_semester" class="form-label">Semester</label>
            <select class="form-select" id="edit_semester" name="semester">
              <option value="ganjil">Ganjil</option>
              <option value="genap">Genap</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="edit_start_date" class="form-label">Tanggal Mulai</label>
            <input type="date" class="form-control" id="edit_start_date" name="start_date">
          </div>
          <div class="mb-3">
            <label for="edit_end_date" class="form-label">Tanggal Selesai</label>
            <input type="date" class="form-control" id="edit_end_date" name="end_date">
          </div>
          <div class="form-check mb-2">
            <input class="form-check-input" type="checkbox" value="1" id="edit_is_active" name="is_active">
            <label class="form-check-label" for="edit_is_active">
              Aktifkan Periode Ini
            </label>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>

  <script>
  // id, tahun_ajaran, semester, start, end, isActive
  function setEditPeriode(id, tahun_ajaran, semester, start, end, isActive) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_tahun_ajaran').value = tahun_ajaran;
    document.getElementById('edit_semester').value = semester.toLowerCase();
    document.getElementById('edit_start_date').value = start;
    document.getElementById('edit_end_date').value = end;
    document.getElementById('edit_is_active').checked = !!isActive;

    // Set action form
    var form = document.getElementById('editPeriodeForm');
    form.action = "{{ url('periode') }}/" + id;
  }
  // Set id periode untuk hapus
  function setDeletePeriode(id, name) {
    document.getElementById('delete_id').value = id;
    document.getElementById('deletePeriodeName').innerText = name;
    var form = document.getElementById('deletePeriodeForm');
    form.action = "{{ url('periode') }}/" + id;
  }
  </script>
  {{-- Modal Hapus Periode --}}
  <div class="modal fade" id="deletePeriodeModal" tabindex="-1" aria-labelledby="deletePeriodeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <form class="modal-content" id="deletePeriodeForm" method="POST">
        @csrf
        @method('DELETE')
        <input type="hidden" id="delete_id" name="id">
        <div class="modal-header">
          <h5 class="modal-title" id="deletePeriodeModalLabel">Konfirmasi Hapus Periode</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p>Apakah Anda yakin ingin menghapus periode <strong id="deletePeriodeName"></strong>?</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-danger">Hapus</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

