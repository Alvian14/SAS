@extends('pages.index')
@section('admin_content')


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
        .container {
            padding: 2rem 0;
        }

        .grade-title {
            color: rgb(87, 87, 87);
            font-weight: 700;
            font-size: 2rem;
            margin-bottom: 2rem;
            text-shadow: none;
        }

        .class-card {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            color: #334155;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            height: 280px;
            cursor: pointer;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 20px rgba(54, 92, 245, 0.08);
        }

        .class-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(54, 92, 245, 0.15);
            border-color: #365CF5;
        }

        .class-card.tkj {
            background: linear-gradient(135deg, #64748b 0%, #94a3b8 100%);
            color: white;
        }

        .class-card.tav {
            background: linear-gradient(135deg, #059669 0%, #34d399 100%);
            color: white;
        }

        .class-card.multimedia {
            background: linear-gradient(135deg, #dc2626 0%, #f87171 100%);
            color: white;
        }

        .class-card.akuntansi {
            background: linear-gradient(135deg, #7c3aed 0%, #a78bfa 100%);
            color: white;
        }

        .class-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
            z-index: 1;
        }

        .class-card::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -30%;
            width: 60%;
            height: 60%;
            background: rgba(255,255,255,0.03);
            border-radius: 50%;
            z-index: 1;
        }

        .card-content {
            position: relative;
            z-index: 2;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .class-icon {
            width: 80px;
            height: 80px;
            background: rgba(255,255,255,0.15);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255,255,255,0.2);
        }

        .class-icon i {
            font-size: 2rem;
            color: white;
        }

        .class-number {
            font-size: 3rem;
            font-weight: 900;
            margin-bottom: 0.5rem;
            text-shadow: none;
        }

        .class-name {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            opacity: 0.95;
        }

        .major-name {
            font-size: 0.9rem;
            font-weight: 500;
            background: rgba(255,255,255,0.15);
            padding: 0.3rem 1rem;
            border-radius: 20px;
            display: inline-block;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
        }

        .status-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: rgba(255,255,255,0.9);
            color: #365CF5;
            padding: 0.3rem 0.8rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
            z-index: 3;
        }

        .status-badge.active {
            background: rgba(255,255,255,0.95);
            color: #365CF5;
        }

        .decorative-elements {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            pointer-events: none;
            z-index: 1;
        }

        .circle-1 {
            position: absolute;
            top: 10%;
            right: 10%;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255,255,255,0.2);
            border-radius: 50%;
        }

        .circle-2 {
            position: absolute;
            bottom: 20%;
            left: 10%;
            width: 15px;
            height: 15px;
            background: rgba(255,255,255,0.15);
            border-radius: 50%;
        }

        .square {
            position: absolute;
            top: 60%;
            right: 15%;
            width: 12px;
            height: 12px;
            background: rgba(255,255,255,0.15);
            border-radius: 3px;
            transform: rotate(45deg);
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        .floating {
            animation: float 3s ease-in-out infinite;
        }

        .section-divider {
            margin: 3rem 0;
        }
</style>
<div class="container-fluid">
    <!-- ========== title-wrapper start ========== -->
    <div class="title-wrapper pt-30">
        <div class="row align-items-start">
            <div class="col-md-6">
                <div class="title">
                    <h2 style="font-weight: 500;">Kelas</h2> <!-- Kurangi ketebalan judul -->
                </div>
            </div>
            <div class="col-md-6">
                <div class="breadcrumb-wrapper">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard.index') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                Kelas
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- ========== title-wrapper end ========== -->

    <!-- Button Tambah Kelas -->
    <div class="mb-3 text-end">
        <button class="btn btn-primary btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalTambahKelas">
            <i class="fas fa-plus text-white me-2"></i>
            Tambah Kelas
        </button>
    </div>
    <hr class="my-4"> <!-- Garis horizontal pembatas kelas 10, 11, 12 -->

    <!-- Modal Tambah Kelas -->
    <div class="modal fade" id="modalTambahKelas" tabindex="-1" aria-labelledby="modalTambahKelasLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content shadow-lg border-0 rounded-4">
                <div class="modal-header bg-primary text-white border-0 rounded-top-4">
                    <h5 class="modal-title fw-bold" id="modalTambahKelasLabel">
                        <i class="fas fa-plus me-2"></i>Tambah Kelas
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('kelas.store') }}">
                    @csrf
                    <div class="modal-body bg-light">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Kelas</label>
                            <input type="text" name="name" class="form-control" placeholder="Contoh: 10 TKJ 1" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Jurusan</label>
                            <input type="text" name="major" class="form-control" placeholder="Contoh: Teknik Komputer dan Jaringan" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tingkat</label>
                            <input type="number" name="grade" class="form-control" placeholder="Contoh: 10" min="10" max="12" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Kode Kelas</label>
                            <input type="text" name="code" class="form-control" placeholder="Contoh: TKJ" required>
                        </div>
                    </div>
                    <div class="modal-footer bg-white border-0 p-3">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Batal
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    @php
        $kelas10 = $classes->where('grade', 10);
        $kelas11 = $classes->where('grade', 11);
        $kelas12 = $classes->where('grade', 12);
    @endphp

    <div class="container">
        <!-- Kelas 10 -->
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="grade-title">
                    <i class="fas fa-graduation-cap me-3"></i>
                    KELAS 10
                </h2>
            </div>
            @foreach($kelas10 as $kelas)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="class-card {{ strtolower($kelas->code) }} floating">
                    <div class="status-badge active">Aktif</div>
                    <div class="decorative-elements">
                        <div class="circle-1"></div>
                        <div class="circle-2"></div>
                        <div class="square"></div>
                    </div>
                    <div class="card-content">
                        <div>
                            <div class="class-icon">
                                @if(strtolower($kelas->code) == 'tkj')
                                    <i class="fas fa-network-wired"></i>
                                @elseif(strtolower($kelas->code) == 'tav')
                                    <i class="fas fa-video"></i>
                                @elseif(strtolower($kelas->code) == 'mm')
                                    <i class="fas fa-palette"></i>
                                @elseif(strtolower($kelas->code) == 'ak')
                                    <i class="fas fa-calculator"></i>
                                @else
                                    <i class="fas fa-school"></i>
                                @endif
                            </div>
                            <div class="class-number">{{ $kelas->grade }}</div>
                            <div class="class-name">{{ $kelas->name }}</div>
                        </div>
                        <div class="major-name">{{ $kelas->major }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="section-divider"></div>

        <!-- Kelas 11 -->
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="grade-title">
                    <i class="fas fa-graduation-cap me-3"></i>
                    KELAS 11
                </h2>
            </div>
            @foreach($kelas11 as $kelas)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="class-card {{ strtolower($kelas->code) }} floating">
                    <div class="status-badge active">Aktif</div>
                    <div class="decorative-elements">
                        <div class="circle-1"></div>
                        <div class="circle-2"></div>
                        <div class="square"></div>
                    </div>
                    <div class="card-content">
                        <div>
                            <div class="class-icon">
                                @if(strtolower($kelas->code) == 'tkj')
                                    <i class="fas fa-network-wired"></i>
                                @elseif(strtolower($kelas->code) == 'tav')
                                    <i class="fas fa-video"></i>
                                @elseif(strtolower($kelas->code) == 'mm')
                                    <i class="fas fa-palette"></i>
                                @elseif(strtolower($kelas->code) == 'ak')
                                    <i class="fas fa-calculator"></i>
                                @else
                                    <i class="fas fa-school"></i>
                                @endif
                            </div>
                            <div class="class-number">{{ $kelas->grade }}</div>
                            <div class="class-name">{{ $kelas->name }}</div>
                        </div>
                        <div class="major-name">{{ $kelas->major }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="section-divider"></div>

        <!-- Kelas 12 -->
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="grade-title">
                    <i class="fas fa-graduation-cap me-3"></i>
                    KELAS 12
                </h2>
            </div>
            @foreach($kelas12 as $kelas)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="class-card {{ strtolower($kelas->code) }} floating">
                    <div class="status-badge active">Aktif</div>
                    <div class="decorative-elements">
                        <div class="circle-1"></div>
                        <div class="circle-2"></div>
                        <div class="square"></div>
                    </div>
                    <div class="card-content">
                        <div>
                            <div class="class-icon">
                                @if(strtolower($kelas->code) == 'tkj')
                                    <i class="fas fa-network-wired"></i>
                                @elseif(strtolower($kelas->code) == 'tav')
                                    <i class="fas fa-video"></i>
                                @elseif(strtolower($kelas->code) == 'mm')
                                    <i class="fas fa-palette"></i>
                                @elseif(strtolower($kelas->code) == 'ak')
                                    <i class="fas fa-calculator"></i>
                                @else
                                    <i class="fas fa-school"></i>
                                @endif
                            </div>
                            <div class="class-number">{{ $kelas->grade }}</div>
                            <div class="class-name">{{ $kelas->name }}</div>
                        </div>
                        <div class="major-name">{{ $kelas->major }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

</div>
@endsection

