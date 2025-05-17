@extends('layouts.app')
@section('title', 'Tambah Produk Apotek')
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
        /* Pastikan Select2 full width seperti input lain */
        .select2-container--default .select2-selection--single {
            height: 38px;
            padding: 6px 12px;
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
        }
        .select2-container {
            width: 100% !important;
        }
        .select2-selection__rendered {
            line-height: 24px;
        }
        .select2-selection__arrow {
            height: 36px;
        }
    </style>
@endpush
@section('content')
    <div class="row gx-3">
        <div class="col-xxl-12 col-lg-8 col-md-10 mx-auto">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Tambah Produk Apotek</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Produk</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name') }}" required autofocus>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="category_id" class="form-label">Kategori</label>
                            <select class="form-select @error('category_id') is-invalid @enderror" id="category_id"
                                    name="category_id" required>
                                <option value="" disabled selected>Pilih Kategori</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="satuan" class="form-label">Satuan</label>
                            <input type="text" class="form-control @error('satuan') is-invalid @enderror"
                                   id="satuan" name="satuan" value="{{ old('satuan') }}" required>
                            @error('satuan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="harga" class="form-label">Harga</label>
                            <input type="text" class="form-control @error('harga') is-invalid @enderror"
                                   id="harga" name="harga" value="{{ old('harga') }}" required>
                            @error('harga')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        {{-- column Select type --}}
                        <div class="mb-3">
                            <label for="type" class="form-label">Tipe</label>
                            <select class="form-select @error('type') is-invalid @enderror" id="type"
                                    name="type" required>
                                <option value="" disabled selected>Pilih Tipe</option>
                                <option value="0" {{ old('type') == 0 ? 'selected' : '' }}>Obat Resep</option>
                                <option value="1" {{ old('type') == 1 ? 'selected' : '' }}>Non Resep</option>
                                <option value="2" {{ old('type') == 2 ? 'selected' : '' }}>Umum</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        {{-- column select apakah ada expired --}}
                        <div class="mb-3">
                            <label for="expired" class="form-label">Expired</label>
                            <select class="form-select @error('expired') is-invalid @enderror" id="expired"
                                    name="expired" required>
                                <option value="" disabled selected>Pilih Expired</option>
                                <option value="0" {{ old('expired') == 0 ? 'selected' : '' }}>Ada Expired</option>
                                <option value="1" {{ old('expired') == 1 ? 'selected' : '' }}>Tidak Ada Expired</option>
                            </select>
                            @error('expired')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        {{-- input warning stok --}}
                        <div class="mb-3">
                            <label for="warning_stok" class="form-label">Warning Stok</label>
                            <input type="number" class="form-control @error('warning_stok') is-invalid @enderror"
                                   id="warning_stok" name="warning_stok" value="{{ old('warning_stok') }}" required>
                            @error('warning_stok')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('products.index') }}" class="btn btn-secondary" id="btnKembaliProduk">
                                <span class="spinner-border spinner-border-sm d-none" id="spinnerKembaliProduk" role="status" aria-hidden="true"></span>
                                <span id="textKembaliProduk">Kembali</span>
                            </a>
                            <button type="submit" class="btn btn-primary" id="btnSimpanProduk">
                                <span class="spinner-border spinner-border-sm d-none" id="spinnerSimpanProduk" role="status" aria-hidden="true"></span>
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
            if(btnSimpan) {
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
            if(btnKembali) {
                btnKembali.addEventListener('click', function() {
                    spinnerKembali.classList.remove('d-none');
                    textKembali.textContent = 'Loading...';
                });
            }
        });
        // Funngsi format rupiah waktu kolom harga diketik
        function formatRupiah(angka, prefix) {
            var number_string = angka.replace(/[^,\d]/g, '').toString(),
                split = number_string.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);
            if (ribuan) {
                separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }
            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
        }
        // Event listener untuk kolom harga
        const hargaInput = document.getElementById('harga');
        if (hargaInput) {
            hargaInput.addEventListener('keyup', function(e) {
                this.value = formatRupiah(this.value, 'Rp. ');
            });
        }
    </script>
@endpush
