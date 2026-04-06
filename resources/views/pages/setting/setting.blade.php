@extends('pages.index')

@section('admin_content')

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<style>
		:root {
			--brand: #0086c1;
			--brand-dark: #006a95;
			--card-radius: 16px;
		}

		body {
			background: linear-gradient(135deg, #f4fbff 0%, #eef5ff 50%, #f8fafc 100%);
			min-height: 100vh;
			font-family: 'Segoe UI', sans-serif;
			color: #1f2937;
		}

		.setting-wrap {
			max-width: 1080px;
			margin: 2rem auto;
			padding: 0 1rem 1.5rem;
		}

		.setting-header {
			margin-bottom: 1.2rem;
		}

		.setting-title {
			color: var(--brand);
			font-weight: 700;
			letter-spacing: 0.3px;
			margin: 0;
		}

		.setting-subtitle {
			color: #6b7280;
			margin: 0.35rem 0 0;
			font-size: 0.95rem;
		}

		.setting-card {
			background: #fff;
			border: 1px solid #eaf0f6;
			border-radius: var(--card-radius);
			box-shadow: 0 10px 30px rgba(0, 49, 76, 0.07);
			height: 100%;
		}

		.setting-card .card-body {
			padding: 1.4rem;
		}

		.card-title {
			color: #0f172a;
			font-weight: 700;
			margin-bottom: 0.3rem;
		}

		.card-desc {
			color: #64748b;
			font-size: 0.9rem;
			margin-bottom: 1rem;
		}

		.avatar-preview {
			width: 88px;
			height: 88px;
			border-radius: 50%;
			object-fit: cover;
			border: 3px solid #dbeafe;
			box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08);
			display: block;
			margin-bottom: 0.7rem;
		}

		.btn-brand {
			background: var(--brand);
			border: 2px solid var(--brand);
			color: #fff;
			font-weight: 600;
			border-radius: 10px;
			padding: 0.55rem 1rem;
		}

		.btn-brand:hover {
			background: var(--brand-dark);
			border-color: var(--brand-dark);
			color: #fff;
		}

		.form-control:focus {
			border-color: var(--brand);
			box-shadow: 0 0 0 0.12rem rgba(0, 134, 193, 0.2);
		}

		.input-group-text {
			background: #f8fafc;
		}

		@media (max-width: 767.98px) {
			.setting-wrap {
				margin-top: 1.2rem;
			}

			.setting-card .card-body {
				padding: 1.1rem;
			}
		}
</style>

<div class="setting-wrap">
		<div class="setting-header">
			<h4 class="setting-title">Setting Akun</h4>
			<p class="setting-subtitle">Kelola email, foto profil, dan password akun Anda.</p>
		</div>

		@if (session('success'))
			<div class="alert alert-success" role="alert">
				{{ session('success') }}
			</div>
		@endif

		@if ($errors->any())
			<div class="alert alert-danger" role="alert">
				{{ $errors->first() }}
			</div>
		@endif

		<div class="row g-3">
			<div class="col-12 col-lg-7">
				<div class="setting-card card">
					<div class="card-body">
						<h5 class="card-title"><i class="bi bi-person-gear me-2"></i>Ganti Email & Gambar</h5>
						<p class="card-desc">Perbarui email aktif dan foto profil akun.</p>

						<form method="POST" action="{{ url()->current() }}" enctype="multipart/form-data">
							@csrf
							<input type="hidden" name="form_type" value="profile">

							<div class="mb-3">
								@php
									$defaultAvatar = 'https://ui-avatars.com/api/?name=AD&background=0086c1&color=fff';
									$currentAvatar = $defaultAvatar;
									if (isset($user) && !empty($user->profile_picture)) {
										$profilePath = public_path('storage/profile/' . $user->profile_picture);

										if (file_exists($profilePath)) {
											$currentAvatar = asset('storage/profile/' . $user->profile_picture);
										}
									}
								@endphp
								<img id="avatarPreview" src="{{ $currentAvatar }}" alt="Preview Foto" class="avatar-preview">
								<label for="profile_picture" class="form-label">Gambar Profil</label>
								<input type="file" class="form-control" id="profile_picture" name="profile_picture" accept="image/*">
								@if(isset($user) && !empty($user->profile_picture))
									<button
										type="submit"
										class="btn btn-outline-danger btn-sm mt-2"
										formaction="{{ route('setting.photo.delete') }}"
										formmethod="POST"
										onclick="return confirm('Hapus foto profil sekarang?')"
									>
										<i class="bi bi-trash me-1"></i>Hapus Foto
									</button>
								@endif
							</div>

							<div class="mb-3">
								<label for="email" class="form-label">Email</label>
								<div class="input-group">
									<span class="input-group-text"><i class="bi bi-envelope"></i></span>
									<input
										type="email"
										class="form-control"
										id="email"
										name="email"
										placeholder="Masukkan email baru"
										value="{{ old('email', isset($user) ? $user->email : '') }}"
										required
									>
								</div>
							</div>

							<button type="submit" class="btn btn-brand w-100">Simpan Perubahan Profil</button>
						</form>
					</div>
				</div>
			</div>

			<div class="col-12 col-lg-5">
				<div class="setting-card card">
					<div class="card-body">
						<h5 class="card-title"><i class="bi bi-shield-lock me-2"></i>Ganti Password</h5>
						<p class="card-desc">Gunakan password yang kuat dan mudah Anda ingat.</p>

						<form method="POST" action="{{ url()->current() }}">
							@csrf
							<input type="hidden" name="form_type" value="password">

							<div class="mb-3">
								<label for="current_password" class="form-label">Password Saat Ini</label>
								<input type="password" class="form-control" id="current_password" name="current_password" placeholder="Masukkan password saat ini" required>
							</div>

							<div class="mb-3">
								<label for="new_password" class="form-label">Password Baru</label>
								<input type="password" class="form-control" id="new_password" name="new_password" placeholder="Masukkan password baru" required>
							</div>

							<div class="mb-3">
								<label for="new_password_confirmation" class="form-label">Konfirmasi Password Baru</label>
								<input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" placeholder="Ulangi password baru" required>
							</div>

							<button type="submit" class="btn btn-brand w-100">Simpan Password Baru</button>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>

<script>
	const uploadInput = document.getElementById('profile_picture');
	const avatarPreview = document.getElementById('avatarPreview');

	if (uploadInput && avatarPreview) {
		uploadInput.addEventListener('change', function (event) {
			const file = event.target.files && event.target.files[0];
			if (!file) return;

			const reader = new FileReader();
			reader.onload = function (e) {
				avatarPreview.src = e.target.result;
			};
			reader.readAsDataURL(file);
		});
	}
</script>

@endsection
