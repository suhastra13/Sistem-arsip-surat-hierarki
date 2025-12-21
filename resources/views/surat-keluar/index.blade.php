@extends('layouts.main')

@section('title', 'Data Surat Keluar')

@section('content')

<!-- Header Section -->
<div class="content-header bg-white p-3 rounded shadow-sm mb-3">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-1">
                <i class="fas fa-paper-plane me-2 text-primary"></i>Monitoring Surat Keluar
            </h5>
            <small class="text-muted">Kelola dan pantau status surat keluar</small>
        </div>
        @if(Auth::user()->role == 'staff')
        <a href="{{ route('surat-keluar.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus-circle me-1"></i>Buat Draft Baru
        </a>
        @endif
    </div>
</div>

<!-- Filter Section -->
<div class="filter-section bg-white p-3 rounded shadow-sm mb-3">
    <form action="{{ route('surat-keluar.index') }}" method="GET" id="filterForm">
        <div class="row g-3">
            <!-- Search -->
            <div class="col-lg-6">
                <label class="form-label text-secondary mb-1">
                    <i class="fas fa-search me-1"></i>Pencarian
                </label>
                <input type="text" name="search" class="form-control"
                    placeholder="Cari perihal surat..."
                    value="{{ request('search') }}">
            </div>

            <!-- Status Filter -->
            <div class="col-lg-3 col-md-6">
                <label class="form-label text-secondary mb-1">
                    <i class="fas fa-filter me-1"></i>Filter Status
                </label>
                <select name="filter" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="proses" {{ request('filter') == 'proses' ? 'selected' : '' }}>Dalam Proses</option>
                    <option value="acc" {{ request('filter') == 'acc' ? 'selected' : '' }}>Disetujui</option>
                    <option value="revisi" {{ request('filter') == 'revisi' ? 'selected' : '' }}>Revisi</option>
                    <option value="ditolak" {{ request('filter') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>

                    @if(in_array(Auth::user()->role, ['kabid', 'kasi']))
                    <optgroup label="History Kinerja">
                        <option value="history_acc" {{ request('filter') == 'history_acc' ? 'selected' : '' }}>Saya Setujui</option>
                        <option value="history_revisi" {{ request('filter') == 'history_revisi' ? 'selected' : '' }}>Saya Revisi</option>
                        <option value="history_ditolak" {{ request('filter') == 'history_ditolak' ? 'selected' : '' }}>Saya Tolak</option>
                    </optgroup>
                    @endif
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="col-lg-3 col-md-6">
                <label class="form-label opacity-0 mb-1">Action</label>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-fill">
                        <i class="fas fa-search"></i> Cari
                    </button>
                    @if(request()->hasAny(['search', 'filter']))
                    <a href="{{ route('surat-keluar.index') }}" class="btn btn-secondary" title="Reset">
                        <i class="fas fa-redo"></i>
                    </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Active Filters -->
        @if(request()->hasAny(['search', 'filter']))
        <div class="mt-3 pt-3 border-top">
            <div class="d-flex align-items-center flex-wrap gap-2">
                <strong class="text-primary me-2">
                    <i class="fas fa-filter me-1"></i>Filter Aktif:
                </strong>
                @if(request('search'))
                <span class="badge bg-primary">
                    <i class="fas fa-search me-1"></i>"{{ request('search') }}"
                    <a href="{{ route('surat-keluar.index', request()->except('search')) }}" class="text-white ms-2">×</a>
                </span>
                @endif
                @if(request('filter'))
                <span class="badge bg-primary">
                    <i class="fas fa-tag me-1"></i>
                    @if(request('filter') == 'proses') Dalam Proses
                    @elseif(request('filter') == 'acc') Disetujui
                    @elseif(request('filter') == 'revisi') Revisi
                    @elseif(request('filter') == 'ditolak') Ditolak
                    @elseif(request('filter') == 'history_acc') Saya Setujui
                    @elseif(request('filter') == 'history_revisi') Saya Revisi
                    @elseif(request('filter') == 'history_ditolak') Saya Tolak
                    @endif
                    <a href="{{ route('surat-keluar.index', request()->except('filter')) }}" class="text-white ms-2">×</a>
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
                    <th width="10%" class="text-white">Tanggal</th>
                    <th width="30%" class="text-white">Perihal Surat</th>
                    <th width="15%" class="text-center text-white">Status Approval</th>
                    <th width="25%" class="text-white">Posisi Dokumen</th>
                    <th width="20%" class="text-center text-white">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $item)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($item->tanggal_surat)->format('d/m/Y') }}</td>
                    <td>{{ Str::limit($item->perihal, 50) }}</td>
                    <td class="text-center">
                        @if($item->status_acc == 'acc')
                        <span class="badge bg-success">
                            <i class="fas fa-check-circle me-1"></i>Disetujui
                        </span>
                        @elseif($item->status_acc == 'revisi')
                        <span class="badge bg-warning text-dark">
                            <i class="fas fa-edit me-1"></i>Revisi
                        </span>
                        @elseif($item->status_acc == 'ditolak')
                        <span class="badge bg-danger">
                            <i class="fas fa-ban me-1"></i>Ditolak
                        </span>
                        @else
                        <span class="badge bg-secondary">
                            <i class="fas fa-spinner fa-spin me-1"></i>Proses
                        </span>
                        @endif
                    </td>
                    <td>
                        @if($item->posisi_saat_ini)
                        <div class="d-flex align-items-center">
                            <i class="fas fa-map-marker-alt text-danger me-2"></i>
                            <div>
                                <div class="fw-semibold small">{{ $item->posisi->name }}</div>
                                <div class="text-muted small" style="font-size: 0.7rem;">{{ $item->posisi->jabatan }}</div>
                            </div>
                        </div>
                        @else
                        <span class="text-muted small">Arsip / Selesai</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if(Auth::id() == $item->posisi_saat_ini && Auth::id() != $item->pembuat_id)
                        <a href="{{ route('surat-keluar.show', $item->id) }}" class="btn btn-sm btn-primary pulse-button">
                            <i class="fas fa-edit me-1"></i>Validasi
                        </a>
                        @elseif($item->status_acc == 'revisi' && Auth::id() == $item->pembuat_id)
                        <a href="{{ route('surat-keluar.show', $item->id) }}" class="btn btn-sm btn-warning">
                            <i class="fas fa-tools me-1"></i>Perbaiki
                        </a>
                        @else
                        <a href="{{ route('surat-keluar.show', $item->id) }}" class="btn btn-sm btn-light border">
                            <i class="fas fa-eye me-1"></i>Detail
                        </a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5">
                        <div class="text-muted">
                            <i class="fas fa-inbox fa-3x mb-3 opacity-50"></i>
                            <h5>Tidak Ada Data</h5>
                            <p class="mb-0">
                                @if(request()->hasAny(['search', 'filter']))
                                Tidak ada surat yang sesuai dengan filter yang dipilih.
                                @else
                                Belum ada data surat keluar yang tersedia.
                                @endif
                            </p>
                            @if(request()->hasAny(['search', 'filter']))
                            <a href="{{ route('surat-keluar.index') }}" class="btn btn-primary btn-sm mt-3">
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

    /* Pulse Animation for Validation Button */
    .pulse-button {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(0, 123, 255, 0.7);
        }

        70% {
            box-shadow: 0 0 0 10px rgba(0, 123, 255, 0);
        }

        100% {
            box-shadow: 0 0 0 0 rgba(0, 123, 255, 0);
        }
    }

    /* Responsive */
    @media (max-width: 768px) {
        .table {
            font-size: 0.813rem;
        }
    }
</style>

@endsection