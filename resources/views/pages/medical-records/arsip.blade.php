@extends('layouts.app')
@section('title')
    Arsip Dokumen RM
@endsection
@push('style')
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/dropzone/dropzone.min.css') }}">
    <style>
        .gallery a { display:block; border:1px solid #cdd6dc; border-radius:8px; overflow:hidden; padding:3px; }
        .gallery .file-card { border:1px solid #cdd6dc; border-radius:8px; padding:12px; display:flex; align-items:center; justify-content:space-between; }
        .file-name { word-break: break-all; }
    </style>
@endpush
@section('content')
    <div class="row gx-3">
        <div class="col-lg-4 col-12">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title">Upload Dokumen</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('medical-records.arsip.upload') }}" class="dropzone" id="dropzoneArsip">
                        @csrf
                        <div class="dz-message">Tarik & lepas file disini atau klik untuk memilih</div>
                    </form>
                    <small class="text-muted d-block mt-2">Pastikan dokumen sesuai standar RS dan tidak memuat informasi yang tidak relevan.</small>
                </div>
            </div>
        </div>
        <div class="col-lg-8 col-12">
            <div class="card mb-3">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title m-0">Daftar Arsip</h5>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="btnReload"><i class="ri-refresh-line"></i> Muat Ulang</button>
                </div>
                <div class="card-body">
                    <div id="listArsip" class="d-grid gap-2"></div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="{{ asset('vendor/overlay-scroll/jquery.overlayScrollbars.min.js') }}"></script>
    <script src="{{ asset('vendor/overlay-scroll/custom-scrollbar.js') }}"></script>
    <script src="{{ asset('vendor/dropzone/dropzone.min.js') }}"></script>
    <script src="{{ asset('js/custom.js') }}"></script>
    <script>
        Dropzone.autoDiscover = false;
        const dz = new Dropzone('#dropzoneArsip', {
            paramName: 'file', maxFilesize: 10, timeout: 0, headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        });
        dz.on('success', function(){ loadArsip(); });

        function fmtBytes(bytes){ if(!bytes && bytes!==0) return '-'; const sizes=['B','KB','MB','GB']; const i=parseInt(Math.floor(Math.log(bytes)/Math.log(1024))); return Math.round(bytes/Math.pow(1024,i),2)+' '+sizes[i]; }
        function row(item){
            return `<div class="file-card">
                <div>
                    <div class="file-name"><i class="ri-file-text-line"></i> ${item.name}</div>
                    <small class="text-muted">${item.last_modified || ''} • ${fmtBytes(item.size)}</small>
                </div>
                <div class="d-flex gap-2">
                    <a class="btn btn-sm btn-success" href="${item.url}" target="_blank"><i class="ri-download-2-line"></i></a>
                    <button class="btn btn-sm btn-danger btn-del" data-name="${item.name}"><i class="ri-delete-bin-6-line"></i></button>
                </div>
            </div>`;
        }
        function loadArsip(){
            $.get({ url: "{{ route('medical-records.arsip.list') }}"})
                .done(function(resp){ const c = $('#listArsip'); c.empty(); (resp.data||[]).forEach(f=> c.append(row(f))); });
        }
        $(document).on('click', '#btnReload', loadArsip);
        $(document).on('click', '.btn-del', function(){
            const name = $(this).data('name');
            $.ajax({ url: "{{ route('medical-records.arsip.delete', ':name') }}".replace(':name', name), type: 'DELETE', data: { _token: "{{ csrf_token() }}" } })
                .done(function(){ loadArsip(); })
                .fail(function(){ alert('Gagal menghapus'); });
        });
        $(function(){ loadArsip(); });
    </script>
@endpush
