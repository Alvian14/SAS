@extends('pages.index')

@section('admin_content')
<style>
@media print {
    body * {
        visibility: hidden !important;
    }
    #qrCode, #qrCode canvas {
        visibility: visible !important;
        position: absolute !important;
        left: 50% !important;
        top: 50% !important;
        transform: translate(-50%, -50%) !important;
        width: 8cm !important;
        height: 8cm !important;
        margin: 0 !important;
        background: white !important;
        display: block !important;
    }
}
</style>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            <div id="qrCode"></div>
            <div class="mt-4 d-flex justify-content-center gap-2 flex-wrap no-print">
                <button class="btn btn-primary" id="btn-copy-link" type="button">
                    <i class="fas fa-link"></i> Copy Link
                </button>
                <button class="btn btn-success" id="btn-print" type="button">
                    <i class="fas fa-print"></i> Print
                </button>
                <button class="btn btn-danger" id="btn-download-pdf" type="button">
                    <i class="fas fa-file-pdf"></i> Unduh PDF
                </button>
                <a href="{{ route('jadwal.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali ke Jadwal
                </a>
            </div>
            <div id="copy-success" class="mt-2 text-success" style="display:none;">Link berhasil disalin!</div>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrious/4.0.2/qrious.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Generate QR
        var qr = new QRious({
            element: document.createElement('canvas'),
            value: @json($jadwal->code),
            size: 256,
        });
        document.getElementById('qrCode').appendChild(qr.element);

        // Copy Link
        document.getElementById('btn-copy-link').onclick = function() {
            const url = window.location.href;
            if (navigator.clipboard) {
                navigator.clipboard.writeText(url).then(function() {
                    document.getElementById('copy-success').style.display = 'block';
                    setTimeout(() => {
                        document.getElementById('copy-success').style.display = 'none';
                    }, 2000);
                });
            } else {
                prompt('Copy link berikut:', url);
            }
        };

        // Print
        document.getElementById('btn-print').onclick = function() {
            window.print();
        };

        // Download PDF
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
