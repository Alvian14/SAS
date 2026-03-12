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
                            <th class="text-center" style="width:60px;">Foto</th>
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
                                    <img src="{{ asset('storage/student/' . $student->user->profile_picture) }}" alt="Foto" width="36" height="36" class="rounded-circle border border-2 border-primary shadow-sm">
                                @else
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($student->name) }}" alt="Foto" width="36" height="36" class="rounded-circle border border-2 border-primary shadow-sm">
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
                </div>
                <div class="modal-footer bg-white border-0 p-4 gap-2">
                    <button type="button" class="btn btn-danger px-4 fw-semibold shadow-sm" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-primary px-4 fw-semibold shadow-sm">
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
                                Foto Profil
                                <small class="text-muted">(opsional)</small>
                            </label>
                            <input type="file" name="profile_picture" class="form-control border-2" accept="image/*">
                            <div class="form-text text-muted">
                                Format: JPG, PNG, GIF. Maksimal 2MB.
                            </div>
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
        $(document).ready(function () {
            $('#example').DataTable({
                lengthChange: false, // Nonaktifkan "Show entries"
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
                pageLength: 10 // Jumlah data default per halaman
            });

            // Fungsi untuk memilih semua checkbox
            $('#select-all').on('click', function () {
                $('.row-checkbox').prop('checked', this.checked);
            });

            // Button hapus dengan SweetAlert dan form submit (mirip logout di index)
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
                            // Buat form dinamis seperti logout di index
                            let form = document.createElement('form');
                            form.action = '{{ route("akun.indentitas_siswa.destroy", ":id") }}'.replace(':id', id);
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

            // Button edit
            $('#btn-edit-siswa').on('click', function () {
                const checked = $('.row-checkbox:checked');
                if (checked.length === 0) {
                    alert('Pilih data yang ingin diedit.');
                } else if (checked.length === 1) {
                    // Ambil data dari baris yang dicentang
                    const tr = checked.closest('tr');
                    const id = tr.data('id');
                    const name = tr.find('td').eq(2).text().trim();
                    const email = tr.find('td').eq(3).text().trim();
                    const nisn = tr.find('td').eq(4).text().trim();
                    const kelasId = tr.find('td').eq(5).data('class-id') || '';
                    const entryYear = tr.find('td').eq(6).text().trim();

                    // Isi form modal edit
                    $('#edit-name').val(name);
                    $('#edit-email').val(email);
                    $('#edit-nisn').val(nisn);
                    $('#edit-id_class').val(kelasId);
                    $('#edit-entry_year').val(entryYear);
                    $('#formEditSiswa').attr('action', '{{ url("/pages/akun/indentitas_siswa") }}/' + id);
                    $('#modalEditSiswa').modal('show');
                } else {
                    // Tampilkan modal edit kelas massal
                    $('#mass-edit-class').val('');
                    $('#modalEditKelasMassal').modal('show');

                    // Submit massal
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

@endsection
