<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Verifikasi Token - SAS SMK Taruna Bakti Kertosono</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
	<link rel="icon" type="image/png" href="{{ asset('image/smk-taruna-bakti.png') }}">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
	<style>
		body {
			background: url('{{ asset('image/foto-smk-tb.jpeg') }}') no-repeat center center fixed;
			background-size: cover;
			font-family: 'Segoe UI', sans-serif;
		}
		.overlay {
			position: fixed;
			top: 0;
			left: 0;
			width: 100vw;
			height: 100vh;
			z-index: 0;
			pointer-events: none;
			background: rgba(0, 0, 0, 0.45);
		}
		.verify-card {
			width: 380px;
			border-radius: 15px;
			padding: 2rem;
			background: #fff;
			box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
			border-top: 6px solid #0086C1;
		}
		.verify-title {
			color: #0086C1;
			font-weight: 700;
			letter-spacing: 1px;
		}
		.verify-desc {
			font-size: 0.92rem;
			color: #666;
			margin-bottom: 1rem;
		}
		.form-control:focus {
			border-color: #0086C1;
			box-shadow: none;
		}
		.input-group:hover .form-control,
		.input-group:focus-within .form-control,
		.input-group:hover .input-group-text,
		.input-group:focus-within .input-group-text {
			border-color: #0086C1 !important;
			box-shadow: none !important;
		}
		.btn-submit {
			background: #0086C1;
			border: 2px solid #0086C1;
			color: #fff;
			border-radius: 8px;
			font-weight: 500;
		}
		.btn-submit:hover {
			background: #006a95;
			border-color: #006a95;
			color: #fff;
		}
	</style>
</head>
<body>
	<div class="overlay"></div>
	<div class="d-flex min-vh-100 justify-content-center align-items-center" style="position:relative;z-index:1;">
		<div class="verify-card text-center">
			<div style="width:100px;height:100px;display:flex;align-items:center;justify-content:center;margin:0 auto 1.25rem auto;padding:10px;">
				<img src="{{ asset('image/smk-taruna-bakti.png') }}" alt="Logo SMK Taruna Bakti" style="width:100px;height:100px;object-fit:contain;">
			</div>

			<h5 class="verify-title mb-2">Verifikasi Token</h5>
			<p class="verify-desc text-start">
				Masukkan token yang telah dikirim ke email Anda. Token berlaku selama 60 menit.
			</p>

			@if (session('status'))
				<div class="alert alert-success text-start" role="alert">
					{{ session('status') }}
				</div>
			@endif

			@if ($errors->any())
				<div class="alert alert-danger text-start" role="alert">
					{{ $errors->first() }}
				</div>
			@endif

			<form method="POST" action="{{ route('password.verify.token') }}">
				@csrf
				<input type="hidden" name="email" value="{{ $email }}">

				<div class="mb-3 input-group">
					<span class="input-group-text bg-white border-end-0">
						<i class="bi bi-key"></i>
					</span>
					<input
						type="text"
						name="token"
						class="form-control border-start-0"
						placeholder="Masukkan token"
						required
						autocomplete="off"
					>
				</div>

				<button type="submit" class="btn btn-submit w-100 py-2">Verifikasi</button>
				<a href="{{ route('password.request') }}" class="d-inline-block mt-3 text-decoration-none small text-primary">Kembali</a>
			</form>
		</div>
	</div>
</body>
</html>
