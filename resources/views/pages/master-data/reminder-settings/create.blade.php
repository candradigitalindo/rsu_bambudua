@extends('layouts.app')

@section('title', 'Tambah Reminder')
@push('style')
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
@endpush

@section('content')
    <div class="row gx-3">
        <div class="col-xxl-8 col-lg-10 mx-auto">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title">Tambah Pengaturan Reminder</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('reminder-settings.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Reminder <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ old('name') }}" placeholder="Contoh: Reminder Beli Obat Lagi"
                                required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="type" class="form-label">Tipe Reminder <span
                                    class="text-danger">*</span></label>
                            <select class="form-select @error('type') is-invalid @enderror" id="type" name="type"
                                required>
                                <option value="">-- Pilih Tipe --</option>
                                <option value="obat" {{ old('type') == 'obat' ? 'selected' : '' }}>Beli Obat</option>
                                <option value="checkup" {{ old('type') == 'checkup' ? 'selected' : '' }}>Check Up / Kontrol
                                </option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                • <strong>Beli Obat:</strong> Reminder berdasarkan masa pemakaian obat<br>
                                • <strong>Check Up:</strong> Reminder untuk kontrol kembali
                            </small>
                        </div>

                        <div class="mb-3">
                            <label for="days_before" class="form-label">Kirim Reminder Setelah/Dari (Hari) <span
                                    class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('days_before') is-invalid @enderror"
                                id="days_before" name="days_before" value="{{ old('days_before', 2) }}" min="1"
                                max="365" required>
                            @error('days_before')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                • <strong>Obat:</strong> Reminder akan dikirim X hari <strong>setelah</strong> obat habis
                                (dihitung dari tanggal resep + masa pemakaian)<br>
                                • <strong>Check Up:</strong> Reminder akan dikirim X hari <strong>dari</strong> tanggal
                                kunjungan terakhir
                            </small>
                        </div>

                        <div class="mb-3">
                            <label for="message_template" class="form-label">Template Pesan</label>
                            <textarea class="form-control @error('message_template') is-invalid @enderror" id="message_template"
                                name="message_template" rows="15" style="white-space: pre-wrap; font-family: monospace;"
                                placeholder="Contoh:&#10;*Bambu Dua Clinic* – *Pengingat*&#10;&#10;Halo {nama_pasien},&#10;Reminder untuk kontrol kesehatan...">{{ old('message_template') }}</textarea>
                            @error('message_template')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Variabel yang bisa digunakan:<br>
                                • <code>{nama_pasien}</code> - Nama pasien<br>
                                • <code>{rekam_medis}</code> - Nomor rekam medis<br>
                                • <code>{hari}</code> - Jumlah hari setelah obat habis / dari kunjungan terakhir<br>
                                • <code>{tanggal}</code> - Tanggal masa habis/kontrol<br>
                                <strong>Tips:</strong> Gunakan *text* untuk bold di WhatsApp. Enter akan tersimpan sebagai
                                baris baru.
                            </small>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input type="hidden" name="is_active" value="0">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                    value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Aktifkan Reminder
                                </label>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="ri-save-line"></i> Simpan
                            </button>
                            <a href="{{ route('reminder-settings.index') }}" class="btn btn-secondary">
                                <i class="ri-arrow-left-line"></i> Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="{{ asset('vendor/overlay-scroll/jquery.overlayScrollbars.min.js') }}"></script>
    <script src="{{ asset('vendor/overlay-scroll/custom-scrollbar.js') }}"></script>
    <script src="{{ asset('js/custom.js') }}"></script>
@endpush
