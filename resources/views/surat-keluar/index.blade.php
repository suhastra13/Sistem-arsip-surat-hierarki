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
            <div class="col-lg-4">
                <label class="form-label text-secondary mb-1">
                    <i class="fas fa-search me-1"></i>Pencarian
                </label>
                <input type="text" name="search" class="form-control"
                    placeholder="Cari perihal surat..."
                    value="{{ request('search') }}">
            </div>

            <!-- Tahun Filter -->
            <div class="col-lg-2 col-md-4">
                <label class="form-label text-secondary mb-1">
                    <i class="fas fa-calendar-alt me-1"></i>Tahun
                </label>
                <select name="tahun" class="form-select">
                    <option value="">Semua Tahun</option>
                    @for($year = date('Y'); $year >= date('Y') - 5; $year--)
                    <option value="{{ $year }}" {{ request('tahun') == $year ? 'selected' : '' }}>
                        {{ $year }}
                    </option>
                    @endfor
                </select>
            </div>

            <!-- Bulan Filter -->
            <div class="col-lg-2 col-md-4">
                <label class="form-label text-secondary mb-1">
                    <i class="fas fa-calendar me-1"></i>Bulan
                </label>
                <select name="bulan" class="form-select">
                    <option value="">Semua Bulan</option>
                    <option value="01" {{ request('bulan') == '01' ? 'selected' : '' }}>Januari</option>
                    <option value="02" {{ request('bulan') == '02' ? 'selected' : '' }}>Februari</option>
                    <option value="03" {{ request('bulan') == '03' ? 'selected' : '' }}>Maret</option>
                    <option value="04" {{ request('bulan') == '04' ? 'selected' : '' }}>April</option>
                    <option value="05" {{ request('bulan') == '05' ? 'selected' : '' }}>Mei</option>
                    <option value="06" {{ request('bulan') == '06' ? 'selected' : '' }}>Juni</option>
                    <option value="07" {{ request('bulan') == '07' ? 'selected' : '' }}>Juli</option>
                    <option value="08" {{ request('bulan') == '08' ? 'selected' : '' }}>Agustus</option>
                    <option value="09" {{ request('bulan') == '09' ? 'selected' : '' }}>September</option>
                    <option value="10" {{ request('bulan') == '10' ? 'selected' : '' }}>Oktober</option>
                    <option value="11" {{ request('bulan') == '11' ? 'selected' : '' }}>November</option>
                    <option value="12" {{ request('bulan') == '12' ? 'selected' : '' }}>Desember</option>
                </select>
            </div>

            <!-- Kategori Filter -->
            <div class="col-lg-2 col-md-4">
                <label class="form-label text-secondary mb-1">
                    <i class="fas fa-tags me-1"></i>Kategori
                </label>
                <select name="kategori" class="form-select">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoris as $kategori)
                    <option value="{{ $kategori }}" {{ request('kategori') == $kategori ? 'selected' : '' }}>
                        {{ $kategori }}
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div class="col-lg-2 col-md-6">
                <label class="form-label text-secondary mb-1">
                    <i class="fas fa-filter me-1"></i>Status
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
        </div>

        <!-- Action Buttons -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="d-flex gap-2 justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-1"></i> Cari
                    </button>
                    @if(request()->hasAny(['search', 'filter', 'tahun', 'bulan', 'kategori']))
                    <a href="{{ route('surat-keluar.index') }}" class="btn btn-secondary">
                        <i class="fas fa-redo me-1"></i> Reset
                    </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Active Filters -->
        @if(request()->hasAny(['search', 'filter', 'tahun', 'bulan', 'kategori']))
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
                @if(request('tahun'))
                <span class="badge bg-primary">
                    <i class="fas fa-calendar-alt me-1"></i>Tahun {{ request('tahun') }}
                    <a href="{{ route('surat-keluar.index', request()->except('tahun')) }}" class="text-white ms-2">×</a>
                </span>
                @endif
                @if(request('bulan'))
                <span class="badge bg-primary">
                    <i class="fas fa-calendar me-1"></i>
                    @php
                    $bulanNames = ['01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
                    '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
                    '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'];
                    @endphp
                    {{ $bulanNames[request('bulan')] ?? request('bulan') }}
                    <a href="{{ route('surat-keluar.index', request()->except('bulan')) }}" class="text-white ms-2">×</a>
                </span>
                @endif
                @if(request('kategori'))
                <span class="badge bg-primary">
                    <i class="fas fa-tags me-1"></i>{{ request('kategori') }}
                    <a href="{{ route('surat-keluar.index', request()->except('kategori')) }}" class="text-white ms-2">×</a>
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
                    <th width="8%" class="text-white">Tanggal</th>
                    <th width="20%" class="text-white">Perihal Surat</th>
                    <th width="10%" class="text-center text-white">Kategori</th>
                    <th width="10%" class="text-center text-white">Status</th>
                    <th width="17%" class="text-white">Posisi Dokumen</th>
                    <th width="20%" class="text-white">History Approval</th>
                    <th width="15%" class="text-center text-white">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $item)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($item->tanggal_surat)->format('d/m/Y') }}</td>
                    <td>{{ Str::limit($item->perihal, 40) }}</td>
                    <td class="text-center">
                        <span class="badge bg-secondary">{{ $item->kategori ?? '-' }}</span>
                    </td>
                    <td class="text-center">
                        @if($item->status_acc == 'acc')
                        <span class="badge bg-success">
                            <i class="fas fa-check-circle me-1"></i>ACC
                        </span>
                        @elseif($item->status_acc == 'revisi')
                        <span class="badge bg-warning text-dark">
                            <i class="fas fa-edit me-1"></i>Revisi
                        </span>
                        @elseif($item->status_acc == 'ditolak')
                        <span class="badge bg-danger">
                            <i class="fas fa-ban me-1"></i>Tolak
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
                    <td>
                        <!-- History Timeline -->
                        <div class="history-timeline">
                            @php
                            $histories = $item->logs ?? collect();
                            @endphp

                            @if($histories->isEmpty())
                            <span class="text-muted small">
                                <i class="fas fa-clock me-1"></i>Belum ada history
                            </span>
                            @else
                            @foreach($histories->take(2) as $history)
                            <div class="history-item mb-2">
                                <div class="d-flex align-items-start">
                                    @if($history->aksi == 'acc')
                                    <i class="fas fa-check-circle text-success me-2 mt-1"></i>
                                    @elseif($history->aksi == 'revisi')
                                    <i class="fas fa-edit text-warning me-2 mt-1"></i>
                                    @elseif($history->aksi == 'ditolak')
                                    <i class="fas fa-times-circle text-danger me-2 mt-1"></i>
                                    @else
                                    <i class="fas fa-arrow-right text-info me-2 mt-1"></i>
                                    @endif

                                    <div class="flex-grow-1">
                                        <div class="small fw-semibold">{{ $history->pengirim->name ?? '-' }}</div>
                                        <div class="text-muted" style="font-size: 0.7rem;">
                                            {{ $history->pengirim->jabatan ?? '-' }}
                                            @if($history->aksi == 'acc')
                                            <span class="text-success">✓ Setuju</span>
                                            @elseif($history->aksi == 'revisi')
                                            <span class="text-warning">⟳ Revisi</span>
                                            @elseif($history->aksi == 'ditolak')
                                            <span class="text-danger">✗ Tolak</span>
                                            @else
                                            <span class="text-info">{{ ucfirst($history->aksi) }}</span>
                                            @endif
                                        </div>
                                        <div class="text-muted" style="font-size: 0.65rem;">
                                            {{ \Carbon\Carbon::parse($history->created_at)->format('d/m/y H:i') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach

                            @if($histories->count() > 2)
                            <button type="button" class="btn btn-link btn-sm p-0 text-primary"
                                data-bs-toggle="modal"
                                data-bs-target="#historyModal{{ $item->id }}">
                                <i class="fas fa-plus-circle me-1"></i>Lihat {{ $histories->count() - 2 }} lainnya
                            </button>
                            @endif
                            @endif
                        </div>
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

                <!-- Modal Full History -->
                @if($histories->count() > 2)
                <div class="modal fade" id="historyModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h6 class="modal-title mb-0">
                                    <i class="fas fa-history me-2"></i>Riwayat Lengkap Approval
                                </h6>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-2">
                                    <strong class="text-primary">Perihal:</strong> {{ $item->perihal }}
                                </div>
                                <hr>
                                @foreach($histories as $history)
                                <div class="history-item-full mb-3 p-3 border rounded">
                                    <div class="d-flex align-items-start">
                                        @if($history->aksi == 'acc')
                                        <div class="badge bg-success me-3 mt-1">✓</div>
                                        @elseif($history->aksi == 'revisi')
                                        <div class="badge bg-warning me-3 mt-1">⟳</div>
                                        @elseif($history->aksi == 'ditolak')
                                        <div class="badge bg-danger me-3 mt-1">✗</div>
                                        @else
                                        <div class="badge bg-info me-3 mt-1">→</div>
                                        @endif

                                        <div class="flex-grow-1">
                                            <div class="fw-semibold">{{ $history->pengirim->name ?? '-' }}</div>
                                            <div class="text-muted small">{{ $history->pengirim->jabatan ?? '-' }}</div>
                                            <div class="mt-2">
                                                <span class="badge 
                                                    @if($history->aksi == 'acc') bg-success
                                                    @elseif($history->aksi == 'revisi') bg-warning text-dark
                                                    @elseif($history->aksi == 'ditolak') bg-danger
                                                    @else bg-info
                                                    @endif">
                                                    {{ ucfirst($history->aksi) }}
                                                </span>
                                            </div>
                                            @if($history->catatan_revisi)
                                            <div class="mt-2 small text-muted">
                                                <strong>Catatan:</strong> {{ $history->catatan_revisi }}
                                            </div>
                                            @endif
                                            <div class="mt-2 text-muted" style="font-size: 0.75rem;">
                                                <i class="far fa-clock me-1"></i>
                                                {{ \Carbon\Carbon::parse($history->created_at)->format('d M Y, H:i') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @empty
                <tr>
                    <td colspan="7" class="text-center py-5">
                        <div class="text-muted">
                            <i class="fas fa-inbox fa-3x mb-3 opacity-50"></i>
                            <h5>Tidak Ada Data</h5>
                            <p class="mb-0">
                                @if(request()->hasAny(['search', 'filter', 'tahun', 'bulan', 'kategori']))
                                Tidak ada surat yang sesuai dengan filter yang dipilih.
                                @else
                                Belum ada data surat keluar yang tersedia.
                                @endif
                            </p>
                            @if(request()->hasAny(['search', 'filter', 'tahun', 'bulan', 'kategori']))
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

    <!-- Pagination -->
    @if($data->hasPages())
    <div class="p-3 border-top">
        {{ $data->appends(request()->query())->links() }}
    </div>
    @endif
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
        vertical-align: top;
    }

    .table-hover tbody tr {
        transition: all 0.2s ease;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(0, 64, 133, 0.05);
    }

    /* History Timeline */
    .history-timeline {
        max-width: 100%;
    }

    .history-item {
        padding-left: 0;
        border-left: 2px solid #e9ecef;
        padding-left: 10px;
    }

    .history-item:last-child {
        border-left: none;
    }

    .history-item-full {
        background: #f8f9fa;
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

    .btn-link {
        text-decoration: none;
        font-size: 0.75rem;
    }

    .btn-link:hover {
        text-decoration: underline;
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

    /* Modal */
    .modal-header {
        background: linear-gradient(135deg, #004085 0%, #0056b3 100%);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .table {
            font-size: 0.75rem;
        }

        .history-item {
            font-size: 0.7rem;
        }
    }
</style>

@endsection