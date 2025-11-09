@extends('layouts.app')
@section('title', 'Tambah / Kurang Stok')

@push('style')
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
@endpush

@section('content')
    <div class="row gx-3">
        <div class="col-xxl-12 col-lg-8 col-md-10 mx-auto">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Tambah / Kurang Stok {{ $product->name ?? '' }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('product.storeStock', $product->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="type" class="form-label">Tipe</label>
                            <select class="form-select @error('type') is-invalid @enderror" id="type" name="type"
                                required>
                                <option value="" disabled selected>Pilih Tipe</option>
                                <option value="0" {{ old('type') == '0' ? 'selected' : '' }}>Stok Masuk</option>
                                <option value="1" {{ old('type') == '1' ? 'selected' : '' }}>Stok Keluar</option>
                                <option value="2" {{ old('type') == '2' ? 'selected' : '' }}>Stok Keluar Expired
                                </option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="stok" class="form-label">Stok</label>
                            <input type="number" class="form-control @error('stok') is-invalid @enderror" id="stok"
                                name="stok" value="{{ old('stok', $product->stok ?? 0) }}" min="0" required>
                            @error('stok')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        @if ($product->expired == 0)
                            <div class="mb-3" id="tanggalExpiredGroup">
                                <label for="expired_at" class="form-label">Tanggal Expired</label>
                                <input type="date" class="form-control @error('expired_at') is-invalid @enderror"
                                    id="expired_at" name="expired_at"
                                    value="{{ old('expired_at', $product->expired_at ?? '') }}">
                                @error('expired_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif
                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <textarea class="form-control @error('keterangan') is-invalid @enderror" id="keterangan" name="keterangan"
                                rows="2" placeholder="Tulis keterangan jika perlu...">{{ old('keterangan', $product->keterangan ?? '') }}</textarea>
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('products.index') }}" class="btn btn-secondary" id="btnKembaliStok">
                                <span class="spinner-border spinner-border-sm d-none" id="spinnerKembaliStok" role="status"
                                    aria-hidden="true"></span>
                                <span id="textKembaliStok">Kembali</span>
                            </a>
                            <button type="submit" class="btn btn-primary" id="btnSimpanStok">
                                <span class="spinner-border spinner-border-sm d-none" id="spinnerSimpanStok" role="status"
                                    aria-hidden="true"></span>
                                <span id="textSimpanStok">Simpan</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script src="{{ asset('vendor/overlay-scroll/jquery.overlayScrollbars.min.js') }}"></script>
        <script src="{{ asset('assets/js/custom-scrollbar.js') }}"></script>
        <script src="{{ asset('js/custom.js') }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Spinner tombol kembali
                const btnKembali = document.getElementById('btnKembaliStok');
                const spinnerKembali = document.getElementById('spinnerKembaliStok');
                const textKembali = document.getElementById('textKembaliStok');
                if (btnKembali) {
                    btnKembali.addEventListener('click', function() {
                        spinnerKembali.classList.remove('d-none');
                        textKembali.textContent = 'Loading...';
                    });
                }
                // Spinner tombol simpan
                const btnSimpan = document.getElementById('btnSimpanStok');
                const spinnerSimpan = document.getElementById('spinnerSimpanStok');
                const textSimpan = document.getElementById('textSimpanStok');
                if (btnSimpan) {
                    btnSimpan.form.addEventListener('submit', function() {
                        btnSimpan.disabled = true;
                        spinnerSimpan.classList.remove('d-none');
                        textSimpan.textContent = 'Menyimpan...';
                    });
                }
                // Tidak boleh minus
                const stokInput = document.getElementById('stok');
                stokInput.addEventListener('input', function() {
                    if (this.value < 0) {
                        this.value = 0;
                    }
                });
                // Toggle tanggal expired
                function toggleTanggalExpired() {
                    var typeSelect = document.getElementById('type');
                    var tanggalGroup = document.getElementById('tanggalExpiredGroup');
                    if (typeSelect.value == '0') {
                        tanggalGroup.style.display = '';
                    } else {
                        tanggalGroup.style.display = 'none';
                        document.getElementById('expired_at').value = '';
                    }
                }
                var typeSelect = document.getElementById('type');
                if (typeSelect) {
                    typeSelect.addEventListener('change', toggleTanggalExpired);
                    toggleTanggalExpired(); // initial check
                }
            });
        </script>
    @endpush
@endsection
