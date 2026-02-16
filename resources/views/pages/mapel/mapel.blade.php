@extends('pages.index')

@section('admin_content')
<div class="container-fluid">
  <!-- ========== title-wrapper start ========== -->
  <div class="title-wrapper pt-30">
    <div class="row align-items-start">
      <div class="col-md-6">
        <div class="title">
          <h2>Mata Pelajaran</h2>
        </div>
      </div>
      <div class="col-md-6">
        <div class="breadcrumb-wrapper">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item">
                <a href="{{ route('mapel.index') }}">Mata Pelajaran</a>
              </li>
              <li class="breadcrumb-item active" aria-current="page">
                Kelola Mapel
              </li>
            </ol>
          </nav>
        </div>
      </div>
    </div>
  </div>
  <!-- ========== title-wrapper end ========== -->

  <div class="row">
    <!-- Form Tambah Mapel -->
    <div class="col-lg-4 mb-4">
      <div class="card-style">
        <h5 class="mb-3">Tambah Mata Pelajaran</h5>
        <form action="{{ route('mapel.store') }}" method="POST">
          @csrf
          <div class="mb-3">
            <label for="name" class="form-label">Nama Mapel</label>
            <input type="text" class="form-control" id="name" name="name" placeholder="Contoh: Matematika" required>
          </div>
          <div class="mb-3">
            <label for="type" class="form-label">Tipe</label>
            <select class="form-select" id="type" name="type" required>
              <option value="">Pilih Tipe</option>
              <option value="umum">Umum</option>
              <option value="jurusan">Jurusan</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="code" class="form-label">Kode Mapel</label>
            <input type="text" class="form-control" id="code" name="code" placeholder="Contoh: MTK01" required>
          </div>
          <button type="submit" class="btn btn-primary w-100">Tambah</button>
        </form>
      </div>
    </div>

    <!-- Daftar Mapel -->
    <div class="col-lg-8">
      <div class="card-style">
        <h5 class="mb-3">Daftar Mata Pelajaran</h5>
        <div class="table-responsive">
          <table class="table table-striped table-bordered table-hover align-middle">
            <thead>
              <tr>
                <th class="px-3 py-2">No</th>
                <th class="px-3 py-2">Nama Mapel</th>
                <th class="px-3 py-2">Tipe</th>
                <th class="px-3 py-2">Kode</th>
                <th class="px-3 py-2" style="min-width:100px;">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($subjects as $i => $subject)
              <tr>
                <td class="px-3 py-2">{{ $i+1 }}</td>
                <td class="px-3 py-2">{{ $subject->name }}</td>
                <td class="px-3 py-2">{{ ucfirst($subject->type) }}</td>
                <td class="px-3 py-2">{{ $subject->code }}</td>
                <td class="px-3 py-2">
                  <button class="btn btn-sm btn-warning me-1 mb-1" title="Edit"
                    data-bs-toggle="modal" data-bs-target="#editMapelModal"
                    onclick="setEditMapel('{{ $subject->id }}', '{{ $subject->name }}', '{{ $subject->type }}', '{{ $subject->code }}')">
                    <i class="fas fa-pencil-alt"></i>
                  </button>
                  <button class="btn btn-sm btn-danger mb-1" title="Hapus"
                    data-bs-toggle="modal" data-bs-target="#deleteMapelModal"
                    onclick="setDeleteMapel('{{ $subject->id }}', '{{ $subject->name }}')">
                    <i class="fas fa-trash-alt"></i>
                  </button>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="5" class="text-center">Belum ada data mapel.</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  {{-- Modal Edit Mapel --}}
  <div class="modal fade" id="editMapelModal" tabindex="-1" aria-labelledby="editMapelModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <form class="modal-content" id="editMapelForm" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-header">
          <h5 class="modal-title" id="editMapelModalLabel">Edit Mata Pelajaran</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="edit_name" class="form-label">Nama Mapel</label>
            <input type="text" class="form-control" id="edit_name" name="name" required>
          </div>
          <div class="mb-3">
            <label for="edit_type" class="form-label">Tipe</label>
            <select class="form-select" id="edit_type" name="type" required>
              <option value="">Pilih Tipe</option>
              <option value="umum">Umum</option>
              <option value="jurusan">Jurusan</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="edit_code" class="form-label">Kode Mapel</label>
            <input type="text" class="form-control" id="edit_code" name="code" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>

  {{-- Modal Hapus Mapel --}}
  <div class="modal fade" id="deleteMapelModal" tabindex="-1" aria-labelledby="deleteMapelModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <form class="modal-content" id="deleteMapelForm" method="POST">
        @csrf
        @method('DELETE')
        <div class="modal-header">
          <h5 class="modal-title" id="deleteMapelModalLabel">Konfirmasi Hapus Mapel</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p>Apakah Anda yakin ingin menghapus mapel <strong id="deleteMapelName"></strong>?</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-danger">Hapus</button>
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
