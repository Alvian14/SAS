@extends('pages.index')

@section('admin_content')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
    .qr-card {
        border-radius: 24px;
        border: none;
        box-shadow: 0 8px 32px rgba(54,92,245,0.12);
        overflow: hidden;
    }
    .qr-header {
        background: linear-gradient(135deg, #365CF5 0%, #6a8ffd 100%);
        padding: 2rem;
        color: white;
    }
    .qr-body {
        padding: 2rem;
        background: #f8faff;
    }
    .qr-canvas-wrap {
        background: white;
        border-radius: 16px;
        padding: 20px;
        display: inline-block;
        box-shadow: 0 4px 16px rgba(54,92,245,0.10);
        border: 2px solid #e3eafd;
    }
    .info-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #e3eafd;
        color: #365CF5;
        border-radius: 20px;
        padding: 6px 16px;
        font-weight: 600;
        font-size: 0.9em;
        margin: 4px;
    }
    .btn-action {
        border-radius: 10px;
        font-weight: 600;
        padding: 10px 20px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
    }
    .print-only { display: none; }
    .print-title {
        font-size: 22px;
        font-weight: 700;
        letter-spacing: 0.3px;
        margin-bottom: 4px;
    }
    .print-subtitle {
        font-size: 13px;
        color: #5b6470;
        margin-bottom: 12px;
    }
    .print-meta {
        width: 100%;
        max-width: 520px;
        margin: 0 auto;
        border: 1px solid #d7deea;
        border-radius: 10px;
        background: #f8faff;
        padding: 10px 14px;
    }
    .print-row {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        padding: 5px 0;
        border-bottom: 1px dashed #dbe3f2;
        font-size: 13px;
    }
    .print-row:last-child {
        border-bottom: none;
    }
    .print-label {
        color: #4b5563;
        font-weight: 600;
    }
    .print-value {
        color: #0f172a;
        font-weight: 600;
        text-align: right;
    }
    .btn-action:hover { transform: translateY(-2px); }
    @media print {
        @page {
            size: auto;
            margin: 12mm;
        }
        body * { visibility: hidden !important; }
        #print-info,
        #print-info *,
        .qr-canvas-wrap,
        .qr-canvas-wrap *,
        #qrCode,
        #qrCode canvas {
            visibility: visible !important;
        }
        .qr-header,
        .mb-3,
        .no-print,
        #copy-success {
            display: none !important;
        }
        .qr-card,
        .qr-body {
            background: #fff !important;
            box-shadow: none !important;
            border: none !important;
        }
        .qr-body {
            padding: 0 !important;
            text-align: center !important;
        }
        #print-info {
            display: block !important;
            position: static !important;
            transform: none !important;
            width: 100% !important;
            max-width: 620px !important;
            margin: 0 auto 14px auto !important;
            text-align: center !important;
            color: #000 !important;
            font-size: 14px !important;
            line-height: 1.4 !important;
        }
        .d-flex.justify-content-center.mb-4 {
            display: block !important;
            margin: 0 !important;
        }
        .qr-canvas-wrap {
            display: inline-block !important;
            border: 1px solid #d7deea !important;
            box-shadow: none !important;
            padding: 10px !important;
            background: #fff !important;
        }
        #qrCode {
            position: static !important;
            transform: none !important;
            margin: 0 !important;
            background: white !important;
            display: block !important;
        }
        #qrCode canvas {
            width: 8.5cm !important;
            height: 8.5cm !important;
            display: block !important;
        }
        .print-meta {
            background: #fff !important;
        }
    }
</style>

<div class="container py-5">

    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <div class="card qr-card">
                <div class="qr-header text-center">
                    <h4 class="fw-bold mb-1"><i class="fas fa-calendar-check me-2"></i>QR Code Absensi</h4>
                    <p class="mb-0 opacity-75">Scan QR untuk absensi mata pelajaran</p>
                </div>
                <div class="qr-body text-center">
                    <div id="print-info" class="print-only">
                        <div class="print-title">QR Code Absensi</div>
                        <div class="print-subtitle">Silakan scan QR di bawah untuk melakukan absensi</div>
                        <div class="print-meta">
                            <div class="print-row">
                                <span class="print-label">Mapel</span>
                                <span class="print-value">{{ $jadwal->subject->name ?? '-' }}</span>
                            </div>
                            <div class="print-row">
                                <span class="print-label">Guru</span>
                                <span class="print-value">{{ $jadwal->teacher->name ?? '-' }}</span>
                            </div>
                            <div class="print-row">
                                <span class="print-label">Kelas</span>
                                <span class="print-value">{{ $jadwal->class->name ?? '-' }}</span>
                            </div>
                            <div class="print-row">
                                <span class="print-label">Hari</span>
                                <span class="print-value">{{ $jadwal->day_of_week ?? '-' }}</span>
                            </div>
                            <div class="print-row">
                                <span class="print-label">Jam</span>
                                <span class="print-value">{{ $jadwal->start_time ?? '-' }} - {{ $jadwal->end_time ?? '-' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Info Jadwal -->
                    <div class="mb-3">
                        <span class="info-badge"><i class="fas fa-book"></i> {{ $jadwal->subject->name ?? '-' }}</span>
                        <span class="info-badge"><i class="fas fa-chalkboard-teacher"></i> {{ $jadwal->teacher->name ?? '-' }}</span>
                        <span class="info-badge"><i class="fas fa-users"></i> {{ $jadwal->class->name ?? '-' }}</span>
                        <span class="info-badge"><i class="fas fa-calendar-day"></i> {{ $jadwal->day_of_week ?? '-' }}</span>
                        <span class="info-badge"><i class="fas fa-clock"></i> {{ $jadwal->start_time ?? '-' }} - {{ $jadwal->end_time ?? '-' }}</span>
                    </div>

                    <!-- QR Code -->
                    <div class="d-flex justify-content-center mb-4">
                        <div class="qr-canvas-wrap">
                            <div id="qrCode"></div>
                            {{-- <div class="mt-2 text-muted" style="font-size:0.8rem;letter-spacing:1px;">{{ $jadwal->code }}</div> --}}
                        </div>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="d-flex justify-content-center gap-2 flex-wrap no-print">
                        <button class="btn btn-primary btn-action" id="btn-copy-link" type="button">
                            <i class="fas fa-link"></i> Copy Link
                        </button>
                        <button class="btn btn-success btn-action" id="btn-print" type="button">
                            <i class="fas fa-print"></i> Print
                        </button>
                        <button class="btn btn-danger btn-action" id="btn-download-pdf" type="button">
                            <i class="fas fa-file-pdf"></i> Unduh PDF
                        </button>
                        <a href="{{ route('jadwal.index') }}" class="btn btn-secondary btn-action">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                    <div id="copy-success" class="mt-3 text-success fw-semibold" style="display:none;">
                        <i class="fas fa-check-circle me-1"></i> Link berhasil disalin!
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrious/4.0.2/qrious.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var qr = new QRious({
            element: document.createElement('canvas'),
            value: @json($jadwal->code),
            size: 300,
        });
        document.getElementById('qrCode').appendChild(qr.element);

        document.getElementById('btn-copy-link').onclick = function() {
            const url = window.location.href;
            if (navigator.clipboard) {
                navigator.clipboard.writeText(url).then(function() {
                    document.getElementById('copy-success').style.display = 'block';
                    setTimeout(() => { document.getElementById('copy-success').style.display = 'none'; }, 2000);
                });
            } else {
                prompt('Copy link berikut:', url);
            }
        };

        document.getElementById('btn-print').onclick = function() { window.print(); };

        document.getElementById('btn-download-pdf').onclick = function() {
            var element = document.getElementById('qrCode');
            html2pdf().set({
                margin: 10,
                filename: 'qr-jadwal-{{ $jadwal->id }}.pdf',
                html2canvas: { scale: 2 },
                jsPDF: { unit: 'mm', format: 'a5', orientation: 'portrait' }
            }).from(element).save();
        };
    });
</script>
@endsection
