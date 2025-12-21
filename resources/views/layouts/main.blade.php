<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Arsip Dinas Kehutanan</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    
    <style>
        body { background-color: #f4f6f9; font-family: 'Inter', sans-serif; overflow-x: hidden; }

        /* SIDEBAR */
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #004085 0%, #002752 100%);
            color: white;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
            position: fixed; top: 0; left: 0; width: 250px; z-index: 100; transition: all 0.3s;
        }

        .sidebar .brand {
            font-size: 1.2rem; font-weight: 700; text-align: center; padding: 25px 0;
            border-bottom: 1px solid rgba(255,255,255,0.1); background: rgba(0,0,0,0.1); letter-spacing: 1px;
        }

        .sidebar .nav-link {
            color: rgba(255,255,255,0.75); padding: 12px 20px; font-size: 0.95rem;
            border-left: 4px solid transparent; transition: all 0.2s; position: relative;
        }

        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: #fff; background-color: rgba(255,255,255,0.1); border-left-color: #00d2ff;
        }
        
        .sidebar i { margin-right: 10px; width: 20px; text-align: center; }
        .sidebar .menu-label {
            font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px;
            color: rgba(255,255,255,0.4); margin: 20px 20px 10px; font-weight: 600;
        }

        /* Badge Custom untuk Sidebar */
        .sidebar-badge {
            position: absolute; right: 15px; top: 12px;
            padding: 3px 8px; border-radius: 10px; font-size: 0.7rem; font-weight: bold;
        }

        .main-content { margin-left: 250px; transition: all 0.3s; }

        .top-navbar {
            background: white; padding: 15px 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center;
        }

        .page-content { padding: 30px; }

        @media (max-width: 768px) {
            .sidebar { margin-left: -250px; }
            .sidebar.active { margin-left: 0; }
            .main-content { margin-left: 0; }
        }
    </style>
</head>
<body>

<nav class="sidebar">
    <div class="brand">
        <i class="fas fa-archive"></i> E-ARSIP DISHUT
    </div>
    
    <div class="nav flex-column mt-3">
        
        <div class="menu-label">Menu Utama</div>
        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}">
            <i class="fas fa-home"></i> Dashboard
        </a>

        @if(in_array(Auth::user()->role, ['admin', 'kabid', 'kasi']))
            <div class="menu-label">Data & Arsip</div>
            <a href="{{ route('surat-masuk.index') }}" class="nav-link {{ request()->is('surat-masuk*') ? 'active' : '' }}">
                <i class="fas fa-envelope-open"></i> Data Surat Masuk
            </a>
        @endif

        @if(Auth::user()->role == 'admin')
            <a href="{{ route('surat-keluar.index') }}" class="nav-link {{ request()->is('surat-keluar*') ? 'active' : '' }}">
                <i class="fas fa-paper-plane"></i> Data Surat Keluar
            </a>
            <a href="{{ route('users.index') }}" class="nav-link {{ request()->is('users*') ? 'active' : '' }}">
                <i class="fas fa-users-cog"></i> Pegawai
            </a>
        @endif

        @if(in_array(Auth::user()->role, ['staff', 'kabid', 'kasi']))
            <div class="menu-label">Tugas & Disposisi</div>
            
            @php 
                $countInbox = \App\Models\DisposisiSurat::where('penerima_id', Auth::id())->where('is_read', 0)->count(); 
            @endphp
            <a href="{{ route('disposisi.index') }}" class="nav-link {{ request()->is('disposisi*') ? 'active' : '' }}">
                <i class="fas fa-inbox"></i> Kotak Masuk
                @if($countInbox > 0)
                    <span class="badge bg-danger sidebar-badge">{{ $countInbox }}</span>
                @endif
            </a>
        @endif

        @if(in_array(Auth::user()->role, ['staff', 'kabid', 'kasi']))
            
            @php
                $notifCount = 0;
                $notifColor = 'danger'; // Merah default
                
                // SKENARIO A: STAFF (Cek Revisi)
                if(Auth::user()->role == 'staff') {
                    // Hitung surat saya yang statusnya REVISI
                    $notifCount = \App\Models\SuratKeluar::where('pembuat_id', Auth::id())
                                    ->where('status_acc', 'revisi')
                                    ->count();
                    $notifColor = 'warning text-dark'; // Kuning kalau revisi
                }
                
                // SKENARIO B: PIMPINAN (Cek Validasi Masuk)
                elseif(in_array(Auth::user()->role, ['kabid', 'kasi'])) {
                    // Hitung surat yang posisinya sedang di MEJA SAYA
                    $notifCount = \App\Models\SuratKeluar::where('posisi_saat_ini', Auth::id())->count();
                }
            @endphp

            <a href="{{ request()->is('surat-keluar/create') ? route('surat-keluar.create') : route('surat-keluar.index') }}" class="nav-link {{ request()->is('surat-keluar*') ? 'active' : '' }}">
                <i class="fas fa-file-signature"></i> 
                {{ Auth::user()->role == 'staff' ? 'Buat Surat Keluar' : 'Validasi Surat Keluar' }}
                
                @if($notifCount > 0)
                    <span class="badge bg-{{ $notifColor }} sidebar-badge">{{ $notifCount }}</span>
                @endif
            </a>
        @endif

        <div style="position: absolute; bottom: 20px; width: 100%; padding: 0 20px;">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-light w-100 btn-sm">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </div>
    </div>
</nav>

<div class="main-content">
    
    <div class="top-navbar">
        <div>
            <h5 class="m-0 fw-bold text-primary">@yield('title', 'Dashboard')</h5>
            <small class="text-muted">{{ date('l, d F Y') }}</small>
        </div>
        
        <div class="dropdown">
            <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                <div class="text-end me-3">
                    <div class="fw-bold text-dark">{{ Auth::user()->name }}</div>
                    <div class="text-muted small" style="font-size: 0.8rem;">{{ strtoupper(Auth::user()->role) }}</div>
                </div>
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                    <i class="fas fa-user"></i>
                </div>
            </a>
            
            <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="dropdownUser">
                <li>
                    <a class="dropdown-item py-2" href="{{ route('profile.edit') }}">
                        <i class="fas fa-user-cog me-2 text-muted"></i> Profil & Password
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="dropdown-item py-2 text-danger">
                            <i class="fas fa-sign-out-alt me-2"></i> Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>

    <div class="page-content">
        @yield('content')
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>