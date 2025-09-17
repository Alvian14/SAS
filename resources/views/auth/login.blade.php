<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Log In - SAS SMK Taruna Bakti Kertosono</title>
  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" type="image/png" href="{{ asset('image/smk-taruna-bakti.png') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <style>
    body {
      background: url('{{ asset('image/foto-smk-tb.jpeg') }}') no-repeat center center fixed;
      background-size: cover;
      font-family: 'Segoe UI', sans-serif;
    }
    .login-card {
      width: 380px;
      border-radius: 15px;
      padding: 2rem;
      background: #fff;
      box-shadow: 0 5px 20px rgba(0,0,0,0.2);
      border-top: 6px solid #0086C1;
    }
    .login-title {
      color: #0086C1;
      font-weight: bold;
      letter-spacing: 1px;
    }
    .btn-login {
      background: #0086C1;
      border: 2px solid #0086C1;
      border-radius: 8px;
      font-weight: 500;
      color: #fff;
      transition: box-shadow 0.2s, border-color 0.2s, background 0.2s, color 0.2s;
      box-shadow: 0 2px 8px rgba(0,134,193,0.08);
    }
    .btn-login:hover {
      background: #006a95;
      border-color: #006a95;
      color: #fff;
    }
    .btn-login:active, .btn-login:focus {
      background: #0086C1 !important;
      border-color: #005b7f !important;
      color: #fff !important;
      outline: 2px solid #005b7f;
      box-shadow: 0 0 0 0.15rem #0086C180;
    }
    .form-control:focus {
      border-color: #0086C1;
      box-shadow: none;
    }
    .input-group-text:focus {
      border-color: #0086C1;
      box-shadow: none;
    }
    /* Tambahkan hover/focus untuk seluruh input-group */
    .input-group:hover .form-control,
    .input-group:focus-within .form-control,
    .input-group:hover .input-group-text,
    .input-group:focus-within .input-group-text {
      border-color: #0086C1 !important;
      box-shadow: none !important;
    }
    .form-check-label {
      font-size: 0.9rem;
      color: #555;
    }
    .icon-social {
      font-size: 1.8rem;
      transition: color 0.2s;
    }
    .icon-instagram {
      color: #E1306C;
    }
    .icon-facebook {
      color: #1877F3;
    }
    .icon-social:hover {
      filter: brightness(1.2);
      opacity: 0.8;
    }

  </style>
</head>
<body>
  <div style="position:fixed;top:0;left:0;width:100vw;height:100vh;z-index:0;pointer-events:none;background:rgba(0,0,0,0.45);"></div>
  <div class="d-flex vh-100 justify-content-center align-items-center" style="position:relative;z-index:1;">
    @if(session('success'))
        <div class="alert alert-success auto-dismiss position-fixed bottom-0 end-0 m-3" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger auto-dismiss position-fixed bottom-0 end-0 m-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ $errors->first() }}
        </div>
    @endif
    <div class="login-card text-center">
      <!-- Logo sekolah -->
      <div style="width:100px;height:100px;display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem auto;padding:10px;">
        <img src="{{ asset('image/smk-taruna-bakti.png') }}" alt="Logo SMK Taruna Bakti" style="width:105px;height:105px;object-fit:contain;">
      </div>

      <h5 class=" mb-4 login-title">Log In</h5>

      <!-- Form -->
      <form method="POST" action="{{ route('login.post') }}">
        @csrf
        <div class="mb-3 input-group">
          <span class="input-group-text bg-white border-end-0">
            <i class="bi bi-envelope"></i>
          </span>
          <input type="email" name="email" class="form-control border-start-0" placeholder="Email"  oninput="this.setCustomValidity('')" required value="{{ old('email') }}">
        </div>
        <div class="mb-3 input-group">
          <span class="input-group-text bg-white border-end-0">
            <i class="bi bi-lock"></i>
          </span>
          <input type="password" name="password" class="form-control border-start-0" placeholder="Password" required id="passwordInput" oninput="this.setCustomValidity('')">
        </div>

        <button  type="submit" class="mt-4 btn btn-login w-100 py-2">Get Started</button>
        <!-- Tambahkan ikon media sosial -->
        <div class="mt-4 d-flex justify-content-center gap-3">
          <a href="https://www.instagram.com/smktarunabakti.kts/" target="_blank" class="text-decoration-none">
            <i class="bi bi-instagram icon-social icon-instagram"></i>
          </a>
          <a href="https://www.facebook.com/share/9RNRGiEoXYkHbDM1/?mibextid=LQQJ4d" target="_blank" class="text-decoration-none">
            <i class="bi bi-facebook icon-social icon-facebook"></i>
          </a>
          <a href="https://www.tiktok.com/@tarunabaktikertosono?_t=8nU1efhZu0l&_r=1" target="_blank" class="text-decoration-none d-flex align-items-center">
            <i class="fa-brands fa-tiktok icon-social" style="color: #000; font-size: 1.7rem;"></i>
          </a>
        </div>
        <div class=" text-center text-muted" style="font-size:0.90rem; margin-bottom: -15px; margin-top: 8px;">
          &copy; Mahasiswa Polije 2026
        </div>
      </form>
    </div>
  </div>

  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <script>
    // Cari semua alert yang punya class "auto-dismiss"
    document.addEventListener("DOMContentLoaded", function () {
        setTimeout(function () {
            document.querySelectorAll('.auto-dismiss').forEach(function (el) {
                el.classList.remove('show'); // animasi fade
                setTimeout(() => el.remove(), 500); // hapus dari DOM setelah fade out
            });
        }, 5000); // 5 detik
    });

</script>

</body>
</html>

