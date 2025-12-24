@extends('layouts.main')

@section('title', 'Surat Masuk')

@section('content')

<!-- Stats removed as requested -->

<!-- Header Section -->
<div class="content-header bg-white p-3 rounded shadow-sm mb-3">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-1">
                <i class="fas fa-table me-2 text-primary"></i>Data Surat Masuk
            </h5>
            <small class="text-muted">Kelola dan lacak semua surat masuk</small>
        </div>
        @if(Auth::user()->role == 'admin')
        <a href="{{ route('surat-masuk.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus-circle me-1"></i>Input Surat Baru
        </a>
        @endif
    </div>
</div>

<!-- Success Alert -->
@if(session('success'))
<div class="alert alert-success border-0 shadow-sm d-flex align-items-center mb-3" role="alert">
    <i class="fas fa-check-circle me-3"></i>
    <div>
        <strong>Berhasil!</strong> {{ session('success') }}
    </div>
    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
</div>
@endif

<!-- Filter Section -->
<div class="filter-section bg-white p-3 rounded shadow-sm mb-3">
    <form action="{{ route('surat-masuk.index') }}" method="GET" id="filterForm">
        <div class="row g-3">
            <!-- Search -->
            <div class="col-lg-4">
                <label class="form-label text-secondary mb-1">
                    <i class="fas fa-search me-1"></i>Pencarian
                </label>
                <input type="text" name="search" class="form-control"
                    placeholder="Cari nomor, perihal, atau pengirim..."
                    value="{{ request('search') }}">
            </div>

            <!-- Year Filter -->
            <div class="col-lg-2 col-md-4">
                <label class="form-label text-secondary mb-1">
                    <i class="fas fa-calendar-alt me-1"></i>Tahun
                </label>
                <select name="year" class="form-select">
                    <option value="">Semua Tahun</option>
                    @for ($y = date('Y'); $y >= date('Y') - 5; $y--)
                    <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <!-- Month Filter -->
            <div class="col-lg-2 col-md-4">
                <label class="form-label text-secondary mb-1">
                    <i class="fas fa-calendar me-1"></i>Bulan
                </label>
                <select name="month" class="form-select">
                    <option value="">Semua Bulan</option>
                    @php
                    $months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                    @endphp
                    @foreach($months as $num => $name)
                    <option value="{{ $num + 1 }}" {{ request('month') == ($num + 1) ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div class="col-lg-2 col-md-4">
                <label class="form-label text-secondary mb-1">
                    <i class="fas fa-filter me-1"></i>Status
                </label>
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="menunggu_kabid" {{ request('status') == 'menunggu_kabid' ? 'selected' : '' }}>Menunggu Kabid</option>
                    <option value="di_kasi" {{ request('status') == 'di_kasi' ? 'selected' : '' }}>Di Meja Kasi</option>
                    <option value="di_staf" {{ request('status') == 'di_staf' ? 'selected' : '' }}>Di Meja Staf</option>
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="col-lg-2">
                <label class="form-label opacity-0 mb-1">Action</label>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-fill">
                        <i class="fas fa-search"></i> Cari
                    </button>
                    @if(request()->hasAny(['search', 'year', 'month', 'status']))
                    <a href="{{ route('surat-masuk.index') }}" class="btn btn-secondary" title="Reset">
                        <i class="fas fa-redo"></i>
                    </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Active Filters Badge -->
        @if(request()->hasAny(['search', 'year', 'month', 'status']))
        <div class="mt-3 pt-3 border-top">
            <div class="d-flex align-items-center flex-wrap gap-2">
                <strong class="text-primary me-2">
                    <i class="fas fa-filter me-1"></i>Filter Aktif:
                </strong>
                @if(request('search'))
                <span class="badge bg-primary">
                    <i class="fas fa-search me-1"></i>"{{ request('search') }}"
                    <a href="{{ route('surat-masuk.index', request()->except('search')) }}" class="text-white ms-2">×</a>
                </span>
                @endif
                @if(request('year'))
                <span class="badge bg-primary">
                    <i class="fas fa-calendar-alt me-1"></i>Tahun: {{ request('year') }}
                    <a href="{{ route('surat-masuk.index', request()->except('year')) }}" class="text-white ms-2">×</a>
                </span>
                @endif
                @if(request('month'))
                <span class="badge bg-primary">
                    <i class="fas fa-calendar me-1"></i>{{ $months[request('month') - 1] ?? '' }}
                    <a href="{{ route('surat-masuk.index', request()->except('month')) }}" class="text-white ms-2">×</a>
                </span>
                @endif
                @if(request('status'))
                <span class="badge bg-primary">
                    <i class="fas fa-tag me-1"></i>
                    @if(request('status') == 'menunggu_kabid') Menunggu Kabid
                    @elseif(request('status') == 'di_kasi') Di Meja Kasi
                    @elseif(request('status') == 'di_staf') Di Meja Staf
                    @endif
                    <a href="{{ route('surat-masuk.index', request()->except('status')) }}" class="text-white ms-2">×</a>
                </span>
                @endif
            </div>
        </div>
        @endif
    </form>
</div>

<!-- Table Section -->
<div class="table-container bg-white rounded shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-primary">
                <tr>
                    <th width="5%" class="text-center text-white">No</th>
                    <th width="15%" class="text-white">Nomor Surat</th>
                    <th width="20%" class="text-white">Pengirim</th>
                    <th width="15%" class="text-white">Kategori</th>
                    <th width="25%" class="text-white">Perihal</th>
                    <th width="10%" class="text-center text-white">File</th>
                    <th width="15%" class="text-center text-white">Status</th>
                    <th width="10%" class="text-center text-white">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($surat as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <div class="fw-semibold text-primary">{{ $item->nomor_surat }}</div>
                        <small class="text-muted">
                            <i class="far fa-calendar-alt me-1"></i>
                            {{ \Carbon\Carbon::parse($item->tanggal_surat)->format('d M Y') }}
                        </small>
                    </td>
                    <td>
                        <div class="d-flex align-items-start">
                            <i class="fas fa-university text-primary me-2 mt-1"></i>
                            <span>{{ $item->pengirim }}</span>
                        </div>
                    </td>
                    <td>
                        <span class="d-flex align-items-start">{{ $item->kategori }}</span>
                    </td>

                    <td>{{ Str::limit($item->perihal, 70) }}</td>
                    <td class="text-center">
                        <a href="{{ asset('storage/' . $item->file_path) }}"
                            target="_blank"
                            class="btn btn-sm btn-danger">
                            <i class="fas fa-file-pdf me-1"></i>PDF
                        </a>
                    </td>
                    <td class="text-center">
                        @if($item->status_akhir == 'Menunggu Disposisi Kabid')
                        <span class="badge bg-warning text-dark">
                            <i class="fas fa-clock me-1"></i>Menunggu Kabid
                        </span>
                        @elseif($item->status_akhir == 'Disposisi di Meja Kasi')
                        <span class="badge bg-danger">
                            <i class="fas fa-user-tie me-1"></i>Di Meja Kasi
                        </span>
                        @elseif($item->status_akhir == 'Disposisi di Meja Staf')
                        <span class="badge bg-info">
                            <i class="fas fa-user me-1"></i>Di Meja Staf
                        </span>
                        @else
                        <span class="badge bg-success">
                            <i class="fas fa-check-circle me-1"></i>Selesai
                        </span>
                        @endif
                    </td>
                    <td class="text-center">
                        <a href="{{ route('surat-masuk.show', $item->id) }}"
                            class="btn btn-sm btn-primary"
                            title="Lihat Detail">
                            <i class="fas fa-eye me-1"></i>Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5">
                        <div class="text-muted">
                            <i class="fas fa-inbox fa-3x mb-3 opacity-50"></i>
                            <h5>Tidak Ada Data</h5>
                            <p class="mb-0">
                                @if(request()->hasAny(['search', 'year', 'month', 'status']))
                                Tidak ada surat yang sesuai dengan filter yang dipilih.
                                @else
                                Belum ada data surat masuk yang tersedia.
                                @endif
                            </p>
                            @if(request()->hasAny(['search', 'year', 'month', 'status']))
                            <a href="{{ route('surat-masuk.index') }}" class="btn btn-primary btn-sm mt-3">
                                <i class="fas fa-redo me-2"></i>Reset Filter
                            </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<style>
    /* Content Sections */
    .content-header,
    .filter-section,
    .table-container {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        border: 1px solid #e9ecef;
    }

    .content-header h5 {
        font-size: 1rem;
        font-weight: 600;
        margin: 0;
    }

    /* Form Elements */
    .form-label {
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        margin-bottom: 0.5rem;
    }

    .form-control,
    .form-select {
        font-size: 0.875rem;
        padding: 0.5rem 0.75rem;
        border: 1px solid #ced4da;
        border-radius: 6px;
        transition: all 0.2s ease;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #004085;
        box-shadow: 0 0 0 0.15rem rgba(0, 64, 133, 0.15);
    }

    /* Table */
    .table {
        font-size: 0.875rem;
    }

    .table-primary {
        background: #003366 !important;
    }

    .table-primary th {
        background: #003366 !important;
        border-color: #002952;
        padding: 0.85rem 0.75rem;
        font-size: 0.813rem;
        font-weight: 600;
        color: white !important;
    }

    .table tbody td {
        padding: 0.85rem 0.75rem;
        border-color: #e9ecef;
    }

    .table-hover tbody tr {
        transition: all 0.2s ease;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(0, 64, 133, 0.05);
    }

    /* Badge */
    .badge {
        font-size: 0.75rem;
        font-weight: 500;
        padding: 0.4rem 0.75rem;
        border-radius: 6px;
    }

    /* Buttons */
    .btn {
        font-size: 0.875rem;
        font-weight: 500;
        border-radius: 6px;
        transition: all 0.2s ease;
    }

    .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
    }

    .btn-sm {
        font-size: 0.813rem;
        padding: 0.375rem 0.75rem;
    }

    .btn-primary {
        background: linear-gradient(135deg, #004085 0%, #0056b3 100%);
        border: none;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #003266 0%, #004085 100%);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .table {
            font-size: 0.813rem;
        }
    }
</style>

@endsection