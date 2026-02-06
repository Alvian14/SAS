@extends('pages.index')

@php
  use Carbon\Carbon;
@endphp

@section('admin_content')
<div class="container-fluid">
  <!-- ========== title-wrapper start ========== -->
  <div class="title-wrapper pt-30">
    <div class="row align-items-start">
      <div class="col-md-6">
        <div class="title">
          <h2>Periode Absensi</h2>
        </div>
      </div>
      <div class="col-md-6">
        <div class="breadcrumb-wrapper">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item">
                <a href="{{ route('periode.index') }}">Periode</a>
              </li>
              <li class="breadcrumb-item active" aria-current="page">
                Kelola Periode
              </li>
            </ol>
          </nav>
        </div>
      </div>
    </div>
  </div>
  <!-- ========== title-wrapper end ========== -->

  <div class="row">
    <!-- Form Tambah Periode -->
    <div class="col-lg-4 mb-4">
      <div class="card-style">
        <h5 class="mb-3">Tambah Periode Baru</h5>
        @if(session('success'))
          <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if($errors->any())
          <div class="alert alert-danger">
            <ul class="mb-0">
              @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
              @endforeach
            </ul>
          </div>
        @endif
        <form action="{{ route('periode.store') }}" method="POST">
          @csrf
          <div class="mb-3">
            <label for="tahun_ajaran" class="form-label">Tahun Ajaran</label>
            <input type="text" class="form-control" id="tahun_ajaran" placeholder="2023/2024" name="tahun_ajaran" value="{{ old('tahun_ajaran') }}">
          </div>
          <div class="mb-3">
            <label for="semester" class="form-label">Semester</label>
            <select class="form-select" id="semester" name="semester">
              <option value="ganjil" {{ old('semester') == 'ganjil' ? 'selected' : '' }}>Ganjil</option>
              <option value="genap" {{ old('semester') == 'genap' ? 'selected' : '' }}>Genap</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="start_date" class="form-label">Tanggal Mulai</label>
            <input type="date" class="form-control" id="start_date" name="start_date" value="{{ old('start_date') }}">
          </div>
          <div class="mb-3">
            <label for="end_date" class="form-label">Tanggal Selesai</label>
            <input type="date" class="form-control" id="end_date" name="end_date" value="{{ old('end_date') }}">
          </div>
          <button type="submit" class="btn btn-primary w-100">Tambah</button>
        </form>
      </div>
    </div>

    <!-- Daftar Periode -->
    <div class="col-lg-8">
      <div class="card-style">
        <h5 class="mb-3">Daftar Periode</h5>
        <div class="table-responsive">
          <table class="table table-striped table-bordered table-hover align-middle">
            <thead >
              <tr>
                <th class="px-3 py-2">No</th>
                <th class="px-3 py-2">Semester & Tahun Ajaran</th>
                <th class="px-3 py-2">Tanggal Mulai</th>
                <th class="px-3 py-2">Tanggal Selesai</th>
                <th class="px-3 py-2">Status</th>
                <th class="px-3 py-2" style="min-width:120px;">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($periods as $i => $p)
              <tr>
                <td class="px-3 py-2">{{ $i+1 }}</td>
                <td class="px-3 py-2">{{ $p->name }}</td>
                <td class="px-3 py-2">{{ Carbon::parse($p->start_date)->format('Y-m-d') }}</td>
                <td class="px-3 py-2">{{ Carbon::parse($p->end_date)->format('Y-m-d') }}</td>
                <td class="px-3 py-2">
                  @if($p->is_active)
                    <span class="badge bg-success">Aktif</span>
                  @else
                    <span class="badge bg-secondary">Tidak Aktif</span>
                  @endif
                </td>
                <td class="px-3 py-2" style="min-width:120px;">
                  <form action="{{ route('periode.activate', $p->id) }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-{{ $p->is_active ? 'secondary' : 'success' }} me-1 mb-1" {{ $p->is_active ? 'disabled' : '' }} title="Aktifkan">
                      <i class="fas fa-toggle-on"></i>
                    </button>
                  </form>
                  <button class="btn btn-sm btn-warning me-1 mb-1" title="Edit"
                    data-bs-toggle="modal" data-bs-target="#editPeriodeModal"
                    onclick="setEditPeriode(
                      '{{ $p->id }}',
                      '{{ explode(' ', $p->name, 2)[1] ?? '' }}',
                      '{{ strtolower(explode(' ', $p->name, 2)[0] ?? '') }}',
                      '{{ Carbon::parse($p->start_date)->format('Y-m-d') }}',
                      '{{ Carbon::parse($p->end_date)->format('Y-m-d') }}',
                      {{ $p->is_active ? 'true' : 'false' }}
                    )">
                    <i class="fas fa-pencil-alt"></i>
                  </button>
                  <button class="btn btn-sm btn-danger mb-1" title="Hapus">
                    <i class="fas fa-trash-alt"></i>
                  </button>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="6" class="text-center">Belum ada data periode.</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        <small class="text-muted">* Hanya satu periode yang bisa aktif dalam satu waktu.</small>
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
  </script>
</div>
@endsection

