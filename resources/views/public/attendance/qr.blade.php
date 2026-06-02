<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Absensi - SAS</title>
    <link rel="shortcut icon" href="{{ asset('image/smk-taruna-bakti.png') }}" type="image/x-icon" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: #f5f8ff;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
            font-family: 'Segoe UI', sans-serif;
        }
        .qr-card {
            border-radius: 24px;
            border: none;
            box-shadow: 0 8px 32px rgba(54,92,245,0.12);
            overflow: hidden;
            width: 100%;
            max-width: 600px;
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
</head>
<body>
    <div class="qr-card">
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

            <!-- Expiration Timer -->
            <div class="mb-3 no-print">
                <div class="alert alert-warning d-inline-block" style="border-radius: 10px; padding: 8px 16px; margin: 0;">
                    <i class="fas fa-hourglass-end"></i> 
                    <span>Expired dalam <strong id="countdown">5:00</strong> menit</span>
                </div>
            </div>

            <!-- QR Code -->
            <div class="d-flex justify-content-center mb-4">
                <div class="qr-canvas-wrap">
                    <div id="qrCode"></div>
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="d-flex justify-content-center gap-2 flex-wrap no-print">
                <button class="btn btn-primary btn-action" id="btn-copy-link" type="button">
                    <i class="fas fa-link"></i> Copy Link
                </button>
            </div>
            <div id="copy-success" class="mt-3 text-success fw-semibold" style="display:none;">
                <i class="fas fa-check-circle me-1"></i> Link berhasil disalin!
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
                const url = @json($publicUrl);
                if (navigator.clipboard) {
                    navigator.clipboard.writeText(url).then(function() {
                        document.getElementById('copy-success').style.display = 'block';
                        setTimeout(() => { document.getElementById('copy-success').style.display = 'none'; }, 2000);
                    });
                } else {
                    prompt('Copy link berikut:', url);
                }
            };

            // Countdown timer
            const expiresAt = new Date(@json($expiresAt)).getTime();
            const countdownEl = document.getElementById('countdown');
            
            function updateCountdown() {
                const now = new Date().getTime();
                const timeLeft = expiresAt - now;
                
                if (timeLeft <= 0) {
                    countdownEl.textContent = '0:00';
                    return;
                }
                
                const minutes = Math.floor(timeLeft / 60000);
                const seconds = Math.floor((timeLeft % 60000) / 1000);
                countdownEl.textContent = minutes + ':' + (seconds < 10 ? '0' : '') + seconds;
            }
            
            updateCountdown();
            setInterval(updateCountdown, 1000);

        });
    </script>
</body>
</html>