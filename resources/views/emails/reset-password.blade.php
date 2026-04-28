<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #0086C1 0%, #006a95 100%);
            color: #fff;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }
        .content {
            padding: 30px;
        }
        .content p {
            margin: 0 0 15px 0;
            font-size: 14px;
        }
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        .reset-button {
            background-color: #0086C1;
            color: #fff;
            padding: 12px 30px;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }
        .reset-button:hover {
            background-color: #006a95;
        }
        .token-info {
            background-color: #f8f9fa;
            border-left: 4px solid #0086C1;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .token-info p {
            margin: 5px 0;
            font-size: 13px;
        }
        .token-code {
            background-color: #fff;
            border: 1px solid #dee2e6;
            padding: 10px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            word-break: break-all;
            font-size: 12px;
            color: #0086C1;
            margin-top: 10px;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #dee2e6;
        }
        .warning {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .warning p {
            margin: 5px 0;
            font-size: 13px;
            color: #856404;
        }
        .logo {
            display: inline-block;
            margin-bottom: 15px;
        }
        .logo img {
            max-width: 80px;
            height: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">
                <img src="{{ asset('image/smk-taruna-bakti.png') }}" alt="Logo SMK Taruna Bakti">
            </div>
            <h1>Reset Password</h1>
            <p style="margin: 10px 0 0 0; opacity: 0.9;">SAS SMK Taruna Bakti Kertosono</p>
        </div>

        <div class="content">
            <p>Halo,</p>

            <p>
                Kami menerima permintaan untuk mereset password akun Anda pada <strong>{{ now()->format('d F Y H:i') }}</strong>.
                Jika Anda tidak membuat permintaan ini, abaikan email ini.
            </p>

            <p>
                Untuk melanjutkan proses reset password, silakan klik tombol di bawah ini:
            </p>

            <div class="button-container">
                <a href="{{ $resetLink }}" class="reset-button">Reset Password Saya</a>
            </div>

            <p style="text-align: center; color: #666; font-size: 12px;">
                Atau copy link berikut ke browser Anda:
            </p>
            <div class="token-code">
                {{ $resetLink }}
            </div>

            <div class="token-info">
                <p><strong>⚠️ Token Reset Password Anda:</strong></p>
                <p>Gunakan token di bawah saat diminta di aplikasi:</p>
                <div class="token-code">{{ $token }}</div>
                <p style="margin-top: 10px; color: #dc3545;">
                    <strong>⏱️ Token berlaku selama 60 menit saja.</strong>
                </p>
            </div>

            <div class="warning">
                <p>
                    <strong>⚠️ Keamanan:</strong> Jangan bagikan token ini kepada siapa pun.
                    Kami tidak akan pernah meminta Anda mengirimkan token melalui email atau pesan lainnya.
                </p>
            </div>

            <p>
                Jika Anda mengalami masalah atau pertanyaan, silakan hubungi tim dukungan kami.
            </p>

            <p style="margin-top: 30px;">
                Terima kasih,<br>
                <strong>Tim SAS SMK Taruna Bakti Kertosono</strong>
            </p>
        </div>

        <div class="footer">
            <p>
                © {{ now()->year }} SMK Taruna Bakti Kertosono. Semua hak dilindungi.
            </p>
            <p style="margin-top: 10px; color: #999;">
                Ini adalah email otomatis, silakan jangan membalas email ini.
            </p>
        </div>
    </div>
</body>
</html>
