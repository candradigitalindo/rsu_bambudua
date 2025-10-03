@extends('layouts.app')
@section('title', 'Edit Produk Apotek')
@push('style')
    <!-- Scrollbar CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        a.disabled {
            /* Make the disabled links grayish*/
            color: gray;
            /* And disable the pointer events */
            pointer-events: none;
        }

        .select2-container {
            width: 100% !important;
        }

        .select2-container--default .select2-selection--single {
            height: 38px;
            padding: 6px 12px;
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
        }

        .select2-selection__arrow {
            height: 36px;
        }
    </style>
@endpush
@section('content')
    <div class="row gx-3">
        <div class="col-xxl-12 col-sm-12">
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edit Produk Apotek</h5>
                    <a href="{{ route('products.index') }}" class="btn btn-primary btn-sm" id="btnKembaliProduk">
                        <span class="spinner-border spinner-border-sm d-none" id="spinnerKembaliProduk" role="status"
                            aria-hidden="true"></span>
                        <span id="textKembaliProduk">Kembali</span>
                    </a>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    <form action="{{ route('products.update', $product->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="code" class="form-label">Kode Produk</label>
                            <input type="text" class="form-control @error('code') is-invalid @enderror" id="code"
                                name="code" value="{{ old('code', $product->code) }}" disabled required autofocus>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="category_id" class="form-label">Kategori</label>
                            <select class="form-select @error('category_id') is-invalid @enderror" id="category_id"
                                name="category_id" required>
                                <option value="" disabled>Pilih Kategori</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Produk</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ old('name', $product->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="harga" class="form-label">Harga</label>
                            <input type="text" class="form-control @error('harga') is-invalid @enderror" id="harga"
                                name="harga" value="{{ old('harga', number_format($product->harga, 0, ',', '.')) }}"
                                required>
                            @error('harga')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="warning_stok" class="form-label">Warning Stok</label>
                            <input type="number" class="form-control @error('warning_stok') is-invalid @enderror"
                                id="warning_stok" name="warning_stok"
                                value="{{ old('warning_stok', $product->warning_stok) }}" required>
                            @error('warning_stok')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3" id="expired_warning_group">
                            <label for="expired_warning" class="form-label">Warning Expired (Hari)</label>
                            <input type="number" class="form-control @error('expired_warning') is-invalid @enderror"
                                id="expired_warning" name="expired_warning"
                                value="{{ old('expired_warning', $product->expired_warning) }}">
                            @error('expired_warning')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="satuan" class="form-label">Satuan</label>
                            <select class="form-select @error('satuan') is-invalid @enderror" id="satuan" name="satuan" required>
                                <option value="" disabled>Pilih Satuan</option>
                                @foreach(($units ?? []) as $unit)
                                    <option value="{{ $unit->name }}" {{ old('satuan', $product->satuan) == $unit->name ? 'selected' : '' }}>
                                        {{ $unit->name }} @if($unit->abbrev) ({{ $unit->abbrev }}) @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('satuan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="type" class="form-label">Tipe</label>
                            <select class="form-select @error('type') is-invalid @enderror" id="type" name="type"
                                required>
                                <option value="" disabled
                                    {{ old('type', $product->type) === null ? 'selected' : '' }}>Pilih Tipe</option>
                                <option value="0" {{ old('type', $product->type) == 0 ? 'selected' : '' }}>Obat Resep
                                </option>
                                <option value="1" {{ old('type', $product->type) == 1 ? 'selected' : '' }}>Non Resep
                                </option>
                                <option value="2" {{ old('type', $product->type) == 2 ? 'selected' : '' }}>Umum
                                </option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="expired" class="form-label">Expired</label>
                            <select class="form-select @error('expired') is-invalid @enderror" id="expired"
                                name="expired" required>
                                <option value="" disabled
                                    {{ old('expired', $product->expired) === null ? 'selected' : '' }}>Pilih Expired
                                </option>
                                <option value="0" {{ old('expired', $product->expired) == 0 ? 'selected' : '' }}>Ada
                                    Expired</option>
                                <option value="1" {{ old('expired', $product->expired) == 1 ? 'selected' : '' }}>
                                    Tidak Ada Expired</option>
                            </select>
                            @error('expired')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('products.index') }}" class="btn btn-secondary" id="btnKembaliProduk">
                                <span class="spinner-border spinner-border-sm d-none" id="spinnerKembaliProduk"
                                    role="status" aria-hidden="true"></span>
                                <span id="textKembaliProduk">Kembali</span>
                            </a>
                            <button type="submit" class="btn btn-primary" id="btnSimpanProduk">
                                <span class="spinner-border spinner-border-sm d-none" id="spinnerSimpanProduk"
                                    role="status" aria-hidden="true"></span>
                                <span id="textSimpanProduk">Simpan</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <!-- Overlay Scroll JS -->
    <script src="{{ asset('vendor/overlay-scroll/jquery.overlayScrollbars.min.js') }}"></script>
    <script src="{{ asset('vendor/overlay-scroll/custom-scrollbar.js') }}"></script>
    <script src="{{ asset('js/custom.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $('#category_id').select2({
                placeholder: 'Pilih Kategori',
                allowClear: true,
                width: '100%'
            });
            // Loading untuk tombol Simpan
            const btnSimpan = document.getElementById('btnSimpanProduk');
            const spinnerSimpan = document.getElementById('spinnerSimpanProduk');
            const textSimpan = document.getElementById('textSimpanProduk');
            if (btnSimpan) {
                btnSimpan.form.addEventListener('submit', function() {
                    btnSimpan.disabled = true;
                    spinnerSimpan.classList.remove('d-none');
                    textSimpan.textContent = 'Menyimpan...';
                });
            }
            // Loading untuk tombol Kembali
            const btnKembali = document.getElementById('btnKembaliProduk');
            const spinnerKembali = document.getElementById('spinnerKembaliProduk');
            const textKembali = document.getElementById('textKembaliProduk');
            if (btnKembali) {
                btnKembali.addEventListener('click', function() {
                    spinnerKembali.classList.remove('d-none');
                    textKembali.textContent = 'Loading...';
                });
            }

            // Toggle visibility of expired_warning based on expired dropdown
            const expiredSelect = document.getElementById('expired');
            const expiredWarningGroup = document.getElementById('expired_warning_group');
            const expiredWarningInput = document.getElementById('expired_warning');

            function toggleExpiredWarning() {
                if (expiredSelect.value === '0') { // '0' is for "Ada Expired"
                    expiredWarningGroup.style.display = 'block';
                    expiredWarningInput.required = true;
                } else {
                    expiredWarningGroup.style.display = 'none';
                    expiredWarningInput.required = false;
                }
            }

            expiredSelect.addEventListener('change', toggleExpiredWarning);
            // Initial check on page load
            toggleExpiredWarning();
        });

        function formatRupiah(angka, prefix) {
            var number_string = angka.replace(/[^,\d]/g, '').toString(),
                split = number_string.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                var separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }
            rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
            return prefix === undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
        }

        const hargaInput = document.getElementById('harga');
        if (hargaInput) {
            hargaInput.addEventListener('keyup', function(e) {
                this.value = formatRupiah(this.value, 'Rp. ');
            });
            // Format saat halaman load
            hargaInput.value = formatRupiah(hargaInput.value, 'Rp. ');
        }
    </script>
@endpush
