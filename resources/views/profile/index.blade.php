@extends('layouts.app')

@section('title', 'Profil Pengguna')

@section('content')
<div class="mb-4">
    <h3 class="fw-bold m-0 text-dark">Profil & Pengaturan Akun</h3>
    <p class="text-muted small m-0">Kelola identitas pengguna dan keamanan kata sandi akun StockFlow Anda.</p>
</div>

<div class="row g-4">
    <div class="col-12 col-md-4">
        <div class="card-custom p-4 text-center">
            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-3 shadow" style="width: 80px; height: 80px; font-size: 2rem;">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <h5 class="fw-bold text-dark mb-1">{{ $user->name }}</h5>
            <div class="text-muted small mb-2">{{ $user->email }}</div>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 text-capitalize">
                Peran: {{ $user->role }}
            </span>
            <div class="border-top mt-3 pt-3 text-start small text-muted">
                <div class="mb-2"><i class="bi bi-telephone me-2"></i>{{ $user->phone ?? 'Belum diisi' }}</div>
                <div><i class="bi bi-calendar3 me-2"></i>Bergabung: {{ $user->created_at->translatedFormat('F Y') }}</div>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-8">
        <div class="card-custom p-4">
            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">Perbarui Informasi Akun</h6>

            <form action="{{ route('profile.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-semibold">Alamat Email (Akun)</label>
                        <input type="email" class="form-control bg-light" value="{{ $user->email }}" readonly disabled>
                        <span class="form-text small text-muted">Email digunakan sebagai username login utama.</span>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-semibold">Nomor Telepon / WhatsApp</label>
                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $user->phone) }}" placeholder="0812xxxxxxxx">
                        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12">
                        <hr class="my-2">
                        <h6 class="fw-bold text-dark mb-1 small text-uppercase text-muted">Ganti Kata Sandi (Opsional)</h6>
                        <span class="small text-muted">Kosongkan bagian ini jika tidak ingin mengubah kata sandi.</span>
                    </div>

                    <div class="col-12">
                        <label class="form-label small fw-semibold">Kata Sandi Saat Ini</label>
                        <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" placeholder="••••••••">
                        @error('current_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-semibold">Kata Sandi Baru</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Minimal 6 karakter">
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-semibold">Konfirmasi Kata Sandi Baru</label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi kata sandi baru">
                    </div>
                </div>

                <div class="mt-4 pt-3 border-top text-end">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-check-lg me-1"></i> Simpan Profil
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
