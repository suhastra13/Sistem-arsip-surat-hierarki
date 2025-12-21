@extends('layouts.main')

@section('title', 'Proses Disposisi')

@section('content')

<!-- Progress Indicator -->
<div class="mb-4">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h4 class="mb-1 fw-bold text-dark">Proses Disposisi Surat</h4>
            <p class="text-muted mb-0 small">Baca dokumen dan teruskan kepada bawahan jika diperlukan</p>
        </div>
        <div class="badge bg-success px-3 py-2">
            <i class="fas fa-eye me-1"></i> Sudah Dibaca
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Left Column: Document Info -->
    <div class="col-lg-7">
        <!-- Incoming Instruction Alert -->
        <div class="alert alert-warning border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #fff3cd 0%, #fff8e1 100%);">
            <div class="d-flex align-items-start">
                <div class="flex-shrink-0">
                    <div class="bg-warning bg-opacity-25 rounded-circle p-2">
                        <i class="fas fa-comment-dots fa-lg text-warning"></i>
                    </div>
                </div>
                <div class="flex-grow-1 ms-3">
                    <h6 class="alert-heading fw-bold mb-1">Instruksi dari {{ $disposisi->pengirim->name }}</h6>
                    <p class="mb-0 fst-italic">"{{ $disposisi->instruksi }}"</p>
                    <small class="text-muted">
                        <i class="fas fa-clock me-1"></i> {{ $disposisi->read_at }}
                    </small>
                </div>
            </div>
        </div>

        <!-- Document Details Card -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <div class="d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 rounded p-2 me-3">
                        <i class="fas fa-envelope-open-text fa-lg text-primary"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold">Detail Surat Masuk</h6>
                        <small class="text-muted">Informasi lengkap dokumen</small>
                    </div>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-12">
                        <div class="d-flex align-items-start">
                            <div class="text-muted me-3" style="min-width: 120px;">
                                <i class="fas fa-building me-2"></i>
                                <span class="small fw-bold">PENGIRIM</span>
                            </div>
                            <div class="fw-bold text-dark">{{ $disposisi->surat->pengirim }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-start">
                            <div class="text-muted me-3" style="min-width: 120px;">
                                <i class="fas fa-hashtag me-2"></i>
                                <span class="small fw-bold">NO. SURAT</span>
                            </div>
                            <div>{{ $disposisi->surat->nomor_surat }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-start">
                            <div class="text-muted me-3" style="min-width: 120px;">
                                <i class="fas fa-calendar me-2"></i>
                                <span class="small fw-bold">TANGGAL</span>
                            </div>
                            <div>{{ \Carbon\Carbon::parse($disposisi->surat->tanggal_surat)->translatedFormat('d F Y') }}</div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex align-items-start">
                            <div class="text-muted me-3" style="min-width: 120px;">
                                <i class="fas fa-file-alt me-2"></i>
                                <span class="small fw-bold">PERIHAL</span>
                            </div>
                            <div class="flex-grow-1">
                                <div class="bg-light border rounded p-3">
                                    {{ $disposisi->surat->perihal }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <div class="d-grid">
                    <a href="{{ asset('storage/' . $disposisi->surat->file_path) }}" target="_blank" class="btn btn-danger btn-lg shadow-sm">
                        <i class="fas fa-file-pdf me-2"></i>
                        Buka Dokumen PDF
                        <i class="fas fa-external-link-alt ms-2 small"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Action Panel -->
    <div class="col-lg-5">
        @if($bawahan->count() > 0)
        <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
            <div class="card-header text-white py-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <h6 class="mb-0 fw-bold">
                    <i class="fas fa-share-square me-2"></i>
                    Teruskan Disposisi
                </h6>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('disposisi.update', $disposisi->id) }}" method="POST" id="disposisiForm">
                    @csrf
                    @method('PUT')

                    <!-- Step 1: Select Recipients -->
                    <div class="mb-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <label class="form-label fw-bold mb-0">
                                <span class="badge bg-primary rounded-circle me-2">1</span>
                                Pilih Penerima
                            </label>
                            <span class="badge bg-secondary" id="selectedCount">0 dipilih</span>
                        </div>

                        <!-- Select All -->
                        <div class="card mb-3 border-primary" style="border-width: 2px; border-style: dashed !important;">
                            <div class="card-body py-2 px-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="checkAll">
                                    <label class="form-check-label fw-semibold text-primary" for="checkAll">
                                        <i class="fas fa-users me-1"></i>
                                        Pilih Semua ({{ $bawahan->count() }} orang)
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Recipients List -->
                        <div class="border rounded" style="max-height: 280px; overflow-y: auto; background: #f8f9fa;">
                            @foreach($bawahan as $staf)
                            <div class="form-check p-3 border-bottom recipient-item" style="cursor: pointer; transition: all 0.2s;">
                                <input class="form-check-input bawahan-checkbox" type="checkbox" name="tujuan_id[]" value="{{ $staf->id }}" id="staf{{ $staf->id }}">
                                <label class="form-check-label w-100 d-flex align-items-center" for="staf{{ $staf->id }}" style="cursor: pointer;">
                                    <div class="bg-white rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm" style="width: 42px; height: 42px; min-width: 42px;">
                                        <i class="fas fa-user text-primary"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-bold text-dark">{{ $staf->name }}</div>
                                        <small class="text-muted">
                                            <i class="fas fa-briefcase me-1"></i>{{ $staf->jabatan }}
                                        </small>
                                    </div>
                                </label>
                            </div>
                            @endforeach
                        </div>

                        <div class="form-text mt-2">
                            <i class="fas fa-info-circle me-1"></i>
                            Centang minimal 1 penerima untuk melanjutkan
                        </div>
                    </div>

                    <!-- Step 2: Write Instruction -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            <span class="badge bg-primary rounded-circle me-2">2</span>
                            Tulis Instruksi
                        </label>
                        <textarea name="instruksi" class="form-control" rows="5" placeholder="Contoh: Mohon untuk segera ditindaklanjuti dan berkoordinasi dengan unit terkait..." required></textarea>
                        <div class="form-text mt-2">
                            <i class="fas fa-lightbulb me-1"></i>
                            Berikan instruksi yang jelas dan spesifik
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg fw-bold shadow-sm" id="submitBtn" disabled>
                            <i class="fas fa-paper-plane me-2"></i>
                            Kirim Disposisi
                        </button>
                        <a href="{{ route('disposisi.index') }}" class="btn btn-light">
                            <i class="fas fa-arrow-left me-2"></i>
                            Kembali ke Inbox
                        </a>
                    </div>
                </form>
            </div>
        </div>
        @else
        <!-- Final Recipient State -->
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body p-5">
                <div class="mb-4">
                    <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                        <i class="fas fa-clipboard-check fa-3x text-success"></i>
                    </div>
                </div>
                <h5 class="fw-bold mb-3">Penerima Akhir</h5>
                <p class="text-muted mb-4">
                    Anda adalah penerima terakhir dalam rantai disposisi ini. Silakan laksanakan tugas sesuai instruksi yang diberikan.
                </p>
                <div class="d-grid gap-2">
                    <a href="{{ route('disposisi.index') }}" class="btn btn-primary">
                        <i class="fas fa-arrow-left me-2"></i>
                        Kembali ke Inbox
                    </a>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<style>
    .recipient-item:hover {
        background-color: white !important;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .recipient-item:last-child {
        border-bottom: none !important;
    }

    .sticky-top {
        position: sticky;
        z-index: 1020;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkAll = document.getElementById('checkAll');
        const checkboxes = document.querySelectorAll('.bawahan-checkbox');
        const selectedCount = document.getElementById('selectedCount');
        const submitBtn = document.getElementById('submitBtn');

        // Check All functionality
        checkAll?.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = this.checked);
            updateUI();
        });

        // Individual checkbox change
        checkboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                updateCheckAllState();
                updateUI();
            });
        });

        function updateCheckAllState() {
            const checkedCount = document.querySelectorAll('.bawahan-checkbox:checked').length;

            if (checkedCount === 0) {
                checkAll.checked = false;
                checkAll.indeterminate = false;
            } else if (checkedCount === checkboxes.length) {
                checkAll.checked = true;
                checkAll.indeterminate = false;
            } else {
                checkAll.checked = false;
                checkAll.indeterminate = true;
            }
        }

        function updateUI() {
            const checkedCount = document.querySelectorAll('.bawahan-checkbox:checked').length;

            // Update counter
            selectedCount.textContent = `${checkedCount} dipilih`;

            if (checkedCount > 0) {
                selectedCount.classList.remove('bg-secondary');
                selectedCount.classList.add('bg-success');
                submitBtn.disabled = false;
            } else {
                selectedCount.classList.remove('bg-success');
                selectedCount.classList.add('bg-secondary');
                submitBtn.disabled = true;
            }
        }

        // Initial state
        updateUI();
    });
</script>

@endsection