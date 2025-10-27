@extends('layouts.app')
@section('title')
    Riwayat Pasien
@endsection
@push('style')
    <link rel="stylesheet" href="{{ asset('vendor/overlay-scroll/OverlayScrollbars.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/datatables/dataTables.bs5.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/datatables/dataTables.bs5-custom.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/datatables/buttons/dataTables.bs5-custom.css') }}">
@endpush
@section('content')
    <div class="row gx-3">
        <div class="col-12">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title">Pencarian Riwayat Pasien</h5>
                </div>
                <div class="card-body">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-6">
                            <label class="form-label">Cari (Nama, RM, RM Lama, No HP)</label>
                            <input type="text" class="form-control" id="q" placeholder="Ketik minimal 2 karakter...">
                        </div>
                        <div class="col-md-3">
                            <button type="button" id="btnCari" class="btn btn-primary w-100">
                                <i class="ri-search-line"></i> Cari
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-outer">
                        <div class="table-responsive">
                            <table class="table truncate m-0" id="tblRiwayat">
                                <thead>
                                    <tr>
                                        <th style="width: 80px;">Riwayat</th>
                                        <th>RM</th>
                                        <th>Nama</th>
                                        <th>JK</th>
                                        <th>Tgl Lahir</th>
                                        <th>HP</th>
                                        <th>Kunjungan Terakhir</th>
                                        <th>Tipe</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    <small class="text-muted d-block mt-2">Catatan: Riwayat menampilkan data ringkas pasien untuk keperluan verifikasi dan kesinambungan pelayanan.</small>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="{{ asset('vendor/overlay-scroll/jquery.overlayScrollbars.min.js') }}"></script>
    <script src="{{ asset('vendor/overlay-scroll/custom-scrollbar.js') }}"></script>
    <script src="{{ asset('vendor/datatables/dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/custom/custom-datatables.js') }}"></script>
    <script src="{{ asset('js/custom.js') }}"></script>
    <script>
        function safeHtml(s){ return $('<div>').text(s||'').html(); }
        function badge(text, cls){ return `<span class="badge ${cls||'bg-primary-subtle text-primary border'} me-1">${safeHtml(text)}</span>`; }
        function renderChild(encounters){
            if (!encounters || !encounters.length) {
                return '<div class="text-muted">Belum ada riwayat kunjungan.</div>';
            }
            let blocks = encounters.map(function(e){
                const tipe = ({1:'RJ',2:'RI',3:'IGD'})[e.type] || '-';
                const diag = (e.diagnoses||[]).slice(0,6).map(d=>badge(d)).join(' ');
                const doctors = (e.doctors||[]).map(n=>badge(n,'bg-info-subtle text-info border')).join(' ');
                const nurses = (e.nurses||[]).map(n=>badge(n,'bg-secondary-subtle text-secondary border')).join(' ');
                const metaRow = `
                    ${badge('Tujuan: '+(e.purpose||'-'),'bg-light text-dark border')}
                    ${badge('Jaminan: '+(e.insurance||'-'),'bg-light text-dark border')}
                    ${badge('Poli: '+(e.clinic||'-'),'bg-light text-dark border')}
                `;
                const ttv = e.ttv ? `
                    <div class="d-flex flex-wrap gap-2">
                        ${badge('Nadi: '+(e.ttv.nadi||'-'),'bg-light text-dark border')}
                        ${badge('TD: '+(e.ttv.sistolik||'-')+'/'+(e.ttv.diastolik||'-'),'bg-light text-dark border')}
                        ${badge('Suhu: '+(e.ttv.suhu||'-'),'bg-light text-dark border')}
                        ${badge('Kesadaran: '+(e.ttv.kesadaran||'-'),'bg-light text-dark border')}
                        ${badge('TB: '+(e.ttv.tinggi_badan||'-'),'bg-light text-dark border')}
                        ${badge('BB: '+(e.ttv.berat_badan||'-'),'bg-light text-dark border')}
                    </div>` : '<div class="text-muted">-</div>';
                const tindakanRows = ((e.tindakan && e.tindakan.items) ? e.tindakan.items : []).map(function(t){
                    return `<tr><td>${safeHtml(t.tindakan_name||'-')}</td><td class=\"text-end\">${t.qty||0}</td></tr>`;
                }).join('');
                const tindakanTable = `<div class=\"table-responsive\"><table class=\"table table-sm m-0\">
                    <thead><tr><th>Tindakan</th><th class=\"text-end\">Qty</th></tr></thead>
                    <tbody>${tindakanRows||'<tr><td colspan=\"2\" class=\"text-muted\">-</td></tr>'}</tbody>
                </table></div>`;
                const labList = ((e.lab && e.lab.items) ? e.lab.items : []).slice(0,10).map(function(li){
                    const st = li.status ? badge(li.status, li.status==='completed'?'bg-success':'bg-secondary') : '';
                    return `<li>${badge(li.test_name||'-','bg-success-subtle text-success border')} ${st}</li>`;
                }).join('');
                const resepList = ((e.resep && e.resep.items) ? e.resep.items : []).slice(0,10).map(function(r){
                    return `<li>${badge(r.nama_obat||'-','bg-danger-subtle text-danger border')} ${badge('Qty: '+(r.qty||0),'bg-light text-dark border')} ${badge(r.aturan_pakai||'-','bg-light text-dark border')}</li>`;
                }).join('');

                return `<div class="mb-3 p-2 border rounded">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <strong>${safeHtml(e.date||'-')}</strong> • <span class="text-muted">${tipe}</span>
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <small class="text-muted">Dokter</small>
                            <div>${doctors || '<span class="text-muted">-</span>'}</div>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted">Perawat</small>
                            <div>${nurses || '<span class="text-muted">-</span>'}</div>
                        </div>
                        <div class="col-md-12">
                            <small class="text-muted">Informasi Kunjungan</small>
                            <div class="d-flex flex-wrap gap-2">${metaRow}</div>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted">Diagnosis</small>
                            <div>${diag || '<span class="text-muted">-</span>'}</div>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted">Tanda Vital</small>
                            <div>${ttv}</div>
                        </div>
                        <div class="col-12">
                            <small class="text-muted">Pemeriksaan Penunjang</small>
                            <ul class="m-0 ps-3">${labList || '<li class="text-muted">-</li>'}</ul>
                        </div>
                        <div class="col-12">
                            <small class="text-muted">Tindakan / Prosedur</small>
                            ${tindakanTable}
                        </div>
                        <div class="col-12">
                            <small class="text-muted">Resep</small>
                            <ul class="m-0 ps-3">${resepList || '<li class="text-muted">-</li>'}</ul>
                        </div>
                    </div>
                </div>`;
            }).join('');
            return `<div>${blocks}</div>`;
        }

        const tbl = $('#tblRiwayat').DataTable({
            paging: true,
            info: true,
            searching: false,
            columns: [
                { data: null, orderable:false, searchable:false, className:'text-center',
                  render: function(){ return '<button class="btn btn-sm btn-outline-primary btn-riwayat">Lihat</button>'; }
                },
                { data: 'rekam_medis' },
                { data: 'name' },
                { data: 'jenis_kelamin', render: d => d==1? 'Pria' : (d==2? 'Wanita' : '-') },
                { data: 'tgl_lahir' },
                { data: 'no_hp' },
                { data: 'last_visit' },
                { data: 'last_visit_type', render: d => ({1:'RJ',2:'RI',3:'IGD'})[d] || '-' },
            ]
        });
        function load(q){
            $.get({ url: "{{ route('medical-records.riwayat.data') }}", data: { q: q }})
                .done(function(resp){ tbl.clear().rows.add(resp.data||[]).draw(); });
        }
        $('#tblRiwayat tbody').on('click', 'button.btn-riwayat', function(){
            const tr = $(this).closest('tr');
            const row = tbl.row(tr);
            if (row.child.isShown()){
                row.child.hide();
                $(this).text('Lihat');
            } else {
                const data = row.data();
                row.child(renderChild(data.encounters||[])).show();
                $(this).text('Tutup');
            }
        });

        $('#btnCari').on('click', function(){ const q = $('#q').val(); if (!q || q.length < 2) { alert('Minimal 2 karakter'); return; } load(q); });
        $('#q').on('keyup', function(e){ if (e.key === 'Enter') { $('#btnCari').click(); } });
    </script>
@endpush
