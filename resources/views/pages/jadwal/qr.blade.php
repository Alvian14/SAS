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
    .btn-action:hover { transform: translateY(-2px); }
    @media print {
        body * { visibility: hidden !important; }
        #qrCode, #qrCode canvas {
            visibility: visible !important;
            position: absolute !important;
            left: 50% !important; top: 50% !important;
            transform: translate(-50%, -50%) !important;
            width: 8cm !important; height: 8cm !important;
            margin: 0 !important; background: white !important;
            display: block !important;
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
            size: 220,
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
