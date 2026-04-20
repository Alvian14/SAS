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
                                    <img src="{{ asset('storage/student/' . $student->user->profile_picture) }}" alt="Foto Profil" width="36" height="36" class="rounded-circle border border-2 border-primary shadow-sm">
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
                            <input type="text" name="name" class="form-control border-2" placeholder="Masukkan nama lengkap" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">
                                Email
                            </label>
                            <input type="email" name="email" class="form-control border-2" placeholder="contoh@email.com" required>
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
                            <input type="number" name="nisn" class="form-control border-2" placeholder="Nomor Induk Siswa" required>
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
                                    <option value="{{ $kelas->id }}">{{ $kelas->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">
                                Tahun Masuk
                            </label>
                            <input type="number" name="entry_year" class="form-control border-2" placeholder="2024" min="2015" max="2030" required>
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
        });
        document.getElementById('btn-capture-edit').addEventListener('click', function(e) {
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
                const photosData = document.getElementById('webcam-photo-data').value;

                if (!photosData) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Foto Webcam Diperlukan',
                        text: 'Silakan ambil 3 foto webcam sebelum menyimpan data',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    });
                    return false;
                }

                const photos = photosData.split('|||').filter(p => p.trim() !== '');
                if (photos.length < 3) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Foto Tidak Lengkap',
                        text: `Anda harus mengambil 3 foto. Foto yang tersimpan: ${photos.length}/3`,
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    });
                    return false;
                }
            });

            // Validasi form submit edit - pastikan 3 foto sudah diambil
            $('#formEditSiswa').on('submit', function(e) {
                const photosData = document.getElementById('webcam-photo-data-edit').value;

                if (!photosData) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Foto Webcam Diperlukan',
                        text: 'Silakan ambil 3 foto webcam sebelum menyimpan data',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    });
                    return false;
                }

                const photos = photosData.split('|||').filter(p => p.trim() !== '');
                if (photos.length < 3) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Foto Tidak Lengkap',
                        text: `Anda harus mengambil 3 foto. Foto yang tersimpan: ${photos.length}/3`,
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    });
                    return false;
                }
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
    </script>

    @if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                toast: true,
                position: 'bottom-end',
                icon: 'success',
                title: '{{ session('success') }}',
                showConfirmButton: false,
                timer: 2500,
                timerProgressBar: true
            });
        });
    </script>
    @endif

    @if(session('error'))
    <scrip>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                toast: true,
                position: 'bottom-end',
                icon: 'error',
                title: '{{ session('error') }}',
                showConfirmButton: false,
                timer: 2500,
                timerProgressBar: true
            });
        });
    </script>
    @endif


    @if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                toast: true,
                position: 'bottom-end',
                icon: 'success',
                title: '{{ session('success') }}',
                showConfirmButton: false,
                timer: 2500,
                timerProgressBar: true
            });
        });
    </script>
    @endif

    @if(session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                toast: true,
                position: 'bottom-end',
                icon: 'error',
                title: '{{ session('error') }}',
                showConfirmButton: false,
                timer: 2500,
                timerProgressBar: true
            });
        });
    </script>
    @endif

    <!-- Webcam Script -->
    <script>
        let webcamStream = null;
        let webcamCanvas = null;
        let collectedPhotos = [null, null, null]; // Array untuk 3 foto
        let currentPhotoIndex = 0; // Index untuk photo yang sedang diambil (0, 1, 2)

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

        function startWebcam() {
            const video = document.getElementById('webcam-video');

            const constraints = {
                video: {
                    width: { ideal: 400 },
                    height: { ideal: 400 }
                },
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

                // Simpan foto ke array
                collectedPhotos[currentPhotoIndex] = photoData;

                // Tampilkan preview di accepted-container
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
                // Semua 3 foto sudah diambil, tampilkan summary
                showPhotosSummary();
            }
        }

        function showPhotosSummary() {
            // Tampilkan semua 3 foto di summary
            for (let i = 0; i < 3; i++) {
                if (collectedPhotos[i]) {
                    document.getElementById(`summary-photo-${i + 1}`).src = collectedPhotos[i];
                }
            }

            // Simpan semua foto ke hidden input (comma-separated)
            const photosString = collectedPhotos.map(photo => {
                if (photo) {
                    return photo;
                }
                return '';
            }).join('|||'); // Gunakan ||| sebagai delimiter

            document.getElementById('webcam-photo-data').value = photosString;

            document.getElementById('accepted-container').style.display = 'none';
            document.getElementById('photos-summary-container').style.display = 'block';
        }

        function deletePhotoFromSummary(btn) {
            const photoNum = parseInt(btn.getAttribute('data-photo')) - 1;
            collectedPhotos[photoNum] = null;

            // Hide foto yang dihapus
            document.getElementById(`summary-photo-${photoNum + 1}`).style.opacity = '0.5';
            btn.style.display = 'none';

            // Update hidden input
            const photosString = collectedPhotos.map(photo => {
                if (photo) {
                    return photo;
                }
                return '';
            }).join('|||');

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
    </script>
@endsection
