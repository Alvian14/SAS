<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Absensi - Error</title>
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
        .qr-body {
            padding: 2rem;
            background: #f8faff;
            text-center;
        }
        .error-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            display: block;
        }
        .error-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: #0f172a;
        }
        .error-message {
            font-size: 1rem;
            color: #5b6470;
            margin-bottom: 2rem;
            line-height: 1.6;
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
        .btn-action:hover {
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="qr-card">
        <div class="qr-body">
            @if($status === 'expired')
                <i class="fas fa-hourglass-end error-icon" style="color: #f59e0b;"></i>
                <div class="error-title">Link Sudah Expired</div>
                <div class="error-message">
                    {{ $message }}
                </div>
            @else
                <i class="fas fa-exclamation-triangle error-icon" style="color: #ef4444;"></i>
                <div class="error-title">Data Tidak Ditemukan</div>
                <div class="error-message">
                    {{ $message }}
                </div>
            @endif
            
            <a href="javascript:history.back()" class="btn btn-primary btn-action">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
</body>
</html>
