@extends('layouts.app')
@section('title', 'Tambah Berita Baru')
@push('style')
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Form Tambah Berita</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('berita.store') }}" method="POST" enctype="multipart/form-data" id="news-form">
                        @csrf
                        <div class="mb-3">
                            <label for="judul" class="form-label">Judul Berita <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('judul') is-invalid @enderror" id="judul"
                                name="judul" value="{{ old('judul') }}" required>
                            @error('judul')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="konten" class="form-label">Konten <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('konten') is-invalid @enderror" id="konten" name="konten" rows="10"
                                required>{{ old('konten') }}</textarea>
                            @error('konten')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status Publikasi <span class="text-danger">*</span></label>
                            <select class="form-select @error('is_published') is-invalid @enderror" name="is_published"
                                required>
                                <option value="0" {{ old('is_published', '0') == '0' ? 'selected' : '' }}>
                                    Draft</option>
                                <option value="1" {{ old('is_published') == '1' ? 'selected' : '' }}>
                                    Publish</option>
                            </select>
                            @error('is_published')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('berita.index') }}" class="btn btn-secondary me-2">Batal</a>
                            <button type="submit" class="btn btn-primary" id="submit-button">
                                <span class="btn-text">Simpan</span>
                                <span class="spinner-border spinner-border-sm d-none" role="status"
                                    aria-hidden="true"></span>
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
    <!-- Custom JS files -->
    <script src="{{ asset('js/custom.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var form = document.querySelector('form');
            form.onsubmit = function() {
                // Menambahkan efek loading pada tombol submit
                var submitButton = document.getElementById('submit-button');
                var buttonText = submitButton.querySelector('.btn-text');
                var spinner = submitButton.querySelector('.spinner-border');

                submitButton.disabled = true;
                buttonText.textContent = 'Menyimpan...';
                spinner.classList.remove('d-none');
            };
        });
    </script>
@endpush
