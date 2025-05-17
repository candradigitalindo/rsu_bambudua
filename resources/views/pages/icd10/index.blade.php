@extends('layouts.app')
@section('title', 'ICD10 Codes')
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
    <div class="container">
        <h1>ICD10 Codes</h1>

        <form method="GET" action="{{ route('icd10.index') }}" class="mb-3">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Cari kode atau deskripsi ICD10..."
                    value="{{ request('search') }}">
                <button class="btn btn-primary" type="submit">Cari</button>
            </div>
        </form>

        {{-- Form Import Excel --}}
        <form action="{{ route('icd10.import') }}" method="POST" enctype="multipart/form-data" class="mb-3"
            id="form-import-icd10">
            @csrf
            <div class="input-group">
                <input type="file" name="file" class="form-control" accept=".xlsx,.xls" required>
                <button class="btn btn-success" type="submit" id="btn-import-icd10">
                    <span class="btn-text">Import Excel</span>
                    <span class="spinner-border spinner-border-sm d-none" id="spinner-import-icd10" role="status"
                        aria-hidden="true"></span>
                </button>
            </div>
            @if (session('success'))
                <div class="alert alert-success mt-2">{{ session('success') }}</div>
            @endif
            @if ($errors->has('file'))
                <div class="alert alert-danger mt-2">{{ $errors->first('file') }}</div>
            @endif
        </form>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Description</th>
                    <th>Version</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($icd10s as $code)
                    <tr>
                        <td>{{ $code->code }}</td>
                        <td>{{ $code->description }}</td>
                        <td>{{ $code->version }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $icd10s->appends(request()->query())->links() }}
    </div>
@endsection

@push('scripts')
    <!-- Overlay Scroll JS -->
    <script src="{{ asset('vendor/overlay-scroll/jquery.overlayScrollbars.min.js') }}"></script>
    <script src="{{ asset('vendor/overlay-scroll/custom-scrollbar.js') }}"></script>
    <!-- Custom JS files -->
    <script src="{{ asset('js/custom.js') }}"></script>
    <script>
        document.getElementById('form-import-icd10').addEventListener('submit', function() {
            document.getElementById('btn-import-icd10').setAttribute('disabled', true);
            document.querySelector('#btn-import-icd10 .btn-text').classList.add('d-none');
            document.getElementById('spinner-import-icd10').classList.remove('d-none');
        });
    </script>
@endpush
