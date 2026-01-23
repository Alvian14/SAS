@extends('pages.index')

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
        <form>
          <div class="mb-3">
            <label for="tahun_ajaran" class="form-label">Tahun Ajaran</label>
            <input type="text" class="form-control" id="tahun_ajaran" placeholder="2023/2024">
          </div>
          <div class="mb-3">
            <label for="semester" class="form-label">Semester</label>
            <select class="form-select" id="semester">
              <option value="ganjil">Ganjil</option>
              <option value="genap">Genap</option>
            </select>
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
          <table class="table table-bordered align-middle">
            <thead>
              <tr>
                <th>#</th>
                <th>Tahun Ajaran</th>
                <th>Semester</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              {{-- Contoh data statis, nanti ganti dengan @foreach --}}
              <tr>
                <td>1</td>
                <td>2023/2024</td>
                <td>Ganjil</td>
                <td>
                  <span class="badge bg-success">Aktif</span>
                </td>
                <td>
                  <button class="btn btn-sm btn-secondary" disabled>Aktifkan</button>
                  <button class="btn btn-sm btn-warning">Edit</button>
                  <button class="btn btn-sm btn-danger">Hapus</button>
                </td>
              </tr>
              <tr>
                <td>2</td>
                <td>2023/2024</td>
                <td>Genap</td>
                <td>
                  <span class="badge bg-secondary">Tidak Aktif</span>
                </td>
                <td>
                  <button class="btn btn-sm btn-success">Aktifkan</button>
                  <button class="btn btn-sm btn-warning">Edit</button>
                  <button class="btn btn-sm btn-danger">Hapus</button>
                </td>
              </tr>
              {{-- End contoh data --}}
            </tbody>
          </table>
        </div>
        <small class="text-muted">* Hanya satu periode yang bisa aktif dalam satu waktu.</small>
      </div>
    </div>
  </div>
</div>
@endsection

