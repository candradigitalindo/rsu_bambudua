{{-- filepath: /Users/candrasyahputra/PROJEK-APLIKASI/bambudua/resources/views/pages/categories/create.blade.php --}}
@extends('layouts.app')
@section('title', 'Tambah Kategori')
@push('style')
    <!-- Scrollbar CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
    <style>
        a.disabled {
            /* Make the disabled links grayish*/
            color: gray;
            /* And disable the pointer events */
            pointer-events: none;
        }
    </style>
@endpush
@section('content')
<div class="row gx-3">
    <div class="col-xxl-12 col-lg-8 col-md-10 mx-auto">
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="mb-0">Tambah Kategori</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('categories.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Kategori</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                               id="name" name="name" value="{{ old('name') }}" required autofocus>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('categories.index') }}" class="btn btn-secondary" id="btnKembaliKategori">
                            <span class="spinner-border spinner-border-sm d-none" id="spinnerKembaliKategori" role="status" aria-hidden="true"></span>
                            <span id="textKembaliKategori">Kembali</span>
                        </a>
                        <button type="submit" class="btn btn-primary" id="btnSimpanKategori">
                            <span class="spinner-border spinner-border-sm d-none" id="spinnerSimpanKategori" role="status" aria-hidden="true"></span>
                            <span id="textSimpanKategori">Simpan</span>
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Loading untuk tombol Simpan
            const btnSimpan = document.getElementById('btnSimpanKategori');
            const spinnerSimpan = document.getElementById('spinnerSimpanKategori');
            const textSimpan = document.getElementById('textSimpanKategori');
            if(btnSimpan) {
                btnSimpan.form.addEventListener('submit', function() {
                    btnSimpan.disabled = true;
                    spinnerSimpan.classList.remove('d-none');
                    textSimpan.textContent = 'Menyimpan...';
                });
            }

            // Loading untuk tombol Kembali
            const btnKembali = document.getElementById('btnKembaliKategori');
            const spinnerKembali = document.getElementById('spinnerKembaliKategori');
            const textKembali = document.getElementById('textKembaliKategori');
            if(btnKembali) {
                btnKembali.addEventListener('click', function() {
                    spinnerKembali.classList.remove('d-none');
                    textKembali.textContent = 'Loading...';
                });
            }
        });
    </script>
@endpush
