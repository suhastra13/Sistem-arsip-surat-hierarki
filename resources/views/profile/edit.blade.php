@extends('layouts.main')

@section('title', 'Profil Saya')

@section('content')

<div class="row justify-content-center">
    <div class="col-md-8">
        
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 fw-bold text-primary"><i class="fas fa-user-cog me-1"></i> Edit Profil & Keamanan</h6>
            </div>
            
            <div class="card-body p-4">

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <h6 class="text-muted small fw-bold text-uppercase mb-3">Informasi Akun</h6>
                    
                    <div class="mb-3 row">
                        <label class="col-sm-3 col-form-label fw-bold">Nama Lengkap</label>
                        <div class="col-sm-9">
                            <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label class="col-sm-3 col-form-label fw-bold">Email (Login)</label>
                        <div class="col-sm-9">
                            <input type="email" name="email" class="form-control bg-light" value="{{ old('email', $user->email) }}" readonly>
                            <small class="text-muted">Email tidak dapat diubah sembarangan.</small>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label class="col-sm-3 col-form-label fw-bold">Jabatan</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control-plaintext" value="{{ $user->jabatan }}" readonly>
                        </div>
                    </div>

                    <hr class="my-4">

                    <h6 class="text-muted small fw-bold text-uppercase mb-3 text-danger">Ganti Password (Opsional)</h6>
                    <div class="alert alert-warning small">
                        <i class="fas fa-info-circle me-1"></i> Kosongkan bagian ini jika tidak ingin mengubah password.
                    </div>

                    <div class="mb-3 row">
                        <label class="col-sm-3 col-form-label fw-bold">Password Lama</label>
                        <div class="col-sm-9">
                            <input type="password" name="current_password" class="form-control" placeholder="Masukkan password saat ini...">
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label class="col-sm-3 col-form-label fw-bold">Password Baru</label>
                        <div class="col-sm-9">
                            <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter">
                        </div>
                    </div>

                    <div class="mb-4 row">
                        <label class="col-sm-3 col-form-label fw-bold">Ulangi Password</label>
                        <div class="col-sm-9">
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Ketik ulang password baru">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary fw-bold px-4">
                            <i class="fas fa-save me-1"></i> SIMPAN PERUBAHAN
                        </button>
                    </div>

                </form>
            </div>
        </div>

    </div>
</div>
@endsection