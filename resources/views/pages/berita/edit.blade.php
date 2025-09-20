@extends('layouts.app')
@section('title', 'Edit Berita')
@push('style')
    <link rel="stylesheet" href="{{ asset('vendor/quill/quill.snow.css') }}">
    <!-- Scrollbar CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Form Edit Berita</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('berita.update', $berita->id) }}" method="POST" enctype="multipart/form-data"
                        id="news-form">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="judul" class="form-label">Judul Berita <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('judul') is-invalid @enderror"
                                        id="judul" name="judul" value="{{ old('judul', $berita->judul) }}" required>
                                    @error('judul')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="konten" class="form-label">Konten <span
                                            class="text-danger">*</span></label>
                                    <input type="hidden" name="konten" value="{{ old('konten', $berita->konten) }}">
                                    <div id="editor" style="height: 300px;">{!! old('konten', $berita->konten) !!}</div>
                                    @error('konten')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Status Publikasi <span class="text-danger">*</span></label>
                                    <select class="form-select @error('is_published') is-invalid @enderror"
                                        name="is_published" required>
                                        <option value="1"
                                            {{ old('is_published', $berita->is_published) == 1 ? 'selected' : '' }}>
                                            Publish</option>
                                        <option value="0"
                                            {{ old('is_published', $berita->is_published) == 0 ? 'selected' : '' }}>
                                            Draft</option>
                                    </select>
                                    @error('is_published')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('berita.index') }}" class="btn btn-secondary me-2">Batal</a>
                            <button type="submit" class="btn btn-primary" id="submit-button">
                                <span class="btn-text">Update</span>
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
    <script src="{{ asset('vendor/quill/quill.min.js') }}"></script>
    <!-- Overlay Scroll JS -->
    <script src="{{ asset('vendor/overlay-scroll/jquery.overlayScrollbars.min.js') }}"></script>
    <script src="{{ asset('vendor/overlay-scroll/custom-scrollbar.js') }}"></script>
    <!-- Custom JS files -->
    <script src="{{ asset('js/custom.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var quill = new Quill('#editor', {
                theme: 'snow',
                modules: {
                    toolbar: [
                        [{
                            'header': [1, 2, 3, 4, 5, 6, false]
                        }],
                        ['bold', 'italic', 'underline'],
                        [{
                            'list': 'ordered'
                        }, {
                            'list': 'bullet'
                        }],
                        ['link'],
                        ['clean']
                    ]
                }
            });

            var form = document.getElementById('news-form');
            form.onsubmit = function() {
                // Salin konten dari editor Quill ke input hidden sebelum form disubmit
                var konten = document.querySelector('input[name=konten]');
                konten.value = quill.root.innerHTML;

                // Menambahkan efek loading pada tombol submit
                var submitButton = document.getElementById('submit-button');
                var buttonText = submitButton.querySelector('.btn-text');
                var spinner = submitButton.querySelector('.spinner-border');

                submitButton.disabled = true;
                buttonText.textContent = 'Mengupdate...';
                spinner.classList.remove('d-none');
            };

            // Pratinjau gambar sebelum diunggah
            const gambarInput = document.getElementById('gambar');
            const gambarPreview = document.getElementById('gambar-preview');

            gambarInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        gambarPreview.src = e.target.result;
                        gambarPreview.style.display = 'block';
                    };
                    reader.readAsDataURL(this.files[0]);
                }
            });
        });
    </script>
@endpush

