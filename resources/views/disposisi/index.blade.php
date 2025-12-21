@extends('layouts.main')

@section('title', 'Kotak Masuk Disposisi')

@section('content')

<!-- Header Section -->
<div class="content-header bg-white p-3 rounded shadow-sm mb-3">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-1">
                <i class="fas fa-inbox me-2 text-primary"></i>Inbox Surat & Disposisi
            </h5>
            <small class="text-muted">Daftar surat dan disposisi yang diterima</small>
        </div>
        @php
        $unreadCount = $inbox->where('is_read', 0)->count();
        @endphp
        @if($unreadCount > 0)
        <div class="badge-counter">
            <span class="badge bg-danger">{{ $unreadCount }} Belum Dibaca</span>
        </div>
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

<!-- Table Section -->
<div class="table-container bg-white rounded shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-primary">
                <tr>
                    <th width="25%" class="text-white">Dari</th>
                    <th width="30%" class="text-white">Perihal Surat</th>
                    <th width="25%" class="text-white">Instruksi</th>
                    <th width="10%" class="text-center text-white">Status</th>
                    <th width="10%" class="text-center text-white">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($inbox as $item)
                <tr class="inbox-row {{ $item->is_read ? '' : 'unread-row' }}">
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="user-avatar {{ $item->is_read ? 'bg-light' : 'bg-primary' }} text-{{ $item->is_read ? 'secondary' : 'white' }} rounded-circle me-2">
                                <i class="fas fa-user"></i>
                            </div>
                            <div>
                                <div class="fw-semibold">{{ $item->pengirim->name }}</div>
                                <small class="text-muted">{{ $item->pengirim->jabatan }}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-start">
                            <i class="fas fa-file-alt text-primary me-2 mt-1"></i>
                            <div>
                                <div class="fw-semibold text-primary">{{ $item->surat->nomor_surat }}</div>
                                <small class="text-muted">{{ Str::limit($item->surat->perihal, 50) }}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="instruction-box">
                            <i class="fas fa-quote-left me-1"></i>
                            <span class="fst-italic">{{ Str::limit($item->instruksi, 40) }}</span>
                        </div>
                    </td>
                    <td class="text-center">
                        @if($item->is_read)
                        <span class="badge bg-success">
                            <i class="fas fa-check-double me-1"></i>Dibaca
                        </span>
                        @else
                        <span class="badge bg-danger">
                            <i class="fas fa-envelope me-1"></i>Baru
                        </span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($item->status == 'pending')
                        <a href="{{ route('disposisi.show', $item->id) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-folder-open me-1"></i>Buka
                        </a>
                        @else
                        <span class="badge bg-light text-success border border-success">
                            <i class="fas fa-check-circle me-1"></i>Selesai
                        </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5">
                        <div class="empty-state">
                            <i class="fas fa-inbox fa-4x mb-3 opacity-25"></i>
                            <h5 class="text-muted">Kotak Masuk Kosong</h5>
                            <p class="text-muted mb-0">Tidak ada surat baru di kotak masuk Anda.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<style>
    /* Content Header */
    .content-header {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        border: 1px solid #e9ecef;
    }

    .content-header h5 {
        font-size: 1rem;
        font-weight: 600;
        margin: 0;
    }

    /* Badge Counter */
    .badge-counter .badge {
        font-size: 0.813rem;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        animation: pulse-badge 2s infinite;
    }

    @keyframes pulse-badge {

        0%,
        100% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.05);
        }
    }

    /* Table Container */
    .table-container {
        border: 1px solid #e9ecef;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
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
        padding: 1rem 0.75rem;
        border-color: #e9ecef;
    }

    /* Unread Row */
    .unread-row {
        background: linear-gradient(90deg, rgba(0, 64, 133, 0.05) 0%, rgba(255, 255, 255, 0) 100%);
        border-left: 3px solid #004085;
    }

    .unread-row:hover {
        background: linear-gradient(90deg, rgba(0, 64, 133, 0.08) 0%, rgba(255, 255, 255, 0) 100%);
    }

    .inbox-row {
        transition: all 0.2s ease;
    }

    .inbox-row:hover {

        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    /* User Avatar */
    .user-avatar {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        flex-shrink: 0;
    }

    /* Instruction Box */
    .instruction-box {
        background: #f8f9fa;
        padding: 0.5rem 0.75rem;
        border-radius: 6px;
        border-left: 3px solid #004085;
        color: #495057;
        font-size: 0.813rem;
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

    /* Empty State */
    .empty-state {
        padding: 2rem 0;
    }

    .empty-state i {
        color: #ced4da;
    }

    /* Alert */
    .alert {
        border-radius: 6px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .table {
            font-size: 0.813rem;
        }

        .user-avatar {
            width: 35px;
            height: 35px;
            font-size: 0.875rem;
        }

        .instruction-box {
            font-size: 0.75rem;
            padding: 0.375rem 0.5rem;
        }
    }
</style>

@endsection