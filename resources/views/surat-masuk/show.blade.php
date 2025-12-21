@extends('layouts.main')

@section('title', 'Detail Surat Masuk')

@section('content')

<div class="row">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="m-0 fw-bold text-dark">
                    <i class="fas fa-envelope-open-text text-primary me-2"></i>
                    Informasi Surat
                </h6>
            </div>
            <div class="card-body p-4">
                <div class="mb-3 pb-3 border-bottom">
                    <small class="text-muted text-uppercase d-block mb-2">Nomor Surat</small>
                    <h6 class="fw-bold text-dark mb-0">{{ $surat->nomor_surat }}</h6>
                </div>

                <div class="mb-3 pb-3 border-bottom">
                    <small class="text-muted text-uppercase d-block mb-2">Pengirim</small>
                    <h6 class="fw-bold text-dark mb-0">{{ $surat->pengirim }}</h6>
                </div>

                <div class="mb-3 pb-3 border-bottom">
                    <small class="text-muted text-uppercase d-block mb-2">Tanggal Surat</small>
                    <div class="d-flex align-items-center">
                        <div class="bg-light border rounded p-2 me-3 text-center" style="min-width: 55px;">
                            <div class="text-primary fw-bold" style="font-size: 18px; line-height: 1;">
                                {{ \Carbon\Carbon::parse($surat->tanggal_surat)->format('d') }}
                            </div>
                            <div class="text-muted small">
                                {{ \Carbon\Carbon::parse($surat->tanggal_surat)->translatedFormat('M Y') }}
                            </div>
                        </div>
                        <div>
                            <div class="fw-semibold text-dark">
                                {{ \Carbon\Carbon::parse($surat->tanggal_surat)->translatedFormat('l') }}
                            </div>
                            <small class="text-muted">
                                {{ \Carbon\Carbon::parse($surat->tanggal_surat)->translatedFormat('d F Y') }}
                            </small>
                        </div>
                    </div>
                </div>

                <div class="mb-3 pb-3 border-bottom">
                    <small class="text-muted text-uppercase d-block mb-2">Perihal</small>
                    <p class="text-dark mb-0">{{ $surat->perihal }}</p>
                </div>

                <div class="alert alert-light mb-3">
                    <small class="text-muted d-block mb-1">
                        <i class="fas fa-clock me-1"></i>Waktu Input Sistem
                    </small>
                    <strong class="text-dark">
                        {{ \Carbon\Carbon::parse($surat->created_at)->translatedFormat('d F Y, H:i') }} WIB
                    </strong>
                </div>

                <a href="{{ asset('storage/' . $surat->file_path) }}" target="_blank" class="btn btn-primary w-100 py-2">
                    <i class="fas fa-file-pdf me-2"></i>Lihat File Surat
                </a>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white py-3">
                <div class="d-flex align-items-center justify-content-between">
                    <h6 class="m-0 fw-bold">
                        <i class="fas fa-route me-2"></i>
                        Riwayat Perjalanan Surat
                    </h6>
                    <span class="badge bg-white text-primary fw-bold">
                        {{ $surat->disposisi->count() + 1 }} Tahapan
                    </span>
                </div>
            </div>
            <div class="card-body p-4">

                <div class="timeline-wrapper position-relative">

                    <!-- Timeline Start - Admin Input -->
                    <div class="timeline-item d-flex mb-3 position-relative">
                        <div class="timeline-line position-absolute" style="left: 20px; top: 45px; bottom: -10px; width: 2px; background: #e5e7eb;"></div>

                        <div class="flex-shrink-0 me-3 position-relative" style="z-index: 2;">
                            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="fas fa-check"></i>
                            </div>
                        </div>

                        <div class="flex-grow-1">
                            <div class="bg-light rounded p-3">
                                <h6 class="fw-bold mb-1 text-dark">
                                    Diterima Admin (Loket)
                                </h6>
                                <p class="small text-muted mb-2">Surat masuk dan diinput ke sistem</p>
                                <span class="badge bg-primary">
                                    <i class="fas fa-clock me-1"></i>
                                    {{ \Carbon\Carbon::parse($surat->created_at)->translatedFormat('d F Y, H:i') }} WIB
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Timeline Disposisi -->
                    @foreach($surat->disposisi as $index => $disp)
                    <div class="timeline-item d-flex mb-3 position-relative">
                        @if($index < $surat->disposisi->count() - 1)
                            <div class="timeline-line position-absolute" style="left: 20px; top: 45px; bottom: -10px; width: 2px; background: #e5e7eb;"></div>
                            @endif

                            <div class="flex-shrink-0 me-3 position-relative" style="z-index: 2;">
                                @php
                                $bgColor = 'bg-secondary';
                                if($disp->penerima->role == 'kabid') $bgColor = 'bg-warning';
                                if($disp->penerima->role == 'kasi') $bgColor = 'bg-info';
                                if($disp->penerima->role == 'staff') $bgColor = 'bg-success';
                                @endphp
                                <div class="{{ $bgColor }} text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="fas fa-user"></i>
                                </div>
                            </div>

                            <div class="flex-grow-1">
                                <div class="bg-white border rounded p-3">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h6 class="fw-bold mb-0 text-dark">{{ $disp->penerima->name }}</h6>
                                            <small class="text-muted">{{ $disp->penerima->jabatan }}</small>
                                        </div>
                                        @if($disp->is_read)
                                        <span class="badge bg-success">
                                            <i class="fas fa-check-double me-1"></i>Dibaca
                                        </span>
                                        @else
                                        <span class="badge bg-danger">
                                            <i class="fas fa-clock me-1"></i>Belum Dibaca
                                        </span>
                                        @endif
                                    </div>

                                    <small class="text-muted d-block mb-2">
                                        <i class="fas fa-user-tie me-1"></i>Dari: {{ $disp->pengirim->name }}
                                    </small>

                                    <div class="bg-primary bg-opacity-10 border-start border-primary border-3 p-2 rounded mb-2">
                                        <small class="text-muted d-block mb-1">
                                            <i class="fas fa-comment-dots me-1"></i>Instruksi:
                                        </small>
                                        <p class="mb-0 text-dark fst-italic small">"{{ $disp->instruksi }}"</p>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center small text-muted">
                                        <div>
                                            <i class="fas fa-paper-plane me-1"></i>
                                            Dikirim: {{ \Carbon\Carbon::parse($disp->created_at)->translatedFormat('d M Y, H:i') }} WIB
                                        </div>

                                        @if($disp->is_read)
                                        <div class="text-success">
                                            <i class="fas fa-check-double me-1"></i>
                                            Dibaca: {{ \Carbon\Carbon::parse($disp->read_at)->translatedFormat('d M Y, H:i') }} WIB
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                    </div>
                    @endforeach

                    <!-- Timeline End -->
                    <div class="timeline-item d-flex">
                        <div class="flex-shrink-0 me-3">
                            <div class="bg-light border border-2 border-success rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="fas fa-flag-checkered text-success"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="alert alert-success mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                @if($surat->disposisi->last() && $surat->disposisi->last()->is_read)
                                Surat telah diterima dan dibaca oleh penerima akhir
                                @else
                                Menunggu tindak lanjut dari penerima
                                @endif
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>

<style>
    .timeline-wrapper {
        padding-left: 0;
    }

    .timeline-item:hover .border {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        transition: box-shadow 0.3s ease;
    }
</style>

@endsection