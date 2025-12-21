@extends('layouts.main')

@section('title', 'Detail & Tracking Surat Keluar')

@section('content')

<!-- Timeline Tracking -->
<div class="tracking-section bg-white rounded shadow-sm p-4 mb-3">
    <h6 class="text-center text-muted mb-4">
        <i class="fas fa-route me-2"></i>Tracking Progress Surat
    </h6>

    <div class="timeline-steps">
        @php
        // Cari log terakhir untuk mengetahui siapa aktor terakhir
        $lastLog = $surat->logs()->latest()->first();
        $lastAction = $lastLog ? $lastLog->aksi : '';
        $lastActorRole = $lastLog && $lastLog->pengirim ? $lastLog->pengirim->role : '';

        // Default class
        $stepKasi = '';
        $stepKabid = '';

        // --- LOGIKA STEP 2: KASI ---
        if($surat->status_acc == 'pending_kasi') {
        $stepKasi = 'active';
        }
        elseif(in_array($surat->status_acc, ['pending_kabid', 'acc'])) {
        $stepKasi = 'done';
        }
        elseif(in_array($surat->status_acc, ['revisi', 'ditolak'])) {
        if($lastActorRole == 'kasi') {
        $stepKasi = ($surat->status_acc == 'ditolak') ? 'die' : 'rejected';
        } else {
        $stepKasi = 'done';
        }
        }

        // --- LOGIKA STEP 3: KABID ---
        if($surat->status_acc == 'pending_kabid') {
        $stepKabid = 'active';
        }
        elseif($surat->status_acc == 'acc') {
        $stepKabid = 'done';
        }
        elseif(in_array($surat->status_acc, ['revisi', 'ditolak'])) {
        if($lastActorRole == 'kabid') {
        $stepKabid = ($surat->status_acc == 'ditolak') ? 'die' : 'rejected';
        }
        }
        @endphp

        <div class="timeline-step done">
            <div class="timeline-content"><i class="fas fa-pen"></i></div>
            <div class="timeline-label">Draft Staff</div>
        </div>

        <div class="timeline-step {{ $stepKasi }}">
            <div class="timeline-content">2</div>
            <div class="timeline-label">Review Kasi</div>
        </div>

        <div class="timeline-step {{ $stepKabid }}">
            <div class="timeline-content">3</div>
            <div class="timeline-label">Review Kabid</div>
        </div>

        <div class="timeline-step {{ $surat->status_acc == 'acc' ? 'done' : '' }}">
            <div class="timeline-content"><i class="fas fa-check"></i></div>
            <div class="timeline-label">Selesai/Arsip</div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Left Column: Info & History -->
    <div class="col-lg-8">

        <!-- Status Card -->
        <div class="status-card bg-white rounded shadow-sm p-3 mb-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <small class="text-muted d-block mb-1">Status Dokumen</small>
                    @if($surat->status_acc == 'acc')
                    <h5 class="fw-semibold text-success mb-0">
                        <i class="fas fa-check-circle me-2"></i>Selesai / Arsip
                    </h5>
                    @elseif($surat->status_acc == 'revisi')
                    <h5 class="fw-semibold text-warning mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>Perlu Revisi
                    </h5>
                    @elseif($surat->status_acc == 'ditolak')
                    <h5 class="fw-semibold text-danger mb-0">
                        <i class="fas fa-ban me-2"></i>Ditolak Mutlak
                    </h5>
                    @else
                    <h5 class="fw-semibold text-warning mb-0">
                        <i class="fas fa-clock me-2"></i>Proses Validasi
                    </h5>
                    @endif
                </div>
                @if($surat->status_acc != 'acc' && $surat->status_acc != 'ditolak')
                <div class="text-end">
                    <small class="text-muted d-block mb-1">Posisi Saat Ini</small>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-map-marker-alt text-danger me-2"></i>
                        <div>
                            <div class="fw-semibold small">{{ $surat->posisi->name ?? '-' }}</div>
                            <div class="text-muted" style="font-size: 0.7rem;">{{ $surat->posisi->jabatan ?? '' }}</div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Info Surat -->
        <div class="info-section bg-white rounded shadow-sm mb-3">
            <div class="section-header">
                <h6 class="mb-0">
                    <i class="fas fa-info-circle me-2 text-primary"></i>Informasi Surat
                </h6>
            </div>
            <div class="section-body">
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Nomor Surat</div>
                        <div class="info-value">{{ $surat->nomor_surat ?? 'Belum digenerate' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Tanggal Surat</div>
                        <div class="info-value">{{ \Carbon\Carbon::parse($surat->tanggal_surat)->format('d F Y') }}</div>
                    </div>
                    <div class="info-item full-width">
                        <div class="info-label">Perihal</div>
                        <div class="info-value">{{ $surat->perihal }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Diupload Oleh</div>
                        <div class="info-value">
                            <strong>{{ $surat->pembuat->name }}</strong>
                            <span class="badge bg-secondary ms-2">{{ $surat->pembuat->jabatan }}</span>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">File Dokumen</div>
                        <div class="info-value">
                            <a href="{{ asset('storage/' . $surat->file_path) }}" target="_blank" class="btn btn-sm btn-danger">
                                <i class="fas fa-file-pdf me-1"></i>Lihat PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- History Log -->
        <div class="history-section bg-white rounded shadow-sm">
            <div class="section-header">
                <h6 class="mb-0">
                    <i class="fas fa-history me-2 text-primary"></i>Jejak Rekam Aktivitas
                </h6>
            </div>
            <div class="section-body">
                <div class="history-timeline">
                    @foreach($surat->logs as $log)
                    @php
                    $color = 'secondary';
                    $icon = 'fas fa-info-circle';
                    if($log->aksi == 'acc') {
                    $color = 'success';
                    $icon = 'fas fa-check-circle';
                    }
                    if($log->aksi == 'revisi') {
                    $color = 'warning';
                    $icon = 'fas fa-exclamation-triangle';
                    }
                    if($log->aksi == 'resubmit') {
                    $color = 'primary';
                    $icon = 'fas fa-upload';
                    }
                    if(str_contains($log->catatan_revisi, 'DITOLAK')) {
                    $color = 'danger';
                    $icon = 'fas fa-ban';
                    }
                    @endphp

                    <div class="history-item">
                        <div class="history-dot bg-{{ $color }}"></div>
                        <div class="history-content">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h6 class="history-title text-{{ $color }} mb-1">
                                        <i class="{{ $icon }} me-1"></i>
                                        @if($log->aksi == 'acc') Disetujui (ACC)
                                        @elseif($log->aksi == 'revisi')
                                        @if(str_contains($log->catatan_revisi, 'DITOLAK')) Ditolak Permanen
                                        @else Dikembalikan (Revisi) @endif
                                        @elseif($log->aksi == 'resubmit') Upload Ulang
                                        @else {{ ucfirst($log->aksi) }}
                                        @endif
                                    </h6>
                                    <p class="history-actor mb-0">
                                        Oleh: <strong>{{ $log->pengirim->name }}</strong>
                                        <span class="text-muted">({{ $log->pengirim->jabatan }})</span>
                                    </p>
                                </div>
                                <div class="text-end">
                                    <div class="history-date">{{ \Carbon\Carbon::parse($log->created_at)->format('d M Y') }}</div>
                                    <div class="history-time">{{ \Carbon\Carbon::parse($log->created_at)->format('H:i') }} WIB</div>
                                </div>
                            </div>
                            @if($log->catatan_revisi)
                            <div class="history-note">
                                <i class="fas fa-quote-left me-1"></i>{{ $log->catatan_revisi }}
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>

    <!-- Right Column: Actions -->
    <div class="col-lg-4">

        <!-- Panel Validasi untuk Pimpinan -->
        @if(Auth::id() == $surat->posisi_saat_ini && Auth::id() != $surat->pembuat_id)
        <div class="action-panel bg-primary text-white rounded shadow-sm mb-3">
            <div class="panel-header">
                <i class="fas fa-gavel me-2"></i>Panel Validasi
            </div>
            <div class="panel-body">
                <form action="{{ route('surat-keluar.update', $surat->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label small opacity-75">Catatan Validasi (Opsional)</label>
                        <textarea name="catatan" class="form-control" rows="3" placeholder="Tulis catatan atau instruksi..."></textarea>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" name="aksi" value="acc" class="btn btn-light fw-semibold">
                            <i class="fas fa-check-circle me-2"></i>Setujui (ACC)
                        </button>
                        <button type="submit" name="aksi" value="revisi" class="btn btn-warning fw-semibold text-dark">
                            <i class="fas fa-tools me-2"></i>Minta Revisi
                        </button>
                        <button type="submit" name="aksi" value="ditolak" class="btn btn-danger fw-semibold" onclick="return confirm('Yakin tolak surat ini secara permanen? Surat tidak akan bisa diedit lagi.');">
                            <i class="fas fa-ban me-2"></i>Tolak Mutlak
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif

        <!-- Panel Upload Revisi untuk Staff -->
        @if($surat->status_acc == 'revisi' && Auth::id() == $surat->pembuat_id)
        <div class="action-panel bg-warning bg-opacity-10 rounded shadow-sm mb-3">
            <div class="panel-header bg-warning text-dark">
                <i class="fas fa-upload me-2"></i>Upload Perbaikan
            </div>
            <div class="panel-body">
                <div class="alert alert-warning border-0 mb-3">
                    <i class="fas fa-info-circle me-2"></i>
                    <small>Surat Anda perlu diperbaiki. Silakan upload file yang sudah direvisi.</small>
                </div>

                @if($catatanRevisi)
                <div class="revision-note mb-3">
                    <small class="text-muted d-block mb-1">Catatan dari validator:</small>
                    <div class="bg-light p-2 rounded border">
                        <small class="fst-italic">"{{ $catatanRevisi->catatan_revisi }}"</small>
                    </div>
                </div>
                @endif

                <form action="{{ route('surat-keluar.update', $surat->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label small">File Surat (PDF/DOC)</label>
                        <input type="file" name="file_surat" class="form-control" required accept=".pdf,.doc,.docx">
                    </div>
                    <button class="btn btn-warning w-100 fw-semibold">
                        <i class="fas fa-paper-plane me-2"></i>Kirim Kembali
                    </button>
                </form>
            </div>
        </div>
        @endif

        <!-- Panel Ditolak -->
        @if($surat->status_acc == 'ditolak')
        <div class="action-panel bg-danger text-white rounded shadow-sm mb-3">
            <div class="panel-body text-center py-4">
                <i class="fas fa-ban fa-3x mb-3 opacity-75"></i>
                <h5 class="fw-semibold mb-2">Surat Ditolak</h5>
                <p class="mb-0 small opacity-75">
                    Mohon maaf, pengajuan surat ini telah ditolak secara permanen. Anda harus membuat pengajuan baru.
                </p>
            </div>
        </div>
        @endif

        <!-- Info Sidebar -->
        <div class="info-sidebar bg-light rounded shadow-sm p-3">
            <h6 class="fw-semibold mb-3">
                <i class="fas fa-lightbulb me-2 text-warning"></i>Informasi
            </h6>
            <ul class="list-unstyled small mb-0">
                <li class="mb-2">
                    <i class="fas fa-check-circle text-success me-2"></i>
                    Surat yang <strong>disetujui</strong> akan otomatis mendapat nomor surat
                </li>
                <li class="mb-2">
                    <i class="fas fa-tools text-warning me-2"></i>
                    Surat <strong>revisi</strong> dapat diperbaiki dan dikirim ulang
                </li>
                <li class="mb-0">
                    <i class="fas fa-ban text-danger me-2"></i>
                    Surat <strong>ditolak</strong> tidak dapat diperbaiki
                </li>
            </ul>
        </div>

    </div>
</div>

<style>
    /* Timeline Tracking */
    .tracking-section {
        border: 1px solid #e9ecef;
    }

    .timeline-steps {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 0;
    }

    .timeline-step {
        align-items: center;
        display: flex;
        flex-direction: column;
        position: relative;
        margin: 0 30px;
    }

    .timeline-step:not(:last-child):after {
        content: "";
        position: absolute;
        top: 15px;
        left: 50%;
        width: 60px;
        height: 3px;
        background-color: #e9ecef;
        z-index: 1;
    }

    .timeline-content {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #e9ecef;
        color: #6c757d;
        display: flex;
        justify-content: center;
        align-items: center;
        font-weight: 600;
        font-size: 0.875rem;
        z-index: 2;
        position: relative;
        border: 3px solid #fff;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .timeline-label {
        margin-top: 10px;
        font-size: 0.75rem;
        font-weight: 600;
        color: #6c757d;
        text-align: center;
    }

    /* Timeline States */
    .timeline-step.active .timeline-content {
        background-color: #ffc107;
        color: #000;
        box-shadow: 0 0 0 4px rgba(255, 193, 7, 0.2);
        animation: pulse-warning 2s infinite;
    }

    .timeline-step.done .timeline-content {
        background-color: #198754;
        color: #fff;
    }

    .timeline-step.done .timeline-label {
        color: #198754;
    }

    .timeline-step.done:not(:last-child):after {
        background-color: #198754;
    }

    .timeline-step.rejected .timeline-content {
        background-color: #ffc107;
        color: #000;
    }

    .timeline-step.rejected .timeline-label {
        color: #ffc107;
    }

    .timeline-step.die .timeline-content {
        background-color: #dc3545;
        color: #fff;
        border-color: #dc3545;
    }

    .timeline-step.die .timeline-label {
        color: #dc3545;
        text-decoration: line-through;
    }

    @keyframes pulse-warning {

        0%,
        100% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.1);
        }
    }

    /* Status Card */
    .status-card {
        border: 1px solid #e9ecef;
    }

    /* Sections */
    .info-section,
    .history-section,
    .action-panel {
        border: 1px solid #e9ecef;
    }

    .section-header,
    .panel-header {
        padding: 0.875rem 1rem;
        border-bottom: 1px solid #e9ecef;
        background: #f8f9fa;
    }

    .section-header h6,
    .panel-header {
        font-size: 0.875rem;
        font-weight: 600;
        margin: 0;
    }

    .section-body,
    .panel-body {
        padding: 1rem;
    }

    /* Info Grid */
    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }

    .info-item {
        display: flex;
        flex-direction: column;
    }

    .info-item.full-width {
        grid-column: 1 / -1;
    }

    .info-label {
        font-size: 0.75rem;
        color: #6c757d;
        margin-bottom: 0.25rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .info-value {
        font-size: 0.875rem;
        color: #212529;
    }

    /* History Timeline */
    .history-timeline {
        position: relative;
        padding-left: 30px;
    }

    .history-timeline::before {
        content: '';
        position: absolute;
        left: 8px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e9ecef;
    }

    .history-item {
        position: relative;
        margin-bottom: 1.5rem;
    }

    .history-item:last-child {
        margin-bottom: 0;
    }

    .history-dot {
        position: absolute;
        left: -26px;
        top: 4px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        border: 2px solid #fff;
        z-index: 2;
    }

    .history-content {
        background: #f8f9fa;
        padding: 0.875rem;
        border-radius: 6px;
        border: 1px solid #e9ecef;
    }

    .history-title {
        font-size: 0.875rem;
        font-weight: 600;
        margin: 0;
    }

    .history-actor {
        font-size: 0.813rem;
        color: #495057;
    }

    .history-date {
        font-size: 0.75rem;
        font-weight: 600;
        color: #212529;
    }

    .history-time {
        font-size: 0.7rem;
        color: #6c757d;
    }

    .history-note {
        background: #fff;
        padding: 0.75rem;
        border-radius: 4px;
        border-left: 3px solid #dee2e6;
        font-size: 0.813rem;
        font-style: italic;
        color: #495057;
        margin-top: 0.75rem;
    }

    /* Action Panel */
    .action-panel .panel-header {
        background: rgba(255, 255, 255, 0.1);
        color: white;
        border-color: rgba(255, 255, 255, 0.2);
    }

    .action-panel.bg-warning .panel-header {
        color: #000;
    }

    /* Info Sidebar */
    .info-sidebar {
        border: 1px solid #dee2e6;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .timeline-step {
            margin: 0 15px;
        }

        .timeline-step:not(:last-child):after {
            width: 30px;
        }

        .info-grid {
            grid-template-columns: 1fr;
        }

        .info-item.full-width {
            grid-column: 1;
        }
    }
</style>

@endsection