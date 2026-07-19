@extends('pages.index')
@section('admin_content')

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css">
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
    .table { border-collapse: separate !important; border-spacing: 0 !important; font-size: 16px; }
    .summary-card { border-radius: 16px; padding: 18px 24px; color: white; display: flex; align-items: center; gap: 16px; box-shadow: 0 4px 16px rgba(0,0,0,0.10); transition: transform 0.2s; }
    .summary-card:hover { transform: translateY(-3px); }
    .summary-card .icon-wrap { width: 52px; height: 52px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.7rem; }
    .summary-card .label { font-size: 0.85rem; opacity: 0.85; }
    .summary-card .count { font-size: 2rem; font-weight: 800; line-height: 1; }
    .btn-tambah-siswa {
        font-weight: bold;
        background-color: #365CF5;
        color: white;
        padding: 8px 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.3s ease;
        border-radius: 8px;
    }
    .btn-tambah-siswa:hover {
        color: white;
        background-color: #2a4fd1;
    }
    .btn-hapus-siswa {
        font-weight: bold;
        background-color: transparent;
        color: #dc3545;
        border: 2px solid #dc3545;
        padding: 8px 16px;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        border-radius: 8px;
    }
    .btn-hapus-siswa:hover {
        background-color: #dc3545;
        color: white;
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
    .btn-dataset-siswa {
        font-weight: bold;
        background-color: transparent;
        color: #06b6d4;
        border: 2px solid #06b6d4;
        padding: 8px 16px;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        border-radius: 8px;
    }
    .btn-dataset-siswa:hover {
        background-color: #06b6d4;
        color: #fff;
    }
    .btn-camera-siswa {
        font-weight: bold;
        background-color: transparent;
        color: #17a2b8;
        border: 2px solid #17a2b8;
        padding: 8px 16px;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        border-radius: 8px;
    }
    .btn-camera-siswa:hover {
        background-color: #17a2b8;
        color: #fff;
    }
    @media (max-width: 767.98px) {
        .btn-tambah-siswa,
        .btn-edit-siswa,
        .btn-hapus-siswa {
            font-size: 15px !important;
            padding: 10px 18px !important;
            width: 100% !important;
        }
    }
</style>

<div class="container-fluid">
    <!-- ========== title-wrapper start ========== -->
    <div class="title-wrapper pt-30">
        <div class="row align-items-start">
            <div class="col-md-6">
                <h2 style="font-weight:500;">
                    <i class="fas fa-user-graduate me-2 text-primary"></i>Identitas Siswa
                </h2>
            </div>
            <div class="col-md-6">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb justify-content-md-end" style="font-size: 0.85rem;">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Identitas Siswa</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <!-- ========== title-wrapper end ========== -->

    @php
        $totalSiswa = $students->count();
        $totalKelas = $classes->count();
    @endphp

    <!-- Summary Cards -->
    <div class="row g-3 mb-4 mt-3">
        <div class="col-6 col-md-3">
            <div class="summary-card" style="background:linear-gradient(135deg,#365CF5,#6a8ffd);">
                <div class="icon-wrap"><i class="fas fa-user-graduate"></i></div>
                <div>
                    <div class="label">Total Siswa</div>
                    <div class="count">{{ $totalSiswa }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="summary-card" style="background:linear-gradient(135deg,#22c55e,#4ade80);">
                <div class="icon-wrap"><i class="fas fa-school"></i></div>
                <div>
                    <div class="label">Total Kelas</div>
                    <div class="count">{{ $totalKelas }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card Wrapper -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0 rounded-top-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center align-items-start py-3 px-4">
            <h5 class="mb-2 mb-md-0 fw-bold text-primary">
                <i class="fas fa-list me-2"></i>Daftar Identitas Siswa
            </h5>
            <div class="d-flex gap-2 flex-column flex-md-row w-100 w-md-auto justify-content-md-end mt-2 mt-md-0">
                <button class="btn btn-tambah-siswa btn-sm" style="font-size:14px;padding:7px 14px;" data-bs-toggle="modal" data-bs-target="#modalTambahSiswa">
                    <i class="fas fa-plus"></i> Tambah Siswa
                </button>
                <button class="btn btn-camera-siswa btn-sm" style="font-size:14px;padding:7px 14px;" id="btn-camera-siswa" type="button">
                    <i class="fas fa-camera me-1"></i> Kamera
                </button>
                <button class="btn btn-dataset-siswa btn-sm" style="font-size:14px;padding:7px 14px;" id="btn-dataset-siswa" type="button">
                    <i class="fas fa-database me-1"></i> Dataset
                </button>
                <button class="btn btn-edit-siswa btn-sm" style="font-size:14px;padding:7px 14px;" id="btn-edit-siswa" type="button">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button class="btn btn-hapus-siswa btn-sm" style="font-size:14px;padding:7px 14px;" id="btn-hapus-siswa" type="button">
                    <i class="fas fa-trash"></i> Hapus
                </button>
            </div>
        </div>
        <div class="card-body px-4 py-3">
            <div class="table-responsive rounded-3">
                <table id="example" class="table table-hover align-middle w-100">
                    <thead class="table-custom-header">
                        <tr>
                            <th class="text-center" style="width:40px;border-radius:12px 0 0 0;">
                                <input type="checkbox" id="select-all" />
                            </th>
                            <th class="text-center" style="width:60px;">Foto Profil</th>
                            <th class="text-center" style="width:60px;">Foto Webcam</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>NISN</th>
                            <th>Kelas</th>
                            <th>Tahun Masuk</th>
                            <th>Password</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $student)
                        <tr data-id="{{ $student->id }}">
                            <td class="text-center">
                                <input type="checkbox" class="row-checkbox" />
                            </td>
                            <td class="text-center">
                                @if($student->user->profile_picture)
                                    <img src="{{ asset('storage/profile_pictures/student/' . $student->user->profile_picture) }}" alt="Foto Profil" width="36" height="36" class="rounded-circle border border-2 border-primary shadow-sm">
                                @else
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($student->name) }}" alt="Foto Profil" width="36" height="36" class="rounded-circle border border-2 border-primary shadow-sm">
                                @endif
                            </td>
                            <td class="text-center">
                                @if($student->pictures)
                                    <div style="display: flex; gap: 5px; justify-content: center; flex-wrap: wrap;">
                                        @php
                                            $photos = explode(',', $student->pictures);
                                        @endphp
                                        @foreach($photos as $photo)
                                            @if(!empty($photo))
                                                <img src="{{ asset('storage/photo-webcam/' . $photo) }}" alt="Foto Webcam" width="32" height="32" class="rounded border border-1 border-success shadow-sm" style="object-fit: cover;">
                                            @endif
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-muted"><i class="fas fa-camera"></i></span>
                                @endif
                            </td>
                            <td><span class="fw-semibold text-dark d-block">{{ $student->name }}</span></td>
                            <td><span class="text-muted">{{ $student->user->email }}</span></td>
                            <td>
                                <span class="badge rounded-pill px-3 py-2" style="background:#e3eafd;color:#365CF5;font-weight:600;">
                                    {{ $student->nisn }}
                                </span>
                            </td>
                            <td data-class-id="{{ $student->class->id ?? '' }}">
                                <span class="badge rounded-pill px-3 py-2" style="background:#dcfce7;color:#16a34a;font-weight:600;">
                                    {{ $student->class->name ?? '-' }}
                                </span>
                            </td>
                            <td>{{ $student->entry_year }}</td>
                            <td>starbaks</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- End Card Wrapper -->

<!-- Modal Tambah Siswa -->
<div class="modal fade" id="modalTambahSiswa" tabindex="-1" aria-labelledby="modalTambahSiswaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content shadow-lg border-0 rounded-4">
            <div class="modal-header bg-primary border-0 rounded-top-4">
                <h5 class="modal-title fw-bold text-white" id="modalTambahSiswaLabel" style="color: white;">
                    Tambah Data Siswa
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formTambahSiswa" method="POST" action="{{ route('register.student.post') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4 bg-light">
                    <!-- Baris 1: Nama Lengkap dan Email -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">
                                Nama Lengkap
                            </label>
                            <input type="text" name="name" class="form-control border-2" placeholder="Masukkan nama lengkap" value="{{ old('name') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">
                                Email
                            </label>
                            <input type="email" name="email" class="form-control border-2" placeholder="contoh@email.com" value="{{ old('email') }}" required>
                        </div>
                    </div>

                    <!-- Baris 2: Password dan NISN -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">
                                Password
                            </label>
                            <input type="text" name="password" class="form-control border-2" value="starbaks" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">
                                NISN
                            </label>
                            <input type="number" name="nisn" class="form-control border-2" placeholder="Nomor Induk Siswa" value="{{ old('nisn') }}" required>
                        </div>
                    </div>

                    <!-- Baris 3: Kelas dan Tahun Masuk -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">
                                Kelas
                            </label>
                            <select name="id_class" class="form-select border-2" required>
                                <option value="">-- Pilih Kelas --</option>
                                @foreach($classes as $kelas)
                                    <option value="{{ $kelas->id }}" {{ (string)old('id_class') === (string)$kelas->id ? 'selected' : '' }}>{{ $kelas->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">
                                Tahun Masuk
                            </label>
                            <input type="number" name="entry_year" class="form-control border-2" placeholder="2024" min="2015" max="2030" value="{{ old('entry_year') }}" required>
                        </div>
                    </div>

                    <!-- Baris 4: Foto Profil (Full Width) -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold text-dark">
                                Foto Profil
                                <small class="text-muted">(opsional)</small>
                            </label>
                            <input type="file" name="profile_picture" class="form-control border-2" accept="image/*">
                            <div class="form-text text-muted">
                                Format: JPG, PNG, GIF. Maksimal 2MB.
                            </div>
                        </div>
                    </div>

                    <!-- Baris 4: Webcam Capture -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold text-dark">
                                <i class="fas fa-camera me-2"></i>Ambil Foto via Webcam
                            </label>
                            <div class="card border-light bg-light p-3">
                                <div id="initial-container" class="text-center">
                                    <button type="button" class="btn btn-outline-primary" id="btn-start-webcam">
                                        <i class="fas fa-video"></i> Aktifkan Webcam
                                    </button>
                                </div>

                                <div id="webcam-container" class="text-center" style="display: none;">
                                    <video id="webcam-video" style="width: 100%; max-width: 400px; border-radius: 8px; border: 2px solid #365CF5; margin-bottom: 10px;"></video>
                                    <div class="d-flex gap-2 justify-content-center mb-3">
                                        <button type="button" class="btn btn-primary btn-sm" id="btn-capture">
                                            <i class="fas fa-camera"></i> Ambil Foto
                                        </button>
                                        <button type="button" class="btn btn-secondary btn-sm" id="btn-stop-webcam">
                                            <i class="fas fa-stop"></i> Tutup
                                        </button>
                                    </div>
                                </div>

                                <div id="preview-container" class="text-center" style="display: none;">
                                    <canvas id="webcam-canvas" style="width: 100%; max-width: 400px; border-radius: 8px; border: 2px solid #365CF5; margin-bottom: 10px;"></canvas>
                                    <div class="d-flex gap-2 justify-content-center">
                                        <button type="button" class="btn btn-warning btn-sm" id="btn-retake">
                                            <i class="fas fa-redo"></i> Ambil Ulang
                                        </button>
                                        <button type="button" class="btn btn-success btn-sm" id="btn-use-photo">
                                            <i class="fas fa-check"></i> Gunakan
                                        </button>
                                    </div>
                                </div>

                                <div id="accepted-container" class="text-center" style="display: none;">
                                    <div style="position: relative; display: inline-block; margin-bottom: 15px;">
                                        <img id="accepted-photo-preview" src="" alt="Foto Diterima" style="width: 150px; height: 150px; border-radius: 8px; border: 2px solid #22c55e; object-fit: cover;">
                                        <button type="button" class="btn btn-sm btn-danger" id="btn-delete-accepted-photo" style="position: absolute; top: -10px; right: -10px; border-radius: 50%; width: 30px; height: 30px; padding: 0; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <div class="alert alert-success mb-3" role="alert">
                                        <i class="fas fa-check-circle me-2"></i> <span id="photos-progress">Foto 1 dari 3</span>
                                    </div>
                                    <button type="button" class="btn btn-primary btn-sm" id="btn-next-photo">
                                        <i class="fas fa-arrow-right me-2"></i> Lanjut ke Foto Berikutnya
                                    </button>
                                </div>

                                <div id="photos-summary-container" class="text-center" style="display: none;">
                                    <div class="alert alert-success mb-3" role="alert">
                                        <i class="fas fa-check-circle me-2"></i> Semua 3 foto berhasil diambil!
                                    </div>
                                    <div style="display: flex; gap: 10px; justify-content: center; flex-wrap: wrap; margin-bottom: 15px;">
                                        <div style="position: relative; display: inline-block;">
                                            <img id="summary-photo-1" src="" alt="Foto 1" style="width: 100px; height: 100px; border-radius: 8px; border: 2px solid #22c55e; object-fit: cover;">
                                            <button type="button" class="btn btn-sm btn-danger" data-photo="1" style="position: absolute; top: -10px; right: -10px; border-radius: 50%; width: 25px; height: 25px; padding: 0; display: flex; align-items: center; justify-content: center; font-size: 12px;" onclick="deletePhotoFromSummary(this)">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                        <div style="position: relative; display: inline-block;">
                                            <img id="summary-photo-2" src="" alt="Foto 2" style="width: 100px; height: 100px; border-radius: 8px; border: 2px solid #22c55e; object-fit: cover;">
                                            <button type="button" class="btn btn-sm btn-danger" data-photo="2" style="position: absolute; top: -10px; right: -10px; border-radius: 50%; width: 25px; height: 25px; padding: 0; display: flex; align-items: center; justify-content: center; font-size: 12px;" onclick="deletePhotoFromSummary(this)">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                        <div style="position: relative; display: inline-block;">
                                            <img id="summary-photo-3" src="" alt="Foto 3" style="width: 100px; height: 100px; border-radius: 8px; border: 2px solid #22c55e; object-fit: cover;">
                                            <button type="button" class="btn btn-sm btn-danger" data-photo="3" style="position: absolute; top: -10px; right: -10px; border-radius: 50%; width: 25px; height: 25px; padding: 0; display: flex; align-items: center; justify-content: center; font-size: 12px;" onclick="deletePhotoFromSummary(this)">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" id="webcam-photo-data" name="pictures" value="">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-white border-0 p-4 gap-2">
                    <button type="button" class="btn btn-danger px-4 fw-semibold shadow-sm" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-primary px-4 fw-semibold shadow-sm" id="btn-submit-form">
                        <i class="fas fa-save me-2"></i>Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Kamera Absensi -->
<div class="modal fade" id="modalKameraAbsensi" tabindex="-1" aria-labelledby="modalKameraAbsensiLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:520px;">
        <div class="modal-content shadow-lg border-0 rounded-4">
            <div class="modal-header border-0 rounded-top-4" style="background:linear-gradient(135deg,#17a2b8,#0e7d91);">
                <h5 class="modal-title fw-bold text-white" id="modalKameraAbsensiLabel">
                    <i class="fas fa-camera me-2"></i>Kamera Absensi
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-light text-center">
                <!-- Live Camera Feed -->
                <div id="absensi-camera-container">
                    <div style="position:relative;display:inline-block;width:100%;">
                        <video id="absensi-video"
                               autoplay playsinline
                               style="width:100%;max-width:460px;border-radius:12px;border:3px solid #17a2b8;background:#000;"></video>
                        <!-- Overlay loading -->
                        <div id="absensi-loading" style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,0.55);border-radius:12px;">
                            <div class="spinner-border text-info" role="status"></div>
                        </div>
                    </div>
                    <canvas id="absensi-canvas" style="display:none;"></canvas>

                    <div class="mt-3 d-flex gap-2 justify-content-center">
                        <button type="button" class="btn btn-info fw-semibold px-4" id="btn-absensi-capture" disabled>
                            <i class="fas fa-camera me-2"></i>Capture & Absen
                        </button>
                    </div>
                </div>

                <!-- Result Area -->
                <div id="absensi-result" class="mt-3" style="display:none;">
                    <div id="absensi-result-content"></div>
                </div>

                <!-- Sending Spinner -->
                <div id="absensi-sending" class="mt-3" style="display:none;">
                    <div class="d-flex align-items-center justify-content-center gap-2 text-info">
                        <div class="spinner-border spinner-border-sm" role="status"></div>
                        <span class="fw-semibold">Memproses pengenalan wajah...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-white border-0 px-4 pb-4 pt-0 gap-2 justify-content-center">
                <button type="button" class="btn btn-secondary px-4 fw-semibold" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Tutup
                </button>
                <button type="button" class="btn btn-outline-info px-4 fw-semibold" id="btn-absensi-retake" style="display:none;">
                    <i class="fas fa-redo me-2"></i>Ambil Ulang
                </button>
            </div>
        </div>
    </div>
</div>
<!-- End Modal Kamera Absensi -->

<!-- Modal Edit Siswa -->
<div class="modal fade" id="modalEditSiswa" tabindex="-1" aria-labelledby="modalEditSiswaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content shadow-lg border-0 rounded-4">
            <div class="modal-header bg-warning border-0 rounded-top-4">
                <h5 class="modal-title fw-bold text-dark" id="modalEditSiswaLabel">
                    Edit Data Siswa
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditSiswa" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body p-4 bg-light">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">
                                Nama Lengkap
                            </label>
                            <input type="text" name="name" id="edit-name" class="form-control border-2" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">
                                Email
                            </label>
                            <input type="email" name="email" id="edit-email" class="form-control border-2" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">
                                Password
                            </label>
                            <input type="text" name="password" id="edit-password" class="form-control border-2" value="starbaks" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">
                                NISN
                            </label>
                            <input type="number" name="nisn" id="edit-nisn" class="form-control border-2" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">
                                Kelas
                            </label>
                            <select name="id_class" id="edit-id_class" class="form-select border-2" required>
                                <option value="">-- Pilih Kelas --</option>
                                @foreach($classes as $kelas)
                                    <option value="{{ $kelas->id }}">{{ $kelas->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">
                                Tahun Masuk
                            </label>
                            <input type="number" name="entry_year" id="edit-entry_year" class="form-control border-2" min="2015" max="2030" required>
                        </div>
                    </div>

                    <!-- Foto Profil -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold text-dark">
                                Foto Profil
                                <small class="text-muted">(opsional, kosongkan jika tidak ingin mengganti)</small>
                            </label>
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <img id="edit-profile-preview" src="" alt="Foto Profil" width="64" height="64" class="rounded-circle border border-2 border-primary shadow-sm" style="object-fit: cover;">
                                <input type="file" name="profile_picture" id="edit-profile_picture" class="form-control border-2" accept="image/*">
                            </div>
                            <div class="form-text text-muted">
                                Format: JPG, PNG, GIF. Maksimal 2MB.
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold text-dark">
                                Foto Webcam yang Ada Saat Ini
                                <small class="text-muted">(akan diganti jika upload baru)</small>
                            </label>
                            <div id="existing-photos-container" class="card border-light bg-light p-3 mb-3">
                                <div style="display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">
                                    <div style="text-align: center;">
                                        <img id="existing-photo-1" src="" alt="Tidak ada" style="width: 100px; height: 100px; border-radius: 8px; border: 2px solid #dee2e6; object-fit: cover; display: none;">
                                        <p class="text-muted small mt-2" id="no-photo-1">-</p>
                                    </div>
                                    <div style="text-align: center;">
                                        <img id="existing-photo-2" src="" alt="Tidak ada" style="width: 100px; height: 100px; border-radius: 8px; border: 2px solid #dee2e6; object-fit: cover; display: none;">
                                        <p class="text-muted small mt-2" id="no-photo-2">-</p>
                                    </div>
                                    <div style="text-align: center;">
                                        <img id="existing-photo-3" src="" alt="Tidak ada" style="width: 100px; height: 100px; border-radius: 8px; border: 2px solid #dee2e6; object-fit: cover; display: none;">
                                        <p class="text-muted small mt-2" id="no-photo-3">-</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold text-dark">
                                Ambil Foto via Webcam
                                <small class="text-muted">(harus 3 foto)</small>
                            </label>
                            <div class="card border-light bg-light p-3">
                                <!-- Initial Container -->
                                <div id="initial-container-edit" class="text-center">
                                    <button type="button" id="btn-start-webcam-edit" class="btn btn-primary btn-sm mb-2">
                                        <i class="fas fa-camera me-2"></i>Mulai Webcam
                                    </button>
                                    <p class="text-muted small mb-0">Klik tombol untuk memulai capture foto dari webcam</p>
                                </div>

                                <!-- Webcam Container -->
                                <div id="webcam-container-edit" style="display: none;" class="text-center mb-3">
                                    <video id="webcam-video-edit" style="width: 100%; max-width: 400px; border: 2px solid #dee2e6; border-radius: 8px;"></video>
                                    <div class="mt-2">
                                        <button type="button" id="btn-capture-edit" class="btn btn-success btn-sm me-2">
                                            <i class="fas fa-camera me-1"></i>Ambil Foto
                                        </button>
                                        <button type="button" id="btn-stop-webcam-edit" class="btn btn-secondary btn-sm">
                                            <i class="fas fa-stop me-1"></i>Tutup
                                        </button>
                                    </div>
                                </div>

                                <!-- Preview Container -->
                                <div id="preview-container-edit" style="display: none;" class="text-center mb-3">
                                    <canvas id="webcam-canvas-edit" style="max-width: 100%; border: 2px solid #dee2e6; border-radius: 8px; display: none;"></canvas>
                                    <img id="preview-img-edit" src="" style="max-width: 100%; max-height: 400px; border: 2px solid #dee2e6; border-radius: 8px;">
                                    <div class="mt-2">
                                        <button type="button" id="btn-use-photo-edit" class="btn btn-success btn-sm me-2">
                                            <i class="fas fa-check me-1"></i>Terima
                                        </button>
                                        <button type="button" id="btn-retake-edit" class="btn btn-warning btn-sm">
                                            <i class="fas fa-redo me-1"></i>Ulangi
                                        </button>
                                    </div>
                                </div>

                                <!-- Accepted Container -->
                                <div id="accepted-container-edit" style="display: none;">
                                    <div style="position: relative; display: inline-block; margin-bottom: 15px;">
                                        <img id="accepted-photo-preview-edit" src="" alt="Foto Diterima" style="width: 150px; height: 150px; border-radius: 8px; border: 2px solid #22c55e; object-fit: cover;">
                                        <button type="button" class="btn btn-sm btn-danger" id="btn-delete-accepted-photo-edit" style="position: absolute; top: -10px; right: -10px; border-radius: 50%; width: 30px; height: 30px; padding: 0; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <div class="alert alert-success mb-3" role="alert">
                                        <i class="fas fa-check-circle me-2"></i> <span id="photos-progress-edit">Foto 1 dari 3</span>
                                    </div>
                                    <button type="button" class="btn btn-primary btn-sm" id="btn-next-photo-edit">
                                        <i class="fas fa-arrow-right me-2"></i> Lanjut ke Foto Berikutnya
                                    </button>
                                </div>

                                <!-- Photos Summary Container -->
                                <div id="photos-summary-container-edit" class="text-center" style="display: none;">
                                    <div class="alert alert-success mb-3" role="alert">
                                        <i class="fas fa-check-circle me-2"></i> Semua 3 foto berhasil diambil!
                                    </div>
                                    <div style="display: flex; gap: 10px; justify-content: center; flex-wrap: wrap; margin-bottom: 15px;">
                                        <div style="position: relative; display: inline-block;">
                                            <img id="summary-photo-1-edit" src="" alt="Foto 1" style="width: 100px; height: 100px; border-radius: 8px; border: 2px solid #22c55e; object-fit: cover;">
                                            <button type="button" class="btn btn-sm btn-danger" data-photo="1" style="position: absolute; top: -10px; right: -10px; border-radius: 50%; width: 25px; height: 25px; padding: 0; display: flex; align-items: center; justify-content: center; font-size: 12px;" onclick="deletePhotoFromSummaryEdit(this)">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                        <div style="position: relative; display: inline-block;">
                                            <img id="summary-photo-2-edit" src="" alt="Foto 2" style="width: 100px; height: 100px; border-radius: 8px; border: 2px solid #22c55e; object-fit: cover;">
                                            <button type="button" class="btn btn-sm btn-danger" data-photo="2" style="position: absolute; top: -10px; right: -10px; border-radius: 50%; width: 25px; height: 25px; padding: 0; display: flex; align-items: center; justify-content: center; font-size: 12px;" onclick="deletePhotoFromSummaryEdit(this)">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                        <div style="position: relative; display: inline-block;">
                                            <img id="summary-photo-3-edit" src="" alt="Foto 3" style="width: 100px; height: 100px; border-radius: 8px; border: 2px solid #22c55e; object-fit: cover;">
                                            <button type="button" class="btn btn-sm btn-danger" data-photo="3" style="position: absolute; top: -10px; right: -10px; border-radius: 50%; width: 25px; height: 25px; padding: 0; display: flex; align-items: center; justify-content: center; font-size: 12px;" onclick="deletePhotoFromSummaryEdit(this)">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" id="webcam-photo-data-edit" name="pictures_edit" value="">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-white border-0 p-4 gap-2">
                    <button type="button" class="btn btn-danger px-4 fw-semibold shadow-sm" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-warning px-4 fw-semibold shadow-sm text-white">
                        <i class="fas fa-save me-2"></i>Update Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Kelas Massal -->
<div class="modal fade" id="modalEditKelasMassal" tabindex="-1" aria-labelledby="modalEditKelasMassalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content shadow-lg border-0 rounded-4">
            <div class="modal-header bg-warning border-0 rounded-top-4">
                <h5 class="modal-title fw-bold text-dark" id="modalEditKelasMassalLabel">
                    Edit Kelas Siswa Terpilih
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditKelasMassal" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4 bg-light">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Kelas Baru</label>
                        <select name="id_class" id="mass-edit-class" class="form-select border-2" required>
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($classes as $kelas)
                                <option value="{{ $kelas->id }}">{{ $kelas->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="alert alert-info mb-0">
                        Semua siswa yang dipilih akan dipindahkan ke kelas ini.
                    </div>
                </div>
                <div class="modal-footer bg-white border-0 p-4 gap-2">
                    <button type="button" class="btn btn-danger px-4 fw-semibold shadow-sm" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-warning px-4 fw-semibold shadow-sm text-white">
                        <i class="fas fa-save me-2"></i>Update Kelas
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
        let webcamStream = null;
        let webcamCanvas = null;
        let collectedPhotos = [null, null, null];
        let currentPhotoIndex = 0;
        let collectedPhotosEdit = [null, null, null];
        let currentPhotoIndexEdit = 0;

        // Add Modal Event Listeners
        document.getElementById('btn-start-webcam').addEventListener('click', function(e) {
            e.preventDefault();
            startWebcam();
        });
        document.getElementById('btn-capture').addEventListener('click', function(e) {
            e.preventDefault();
            capturePhoto();
        });
        document.getElementById('btn-stop-webcam').addEventListener('click', function(e) {
            e.preventDefault();
            stopWebcam();
        });
        document.getElementById('btn-retake').addEventListener('click', function(e) {
            e.preventDefault();
            retakePhoto();
        });
        document.getElementById('btn-use-photo').addEventListener('click', function(e) {
            e.preventDefault();
            usePhoto();
        });
        document.getElementById('btn-delete-accepted-photo').addEventListener('click', function(e) {
            e.preventDefault();
            deleteAcceptedPhoto();
        });
        document.getElementById('btn-next-photo').addEventListener('click', function(e) {
            e.preventDefault();
            nextPhoto();
        });

        // Edit Modal Event Listeners
        document.getElementById('btn-start-webcam-edit').addEventListener('click', function(e) {
            e.preventDefault();
            startWebcamEdit();
        });        document.getElementById('btn-capture-edit').addEventListener('click', function(e) {
            e.preventDefault();
            capturePhotoEdit();
        });
        document.getElementById('btn-stop-webcam-edit').addEventListener('click', function(e) {
            e.preventDefault();
            stopWebcamEdit();
        });
        document.getElementById('btn-retake-edit').addEventListener('click', function(e) {
            e.preventDefault();
            retakePhotoEdit();
        });
        document.getElementById('btn-use-photo-edit').addEventListener('click', function(e) {
            e.preventDefault();
            usePhotoEdit();
        });
        document.getElementById('btn-delete-accepted-photo-edit').addEventListener('click', function(e) {
            e.preventDefault();
            deleteAcceptedPhotoEdit();
        });
        document.getElementById('btn-next-photo-edit').addEventListener('click', function(e) {
            e.preventDefault();
            nextPhotoEdit();
        });

        // Live preview foto profil saat memilih file baru (modal edit)
        document.getElementById('edit-profile_picture').addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('edit-profile-preview').src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });

        // Add Modal Functions
        function startWebcam() {
            const video = document.getElementById('webcam-video');
            const constraints = {
                video: { width: { ideal: 400 }, height: { ideal: 400 } },
                audio: false
            };
            navigator.mediaDevices.getUserMedia(constraints)
                .then(function(stream) {
                    webcamStream = stream;
                    video.srcObject = stream;
                    video.onloadedmetadata = function() {
                        video.play();
                        document.getElementById('initial-container').style.display = 'none';
                        document.getElementById('webcam-container').style.display = 'block';
                    };
                })
                .catch(function(err) {
                    alert('Gagal akses webcam: ' + err.message);
                });
        }

        function capturePhoto() {
            const video = document.getElementById('webcam-video');
            const canvas = document.getElementById('webcam-canvas');
            const context = canvas.getContext('2d');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            context.drawImage(video, 0, 0, canvas.width, canvas.height);
            webcamCanvas = canvas;
            stopWebcam();
            document.getElementById('webcam-container').style.display = 'none';
            document.getElementById('preview-container').style.display = 'block';
        }

        function stopWebcam() {
            if (webcamStream) {
                webcamStream.getTracks().forEach(track => track.stop());
                webcamStream = null;
            }
        }

        function retakePhoto() {
            document.getElementById('preview-container').style.display = 'none';
            document.getElementById('initial-container').style.display = 'block';
        }

        function usePhoto() {
            if (webcamCanvas) {
                const photoData = webcamCanvas.toDataURL('image/png');
                collectedPhotos[currentPhotoIndex] = photoData;
                document.getElementById('accepted-photo-preview').src = photoData;
                document.getElementById('photos-progress').textContent = `Foto ${currentPhotoIndex + 1} dari 3`;
                document.getElementById('preview-container').style.display = 'none';
                document.getElementById('accepted-container').style.display = 'block';
            }
        }

        function deleteAcceptedPhoto() {
            collectedPhotos[currentPhotoIndex] = null;
            document.getElementById('accepted-container').style.display = 'none';
            document.getElementById('initial-container').style.display = 'block';
            Swal.fire({
                toast: true,
                position: 'bottom-end',
                icon: 'info',
                title: 'Foto dihapus',
                showConfirmButton: false,
                timer: 2000
            });
        }

        function nextPhoto() {
            if (currentPhotoIndex < 2) {
                currentPhotoIndex++;
                document.getElementById('accepted-container').style.display = 'none';
                document.getElementById('initial-container').style.display = 'block';
            } else {
                showPhotosSummary();
            }
        }

        function showPhotosSummary() {
            for (let i = 0; i < 3; i++) {
                if (collectedPhotos[i]) {
                    document.getElementById(`summary-photo-${i + 1}`).src = collectedPhotos[i];
                }
            }
            const photosString = collectedPhotos.map(photo => photo ? photo : '').join('|||');
            document.getElementById('webcam-photo-data').value = photosString;
            document.getElementById('accepted-container').style.display = 'none';
            document.getElementById('photos-summary-container').style.display = 'block';
        }

        function deletePhotoFromSummary(btn) {
            const photoNum = parseInt(btn.getAttribute('data-photo')) - 1;
            collectedPhotos[photoNum] = null;
            document.getElementById(`summary-photo-${photoNum + 1}`).style.opacity = '0.5';
            btn.style.display = 'none';
            const photosString = collectedPhotos.map(photo => photo ? photo : '').join('|||');
            document.getElementById('webcam-photo-data').value = photosString;
            Swal.fire({
                toast: true,
                position: 'bottom-end',
                icon: 'info',
                title: 'Foto dihapus',
                showConfirmButton: false,
                timer: 2000
            });
        }

        // Edit Modal Functions
        function startWebcamEdit() {
            const video = document.getElementById('webcam-video-edit');
            const constraints = {
                video: { width: { ideal: 400 }, height: { ideal: 400 } },
                audio: false
            };
            navigator.mediaDevices.getUserMedia(constraints)
                .then(function(stream) {
                    webcamStream = stream;
                    video.srcObject = stream;
                    video.onloadedmetadata = function() {
                        video.play();
                        document.getElementById('initial-container-edit').style.display = 'none';
                        document.getElementById('webcam-container-edit').style.display = 'block';
                    };
                })
                .catch(function(err) {
                    alert('Gagal akses webcam: ' + err.message);
                });
        }

        function capturePhotoEdit() {
            const video = document.getElementById('webcam-video-edit');
            const canvas = document.getElementById('webcam-canvas-edit');
            const context = canvas.getContext('2d');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            context.drawImage(video, 0, 0, canvas.width, canvas.height);
            webcamCanvas = canvas;
            stopWebcamEdit();
            const previewImg = document.getElementById('preview-img-edit');
            previewImg.src = canvas.toDataURL('image/png');
            document.getElementById('webcam-container-edit').style.display = 'none';
            document.getElementById('preview-container-edit').style.display = 'block';
        }

        function stopWebcamEdit() {
            if (webcamStream) {
                webcamStream.getTracks().forEach(track => track.stop());
                webcamStream = null;
            }
        }

        function retakePhotoEdit() {
            document.getElementById('preview-container-edit').style.display = 'none';
            document.getElementById('initial-container-edit').style.display = 'block';
        }

        function usePhotoEdit() {
            if (webcamCanvas) {
                const photoData = webcamCanvas.toDataURL('image/png');
                collectedPhotosEdit[currentPhotoIndexEdit] = photoData;
                document.getElementById('accepted-photo-preview-edit').src = photoData;
                document.getElementById('photos-progress-edit').textContent = `Foto ${currentPhotoIndexEdit + 1} dari 3`;
                document.getElementById('preview-container-edit').style.display = 'none';
                document.getElementById('accepted-container-edit').style.display = 'block';
            }
        }

        function deleteAcceptedPhotoEdit() {
            collectedPhotosEdit[currentPhotoIndexEdit] = null;
            document.getElementById('accepted-container-edit').style.display = 'none';
            document.getElementById('initial-container-edit').style.display = 'block';
            Swal.fire({
                toast: true,
                position: 'bottom-end',
                icon: 'info',
                title: 'Foto dihapus',
                showConfirmButton: false,
                timer: 2000
            });
        }

        function nextPhotoEdit() {
            if (currentPhotoIndexEdit < 2) {
                currentPhotoIndexEdit++;
                document.getElementById('accepted-container-edit').style.display = 'none';
                document.getElementById('initial-container-edit').style.display = 'block';
            } else {
                showPhotosSummaryEdit();
            }
        }

        function showPhotosSummaryEdit() {
            for (let i = 0; i < 3; i++) {
                if (collectedPhotosEdit[i]) {
                    document.getElementById(`summary-photo-${i + 1}-edit`).src = collectedPhotosEdit[i];
                }
            }
            const photosString = collectedPhotosEdit.map(photo => photo ? photo : '').join('|||');
            document.getElementById('webcam-photo-data-edit').value = photosString;
            document.getElementById('accepted-container-edit').style.display = 'none';
            document.getElementById('photos-summary-container-edit').style.display = 'block';
        }

        function deletePhotoFromSummaryEdit(btn) {
            const photoNum = parseInt(btn.getAttribute('data-photo')) - 1;
            collectedPhotosEdit[photoNum] = null;
            document.getElementById(`summary-photo-${photoNum + 1}-edit`).style.opacity = '0.5';
            btn.style.display = 'none';
            const photosString = collectedPhotosEdit.map(photo => photo ? photo : '').join('|||');
            document.getElementById('webcam-photo-data-edit').value = photosString;
            Swal.fire({
                toast: true,
                position: 'bottom-end',
                icon: 'info',
                title: 'Foto dihapus',
                showConfirmButton: false,
                timer: 2000
            });
        }

        $(document).ready(function () {
            // Validasi form submit add - pastikan 3 foto sudah diambil
            $('#btn-submit-form').on('click', function(e) {
                // Webcam photos adalah opsional - boleh submit tanpa foto
                // Validation dihapus untuk membuat webcam truly optional seperti profile picture
            });

            // Form edit siswa - webcam photos opsional
            $('#formEditSiswa').on('submit', function(e) {
                // Tidak ada validasi - webcam opsional seperti profile picture
            });
            $('#example').DataTable({
                lengthChange: false,
                language: {
                    search: "Cari:",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    paginate: {
                        first: "Awal",
                        last: "Akhir",
                        next: "Berikutnya",
                        previous: "Sebelumnya"
                    }
                },
                pageLength: 10
            });

            $('#select-all').on('click', function () {
                $('.row-checkbox').prop('checked', this.checked);
            });

            $('#btn-hapus-siswa').on('click', function () {
                let checked = [];
                $('#example tbody input.row-checkbox:checked').each(function () {
                    checked.push($(this).closest('tr').data('id'));
                });

                if (checked.length === 0) {
                    Swal.fire({
                        toast: true,
                        position: 'bottom-end',
                        icon: 'warning',
                        title: 'Pilih data yang ingin dihapus.',
                        showConfirmButton: false,
                        timer: 2000
                    });
                    return;
                }

                Swal.fire({
                    title: 'Yakin ingin menghapus ' + checked.length + ' siswa?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#dc3545',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        checked.forEach(function(id) {
                            let form = document.createElement('form');
                            form.action = '{{ url("/pages/akun/indentitas_siswa") }}/' + id;
                            form.method = 'POST';
                            form.style.display = 'none';

                            let csrf = document.createElement('input');
                            csrf.type = 'hidden';
                            csrf.name = '_token';
                            csrf.value = '{{ csrf_token() }}';
                            form.appendChild(csrf);

                            let method = document.createElement('input');
                            method.type = 'hidden';
                            method.name = '_method';
                            method.value = 'DELETE';
                            form.appendChild(method);

                            document.body.appendChild(form);
                            form.submit();
                        });
                    }
                });
            });

            // ===== Kamera Absensi Modal =====
            let absensiStream = null;

            function startAbsensiCamera() {
                const video = document.getElementById('absensi-video');
                const loading = document.getElementById('absensi-loading');
                const captureBtn = document.getElementById('btn-absensi-capture');

                navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' }, audio: false })
                    .then(function(stream) {
                        absensiStream = stream;
                        video.srcObject = stream;
                        video.onloadedmetadata = function() {
                            video.play();
                            loading.style.display = 'none';
                            captureBtn.disabled = false;
                        };
                    })
                    .catch(function(err) {
                        loading.style.display = 'none';
                        document.getElementById('absensi-result').style.display = 'block';
                        document.getElementById('absensi-result-content').innerHTML =
                            '<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i>Tidak dapat mengakses kamera: ' + err.message + '</div>';
                    });
            }

            function stopAbsensiCamera() {
                if (absensiStream) {
                    absensiStream.getTracks().forEach(function(track) { track.stop(); });
                    absensiStream = null;
                }
                const video = document.getElementById('absensi-video');
                if (video) video.srcObject = null;
            }

            $('#btn-camera-siswa').on('click', function () {
                // Reset state
                document.getElementById('absensi-result').style.display = 'none';
                document.getElementById('absensi-result-content').innerHTML = '';
                document.getElementById('absensi-sending').style.display = 'none';
                document.getElementById('btn-absensi-retake').style.display = 'none';
                document.getElementById('absensi-loading').style.display = 'flex';
                document.getElementById('btn-absensi-capture').disabled = true;
                $('#modalKameraAbsensi').modal('show');
            });

            document.getElementById('modalKameraAbsensi').addEventListener('shown.bs.modal', function () {
                startAbsensiCamera();
            });

            document.getElementById('btn-absensi-capture').addEventListener('click', function () {
                const video = document.getElementById('absensi-video');
                const canvas = document.getElementById('absensi-canvas');
                const ctx = canvas.getContext('2d');

                canvas.width  = video.videoWidth  || 640;
                canvas.height = video.videoHeight || 480;
                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

                // Tampilkan spinner kirim
                document.getElementById('absensi-sending').style.display = 'block';
                document.getElementById('absensi-result').style.display = 'none';
                document.getElementById('btn-absensi-capture').disabled = true;

                canvas.toBlob(function(blob) {
                    const formData = new FormData();
                    formData.append('image', blob, 'capture.jpg');

                    $.ajax({
                        url: 'http://202.10.47.101:5000/attendance/recognize',
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            document.getElementById('absensi-sending').style.display = 'none';
                            document.getElementById('btn-absensi-retake').style.display = 'inline-block';

                            let html = '';
                            const isSuccess = response && response.status === 'success';
                            const alertClass = isSuccess ? 'alert-success' : 'alert-warning';
                            const iconClass  = isSuccess ? 'fa-check-circle' : 'fa-exclamation-triangle';

                            const nama       = response.student_name || response.name || '-';
                            const statusDisp = response.attendance_status_display || '-';
                            const confidence = response.confidence != null
                                             ? (parseFloat(response.confidence) * 100).toFixed(1) + '%'
                                             : '-';
                            const message    = response.message || '-';

                            html = '<div class="alert ' + alertClass + ' text-start mb-0">'
                                 + '<div class="fw-bold mb-2"><i class="fas ' + iconClass + ' me-2"></i>' + message + '</div>'
                                 + '<table class="table table-sm table-borderless mb-0" style="font-size:0.9rem;">'
                                 + '<tr><td class="text-muted pe-2" style="width:130px;">Nama Siswa</td>'
                                 +     '<td><strong>' + nama + '</strong></td></tr>'
                                 + '<tr><td class="text-muted pe-2">Status Kehadiran</td>'
                                 +     '<td><strong>' + statusDisp + '</strong></td></tr>'
                                 + '<tr><td class="text-muted pe-2">Confidence</td>'
                                 +     '<td><strong>' + confidence + '</strong></td></tr>'
                                 + '</table>'
                                 + '</div>';
                            document.getElementById('absensi-result-content').innerHTML = html;
                            document.getElementById('absensi-result').style.display = 'block';
                        },
                        error: function(xhr) {
                            document.getElementById('absensi-sending').style.display = 'none';
                            document.getElementById('btn-absensi-capture').disabled = false;
                            document.getElementById('btn-absensi-retake').style.display = 'inline-block';

                            let errMsg = 'Gagal menghubungi server absensi.';
                            if (xhr.responseJSON && xhr.responseJSON.message) errMsg = xhr.responseJSON.message;
                            else if (xhr.responseText) errMsg = xhr.responseText.substring(0, 200);

                            document.getElementById('absensi-result-content').innerHTML =
                                '<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i>' + errMsg + '</div>';
                            document.getElementById('absensi-result').style.display = 'block';
                        }
                    });
                }, 'image/jpeg', 0.92);
            });

            document.getElementById('btn-absensi-retake').addEventListener('click', function () {
                document.getElementById('absensi-result').style.display = 'none';
                document.getElementById('absensi-result-content').innerHTML = '';
                document.getElementById('btn-absensi-retake').style.display = 'none';
                document.getElementById('btn-absensi-capture').disabled = false;
            });
            // ===== End Kamera Absensi Modal =====

            $('#btn-dataset-siswa').on('click', function () {
                Swal.fire({
                    title: 'Latih Model CNN?',
                    text: 'Apakah Anda yakin ingin melatih model CNN? Proses ini mungkin memakan waktu beberapa saat.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Latih',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#06b6d4',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'http://presensiku.site/flaskpresensiku/pipeline/run',
                            type: 'POST',
                            contentType: 'application/json',
                            success: function(response) {
                                Swal.fire({
                                    toast: true,
                                    position: 'bottom-end',
                                    icon: 'success',
                                    title: 'Latih model cnn berhasil dijalankan',
                                    showConfirmButton: false,
                                    timer: 2000
                                });
                            },
                            error: function(error) {
                                Swal.fire({
                                    toast: true,
                                    position: 'bottom-end',
                                    icon: 'error',
                                    title: 'Gagal menjalankan latih model',
                                    showConfirmButton: false,
                                    timer: 2000
                                });
                            }
                        });
                    }
                });
            });

            $('#btn-edit-siswa').on('click', function () {
                const checked = $('.row-checkbox:checked');
                if (checked.length === 0) {
                    alert('Pilih data yang ingin diedit.');
                } else if (checked.length === 1) {
                    const tr = checked.closest('tr');
                    const id = tr.data('id');
                    const name = tr.find('td').eq(3).text().trim();
                    const email = tr.find('td').eq(4).text().trim();
                    const nisn = tr.find('td').eq(5).text().trim();
                    const kelasId = tr.find('td').eq(6).data('class-id') || '';
                    const entryYear = tr.find('td').eq(7).text().trim();

                    $('#edit-name').val(name);
                    $('#edit-email').val(email);
                    $('#edit-nisn').val(nisn);
                    $('#edit-id_class').val(kelasId);
                    $('#edit-entry_year').val(entryYear);
                    $('#formEditSiswa').attr('action', '{{ url("/pages/akun/indentitas_siswa") }}/' + id);

                    // Set preview foto profil dari baris tabel & reset input file
                    const profileImg = tr.find('td').eq(1).find('img').attr('src');
                    $('#edit-profile-preview').attr('src', profileImg || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(name));
                    $('#edit-profile_picture').val('');

                    document.getElementById('initial-container-edit').style.display = 'block';
                    document.getElementById('webcam-container-edit').style.display = 'none';
                    document.getElementById('preview-container-edit').style.display = 'none';
                    document.getElementById('accepted-container-edit').style.display = 'none';
                    document.getElementById('photos-summary-container-edit').style.display = 'none';
                    document.getElementById('webcam-photo-data-edit').value = '';

                    // Display existing photos
                    const existingPhotos = tr.find('td').eq(2).find('img');
                    document.getElementById('existing-photo-1').style.display = 'none';
                    document.getElementById('existing-photo-2').style.display = 'none';
                    document.getElementById('existing-photo-3').style.display = 'none';
                    document.getElementById('no-photo-1').style.display = 'block';
                    document.getElementById('no-photo-2').style.display = 'block';
                    document.getElementById('no-photo-3').style.display = 'block';

                    existingPhotos.each(function(index) {
                        if (index < 3) {
                            const src = $(this).attr('src');
                            document.getElementById(`existing-photo-${index + 1}`).src = src;
                            document.getElementById(`existing-photo-${index + 1}`).style.display = 'block';
                            document.getElementById(`no-photo-${index + 1}`).style.display = 'none';
                        }
                    });

                    collectedPhotosEdit = [null, null, null];
                    currentPhotoIndexEdit = 0;
                    collectedPhotosEdit = [null, null, null];
                    currentPhotoIndexEdit = 0;

                    $('#modalEditSiswa').modal('show');
                } else {
                    $('#mass-edit-class').val('');
                    $('#modalEditKelasMassal').modal('show');

                    $('#formEditKelasMassal').off('submit').on('submit', function(e) {
                        e.preventDefault();
                        const kelasBaru = $('#mass-edit-class').val();
                        if (!kelasBaru) {
                            Swal.fire('Pilih kelas terlebih dahulu!', '', 'warning');
                            return;
                        }
                        checked.each(function () {
                            const id = $(this).closest('tr').data('id');
                            let form = document.createElement('form');
                            form.action = '{{ url("/pages/akun/indentitas_siswa") }}/' + id;
                            form.method = 'POST';
                            form.style.display = 'none';

                            let csrf = document.createElement('input');
                            csrf.type = 'hidden';
                            csrf.name = '_token';
                            csrf.value = '{{ csrf_token() }}';
                            form.appendChild(csrf);

                            let method = document.createElement('input');
                            method.type = 'hidden';
                            method.name = '_method';
                            method.value = 'PUT';
                            form.appendChild(method);

                            let kelas = document.createElement('input');
                            kelas.type = 'hidden';
                            kelas.name = 'id_class';
                            kelas.value = kelasBaru;
                            form.appendChild(kelas);

                            document.body.appendChild(form);
                            form.submit();
                        });
                        $('#modalEditKelasMassal').modal('hide');
                    });
                }
            });
        });

        // Modal Cleanup
        document.getElementById('modalTambahSiswa').addEventListener('hidden.bs.modal', function() {
            stopWebcam();
            document.getElementById('initial-container').style.display = 'block';
            document.getElementById('webcam-container').style.display = 'none';
            document.getElementById('preview-container').style.display = 'none';
            document.getElementById('accepted-container').style.display = 'none';
            document.getElementById('photos-summary-container').style.display = 'none';
            document.getElementById('webcam-photo-data').value = '';
            collectedPhotos = [null, null, null];
            currentPhotoIndex = 0;
        });

        document.getElementById('modalEditSiswa').addEventListener('hidden.bs.modal', function() {
            stopWebcamEdit();
            document.getElementById('initial-container-edit').style.display = 'block';
            document.getElementById('webcam-container-edit').style.display = 'none';
            document.getElementById('preview-container-edit').style.display = 'none';
            document.getElementById('accepted-container-edit').style.display = 'none';
            document.getElementById('photos-summary-container-edit').style.display = 'none';
            document.getElementById('webcam-photo-data-edit').value = '';
            collectedPhotosEdit = [null, null, null];
            currentPhotoIndexEdit = 0;

            // Reset existing photos display
            document.getElementById('existing-photo-1').style.display = 'none';
            document.getElementById('existing-photo-2').style.display = 'none';
            document.getElementById('existing-photo-3').style.display = 'none';
            document.getElementById('no-photo-1').style.display = 'block';
            document.getElementById('no-photo-2').style.display = 'block';
            document.getElementById('no-photo-3').style.display = 'block';
        });

        // Cleanup modal kamera absensi saat ditutup
        document.getElementById('modalKameraAbsensi').addEventListener('hidden.bs.modal', function() {
            stopAbsensiCamera();
            document.getElementById('absensi-result').style.display = 'none';
            document.getElementById('absensi-result-content').innerHTML = '';
            document.getElementById('absensi-sending').style.display = 'none';
            document.getElementById('btn-absensi-retake').style.display = 'none';
            document.getElementById('absensi-loading').style.display = 'flex';
            document.getElementById('btn-absensi-capture').disabled = true;
        });
    </script>

    {{-- Flash & validation feedback (single source) --}}
    <div id="page-flash"
        data-success="{{ session('success') ?? '' }}"
        data-error="{{ session('error') ?? '' }}"
        data-open-modal="{{ session('open_modal') ?? '' }}"
        data-edit-id="{{ session('edit_id') ?? '' }}"
        data-errors='@json($errors->all())'
        style="display:none;"></div>

    <script>
        (function () {
            const flashEl = document.getElementById('page-flash');
            if (!flashEl) return;

            const successMsg = flashEl.dataset.success || '';
            const errorMsg = flashEl.dataset.error || '';
            const openModal = flashEl.dataset.openModal || '';
            let validationErrors = [];
            try {
                validationErrors = JSON.parse(flashEl.dataset.errors || '[]');
            } catch (e) {
                validationErrors = [];
            }

            document.addEventListener('DOMContentLoaded', function () {
                // Toast sukses
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
                }

                // Dialog validasi (centered) — prioritaskan ini dulu
                if (validationErrors.length > 0) {
                    validationErrors = validationErrors.map(function(msg) {
                        switch (msg) {
                            case 'The password field must be at least 6 characters.':
                                return 'Password minimal 6 karakter.';

                            case 'The nisn field must be at least 10 characters.':
                            case 'The nisn field must be 10 digits.':
                            case 'The nisn must be at least 10 characters.':
                            case 'The nisn must be 10 digits.':
                                return 'NISN minimal 10 digit.';

                            case 'The email has already been taken.':
                                return 'Email sudah digunakan.';

                            case 'The nisn has already been taken.':
                                return 'NISN sudah terdaftar.';

                            case 'The profile picture field must be an image.':
                                return 'File yang dipilih harus berupa gambar.';

                            default:
                                return msg;
                        }
                    });

                    const listHtml = '<ul class="list-unstyled text-center mb-0 p-0">' +
                        validationErrors.map(function (msg) {
                            return '<li class="mb-1">' + msg + '</li>';
                        }).join('') + '</ul>';

                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Menyimpan Data',
                        html: listHtml,
                        confirmButtonText: 'Mengerti',
                        confirmButtonColor: '#365CF5',
                        customClass: {
                            popup: 'rounded-4 shadow-lg',
                            title: 'fw-bold text-danger',
                            confirmButton: 'fw-semibold px-4'
                        }
                    }).then(function () {
                        reopenModalIfNeeded();
                    });
                } else if (errorMsg) {
                    // Toast error umum (bukan validasi)
                    Swal.fire({
                        toast: true,
                        position: 'bottom-end',
                        icon: 'error',
                        title: errorMsg,
                        showConfirmButton: false,
                        timer: 2500,
                        timerProgressBar: true
                    }).then(function () {
                        reopenModalIfNeeded();
                    });
                } else {
                    reopenModalIfNeeded();
                }
            });

            function reopenModalIfNeeded() {
                if (openModal === 'tambah') {
                    const el = document.getElementById('modalTambahSiswa');
                    if (el && typeof bootstrap !== 'undefined') {
                        bootstrap.Modal.getOrCreateInstance(el).show();
                    }
                }
                // Catatan: modal edit tidak otomatis dibuka kembali karena membutuhkan
                // data dinamis (foto, kelas, dsb) yang dimuat saat tombol edit diklik.
            }
        })();
    </script>
@endsection
