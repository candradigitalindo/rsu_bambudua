<div class="row gx-3">
    <div class="col-xxl-6 col-sm-6">
        <div class="card mb-1">
            <div class="card-header p-0 border-bottom-0">
                <ul class="nav nav-tabs" id="penunjangTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="lab-tab" data-bs-toggle="tab"
                            data-bs-target="#lab-request-pane" type="button" role="tab"
                            aria-controls="lab-request-pane" aria-selected="true">Laboratorium</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="radiologi-tab" data-bs-toggle="tab"
                            data-bs-target="#radiologi-request-pane" type="button" role="tab"
                            aria-controls="radiologi-request-pane" aria-selected="false">Radiologi</button>
                    </li>
                </ul>
            </div>

            <div class="card-body">
                <div class="tab-content" id="penunjangTabContent">
                    {{-- Tab Laboratorium --}}
                    <div class="tab-pane fade show active" id="lab-request-pane" role="tabpanel"
                        aria-labelledby="lab-tab" tabindex="0">
                        <div class="mb-3">
                            <label class="form-label" for="jenis_pemeriksaan_lab">Jenis Pemeriksaan Lab</label>
                            <select name="jenis_pemeriksaan_lab" id="jenis_pemeriksaan_lab" class="form-control">
                                <option value="">Pilih Jenis Pemeriksaan</option>
                                @foreach ($jenisPemeriksaan->where('type', 'lab') as $item)
                                    <option value="{{ $item->id }}" data-name="{{ $item->name }}">
                                        {{ $item->name }} - {{ number_format($item->harga, 0, ',', '.') }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Tab Radiologi --}}
                    <div class="tab-pane fade" id="radiologi-request-pane" role="tabpanel"
                        aria-labelledby="radiologi-tab" tabindex="0">
                        <div class="mb-3">
                            <label class="form-label" for="jenis_pemeriksaan_radiologi">Jenis Pemeriksaan
                                Radiologi</label>
                            <select name="jenis_pemeriksaan_radiologi" id="jenis_pemeriksaan_radiologi"
                                class="form-control">
                                <option value="">Pilih Jenis Pemeriksaan</option>
                                @foreach ($jenisPemeriksaan->where('type', 'radiologi') as $item)
                                    <option value="{{ $item->id }}" data-name="{{ $item->name }}">
                                        {{ $item->name }} - {{ number_format($item->harga, 0, ',', '.') }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary" id="btn-pemeriksaan">
                    <span class="btn-txt" id="text-pemeriksaan">Simpan Permintaan</span>
                    <span class="spinner-border spinner-border-sm d-none" id="spinner-pemeriksaan"></span>
                </button>
            </div>
        </div>
    </div>
    <div class="col-xxl-6 col-sm-12">
        <div class="card mb-3">

            <div class="card-header">
                <h5 class="card-title">Data Pemeriksaan Penunjang</h5>
                <hr class="mb-2">
            </div>
            <div class="card-body">
                <div class="table-outer">
                    <div class="table-responsive">
                        <table class="table truncate m-0">
                            <thead>
                                <tr>
                                    <th>Jenis Pemeriksaan</th>
                                    <th>Qty</th>
                                    <th class="text-end">Harga</th>
                                    <th>Total Harga</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tbody-pendukung"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- [LAB] Hasil Laboratorium (Permintaan Lab) -->
<div class="row gx-3 mt-3">
    <div class="col-xxl-12 col-sm-12">
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Hasil Laboratorium</h5>
            </div>
            <div class="card-body">
                <div id="lab-requests-container">
                    @forelse($labRequests ?? [] as $lr)
                        <div class="mb-3 border rounded p-2">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <strong>{{ $lr->created_at->format('d M Y H:i') }}</strong>
                                    <span class="badge bg-secondary ms-2">{{ ucfirst($lr->status) }}</span>
                                </div>
                                <div class="d-flex gap-2">
                                    @if (auth()->user()->role != 2)
                                        @if ($lr->status === 'completed')
                                            <a href="{{ route('lab.requests.print', $lr->id) }}" target="_blank"
                                                class="btn btn-sm btn-success">Cetak Hasil</a>
                                        @endif
                                    @endif
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th style="width:25%">Pemeriksaan</th>
                                            <th>Hasil</th>
                                            <th class="text-end" style="width:10%">Harga</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($lr->items as $it)
                                            <tr>
                                                <td>
                                                    <div class="fw-semibold">{{ $it->test_name }}</div>
                                                </td>
                                                <td>
                                                    @if (is_array($it->result_payload) && count($it->result_payload))
                                                        <dl class="row mb-0">
                                                            @foreach ($it->result_payload as $k => $v)
                                                                <dt class="col-sm-4 text-muted small">
                                                                    {{ str_replace('_', ' ', ucfirst($k)) }}</dt>
                                                                <dd class="col-sm-8">{{ $v }}</dd>
                                                            @endforeach
                                                        </dl>
                                                    @else
                                                        <div class="row g-2">
                                                            <div class="col-md-4">
                                                                <div><small class="text-muted">Nilai</small></div>
                                                                <div>{{ $it->result_value ?? '-' }}</div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div><small class="text-muted">Satuan</small></div>
                                                                <div>{{ $it->result_unit ?? '-' }}</div>
                                                            </div>
                                                            <div class="col-md-5">
                                                                <div><small class="text-muted">Rujukan</small></div>
                                                                <div>{{ $it->result_reference ?? '-' }}</div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    @if ($it->result_notes)
                                                        <div class="mt-1"><small class="text-muted">Catatan:</small>
                                                            {{ $it->result_notes }}</div>
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    {{ 'Rp ' . number_format($it->price, 0, ',', '.') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @empty
                        <div class="text-muted">Belum ada permintaan/hasil laboratorium untuk encounter ini.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- [RAD] Hasil Radiologi -->
<div class="row gx-3 mt-3">
    <div class="col-xxl-12 col-sm-12">
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Hasil Radiologi</h5>
            </div>
            <div class="card-body">
                <div id="radiology-requests-container"></div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        (function() {
            const IS_DOCTOR = @json(auth()->user()->role == 2);
            const ENCOUNTER_ID = @json($observasi);
            const listUrl = "{{ route('observasi.pemeriksaanPenunjang', ':id') }}".replace(':id', ENCOUNTER_ID);
            const postUrl = "{{ route('observasi.postPemeriksaanPenunjang', ':id') }}".replace(':id', ENCOUNTER_ID);
            const postRadiologyUrl = "{{ route('radiologi.requests.store') }}";

            function ensureFormat() {
                if (typeof window.formatRupiah !== 'function') {
                    window.formatRupiah = function(angka) {
                        if (typeof angka === 'string') {
                            angka = parseFloat(angka);
                        }
                        if (isNaN(angka)) {
                            return '0';
                        }

                        if (angka === null || angka === undefined) return '0';
                        let integer_part = Math.floor(parseFloat(angka)).toString();
                        let sisa = integer_part.length % 3;
                        let rupiah = integer_part.substr(0, sisa);
                        let ribuan = integer_part.substr(sisa).match(/\d{3}/gi);
                        if (ribuan) {
                            let separator = sisa ? '.' : '';
                            rupiah += separator + ribuan.join('.');
                        }
                        return rupiah;
                    }
                }
            }

            function loadPenunjangList() {
                ensureFormat();
                $.ajax({
                    url: listUrl,
                    type: 'GET'
                }).done(function(data) {
                    const tbody = $('#tbody-pendukung');
                    tbody.empty();
                    (data || []).forEach(function(item) {
                        const canDelete = (item.status === 'requested' || item.status === 'canceled');
                        const canPrint = item.status === 'completed';
                        const printUrl = item.type === 'lab' ?
                            `/laboratorium/requests/${item.request_id}/print?auto=1` :
                            `/radiologi/permintaan/${item.request_id}/print?auto=1`;
                        const printBtn = canPrint ?
                            `<a class="btn btn-sm btn-success" href="${printUrl}" target="_blank"><i class="ri-printer-line"></i> Cetak</a>` :
                            '';
                        const deleteBtn = canDelete ?
                            `<button class="btn btn-sm btn-danger btn-hapus-pemeriksaan" data-id="${item.id}" data-type="${item.type}"><i class="ri-delete-bin-6-line"></i> Hapus</button>` :
                            '';
                        const actionsHtml = IS_DOCTOR ? '' : `${deleteBtn} ${printBtn}`;
                        tbody.append(`
          <tr>
            <td>
              <div class="fw-semibold">${item.jenis_pemeriksaan} <span class="badge bg-${item.type === 'lab' ? 'info' : 'primary'}">${item.type}</span></div>
              <div class="small text-muted">${item.status ?? ''}</div>
            </td>
            <td class=\"text-center\">${item.qty}</td>
            <td class="text-end">${formatRupiah(item.harga)}</td>
            <td class=\"text-end\">${formatRupiah(item.total_harga)}</td>
            <td class=\"text-center\">${actionsHtml}</td>
          </tr>
        `);
                    });
                    $('#jenis_pemeriksaan').val(null);
                });
            }

            $(document).on('click', '#tab-pemeriksaan-penunjang', function() {
                loadPenunjangList();
                loadLabRequests();
                loadRadiologyRequests();
            });

            $(document).on('click', '#btn-pemeriksaan', function() {
                const activeTab = $('#penunjangTab .nav-link.active').attr('id');
                let url, data, selectId;

                if (activeTab === 'lab-tab') {
                    url = postUrl;
                    selectId = '#jenis_pemeriksaan_lab';
                    data = {
                        jenis_pemeriksaan_id: $(selectId).val(),
                        _token: '{{ csrf_token() }}'
                    };
                } else if (activeTab === 'radiologi-tab') {
                    url = postRadiologyUrl;
                    selectId = '#jenis_pemeriksaan_radiologi';
                    data = {
                        encounter_id: ENCOUNTER_ID,
                        jenis_pemeriksaan_id: $(selectId).val(),
                        _token: '{{ csrf_token() }}'
                    };
                } else {
                    alert('Tab tidak valid.');
                    return;
                }

                if (!data.jenis_pemeriksaan_id) {
                    alert('Pilih jenis pemeriksaan terlebih dahulu.');
                    return;
                }

                $('#spinner-pemeriksaan').removeClass('d-none');
                $('#text-pemeriksaan').addClass('d-none');

                $.ajax({
                        url: url,
                        type: 'POST',
                        data: data,
                    })
                    .done(function(resp) {
                        swal(resp.message || 'Permintaan berhasil dibuat.', {
                            icon: 'success'
                        });
                        loadPenunjangList();
                        loadLabRequests(); // Refresh lab results too
                        loadRadiologyRequests();
                        $(selectId).val('').trigger('change');
                    })
                    .fail(function() {
                        swal('Terjadi kesalahan saat menyimpan data.', {
                            icon: 'error'
                        });
                    })
                    .always(function() {
                        $('#spinner-pemeriksaan').addClass('d-none');
                        $('#text-pemeriksaan').removeClass('d-none');
                    });
            });

            // ==================== LAB REQUESTS ====================
            function loadLabRequests() {
                const url = "{{ route('observasi.labRequests', ':id') }}".replace(':id', ENCOUNTER_ID);
                $.get(url).done(function(rows) {
                    const $container = $('#lab-requests-container');
                    if (!rows || rows.length === 0) {
                        $container.html(
                            '<div class="text-muted">Belum ada permintaan/hasil laboratorium untuk encounter ini.</div>'
                        );
                        return;
                    }

                    let html = '';
                    rows.forEach(function(lr) {
                        html += buildLabRequestCard(lr);
                    });
                    $container.html(html);
                });
            }

            function buildLabRequestCard(lr) {
                const badgeClass = (lr.status === 'completed') ? 'bg-primary' : 'bg-secondary';
                const statusBadge =
                    `<span class="badge ${badgeClass} ms-2">${(lr.status||'').charAt(0).toUpperCase()+ (lr.status||'').slice(1)}</span>`;

                const itemsHtml = (lr.items || []).map(function(it) {
                    return buildLabResultRow(it);
                }).join('');

                return `<div class="mb-3 border rounded p-2"><div class="d-flex justify-content-between align-items-center mb-2"><div><strong>${lr.created_at || ''}</strong>${statusBadge}</div></div><div class="table-responsive"><table class="table table-sm align-middle mb-0"><thead><tr><th style="width:25%">Pemeriksaan</th><th>Hasil</th><th class="text-end" style="width:10%">Harga</th></tr></thead><tbody>${itemsHtml}</tbody></table></div></div>`;
            }

            function buildLabResultRow(item) {
                const hasPayload = (item.result_payload && typeof item.result_payload === 'object' && Object.keys(item
                    .result_payload).length > 0);
                let resultHtml = '';

                if (hasPayload) {
                    const payloadItems = Object.keys(item.result_payload).map(function(k) {
                        const label = k.replace(/_/g, ' ').charAt(0).toUpperCase() + k.replace(/_/g, ' ').slice(
                            1);
                        const value = item.result_payload[k] || '';
                        return `<dt class="col-sm-4 text-muted small">${label}</dt><dd class="col-sm-8">${value}</dd>`;
                    }).join('');
                    resultHtml = `<dl class="row mb-0">${payloadItems}</dl>`;
                } else {
                    resultHtml =
                        `<div class="row g-2"><div class="col-md-4"><div><small class="text-muted">Nilai</small></div><div>${item.result_value ?? '-'}</div></div><div class="col-md-3"><div><small class="text-muted">Satuan</small></div><div>${item.result_unit ?? '-'}</div></div><div class="col-md-5"><div><small class="text-muted">Rujukan</small></div><div>${item.result_reference ?? '-'}</div></div></div>`;
                }

                const notesHtml = item.result_notes ?
                    `<div class="mt-1"><small class="text-muted">Catatan:</small> ${item.result_notes}</div>` : '';

                return `<tr><td><div class="fw-semibold">${item.test_name || ''}</div></td><td>${resultHtml}${notesHtml}</td><td class="text-end">${formatRupiah(item.price)}</td></tr>`;
            }

            // ==================== RADIOLOGY REQUESTS ====================
            function loadRadiologyRequests() {
                const url = "{{ route('observasi.radiologyRequests', ':id') }}".replace(':id', ENCOUNTER_ID);
                $.get(url).done(function(rows) {
                    const $container = $('#radiology-requests-container');
                    if (!rows || rows.length === 0) {
                        $container.html(
                            '<div class="text-muted">Belum ada permintaan/hasil radiologi untuk encounter ini.</div>'
                        );
                        return;
                    }

                    let html = '';
                    rows.forEach(function(r) {
                        html += buildRadiologyRequestCard(r);
                    });
                    $container.html(html);
                });
            }

            function buildRadiologyRequestCard(r) {
                const badgeClass = (r.status === 'completed') ? 'bg-primary' : 'bg-secondary';
                const statusBadge =
                    `<span class="badge ${badgeClass} ms-2">${(r.status||'').charAt(0).toUpperCase()+ (r.status||'').slice(1)}</span>`;

                const printBtn = (r.status === 'completed') ?
                    `<a href="/radiologi/permintaan/${r.id}/print?auto=1" target="_blank" class="btn btn-sm btn-success"><i class="ri-printer-line"></i> Cetak</a>` :
                    '';

                let contentHtml = '<div class="text-muted small">Belum ada hasil.</div>';

                if (r.latest) {
                    const isEcho = r.jenis_name && (r.jenis_name.toUpperCase().includes('ECHO') || r.jenis_name
                        .toUpperCase().includes('ECHOCARDIOGRAPHY'));

                    const radiologistInfo = r.latest.radiologist_name ?
                        `<div class="row mb-2"><div class="col-md-6"><small class="text-muted">Radiolog:</small> <strong>${r.latest.radiologist_name}</strong></div>` +
                        (r.latest.reporter_name ?
                            `<div class="col-md-6"><small class="text-muted">Perawat:</small> <strong>${r.latest.reporter_name}</strong></div></div>` :
                            '</div>') :
                        '';

                    const payloadHtml = buildRadiologyPayload(r.latest.payload, isEcho);

                    const findingsHtml = (r.latest.findings && r.latest.findings !== '-') ?
                        `<div class="mt-2"><strong class="text-muted">Temuan / Findings:</strong><div class="border-start border-3 border-primary ps-2 mt-1">${r.latest.findings}</div></div>` :
                        '';
                    const impressionHtml = r.latest.impression ?
                        `<div class="mt-2"><strong class="text-muted">Saran / Recommendation:</strong><div class="border-start border-3 border-success ps-2 mt-1">${r.latest.impression}</div></div>` :
                        '';

                    contentHtml =
                        `${radiologistInfo}${payloadHtml}${findingsHtml}${impressionHtml}`;
                }

                return `<div class="mb-3 border rounded p-3 shadow-sm"><div class="d-flex justify-content-between align-items-center mb-2"><div><strong>${r.created_at || ''}</strong>${statusBadge}</div><div>${printBtn}</div></div><div><div class="fw-semibold mb-2 text-primary">${r.jenis_name || ''}</div>${contentHtml}</div></div>`;
            }

            function buildRadiologyPayload(payload, isEcho = false) {
                if (!payload || typeof payload !== 'object' || Object.keys(payload).length === 0) {
                    return '';
                }

                if (isEcho) {
                    return buildEchoPayload(payload);
                }

                let fieldsHtml = '';
                for (let [key, value] of Object.entries(payload)) {
                    if (value) {
                        const label = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                        fieldsHtml +=
                            `<div class="col-md-6"><small class="text-muted">${label}</small><div class="fw-semibold small">${value}</div></div>`;
                    }
                }

                return fieldsHtml ?
                    `<div class="card bg-light mt-2 mb-2"><div class="card-body p-2"><div class="row g-2">${fieldsHtml}</div></div></div>` :
                    '';
            }

            function buildEchoPayload(payload) {
                const normalRanges = {
                    'Root diam': '20-37 mm',
                    'LA Dimension': '15-40 mm',
                    'LA/Ao ratio': '< 1.33',
                    'RV Dimension': '< 43 mm',
                    'M.V.A': '> 3 cm²',
                    'TAPSE': '≥ 16 mm',
                    'EDD': '35-52 mm',
                    'ESD': '26-36 mm',
                    'IVS Diastole': '7-11 mm',
                    'PW Diastole': '7-11 mm',
                    'EF': '52-77 %',
                    'FS': '> 25 %',
                    'EPSS': '< 10 mm'
                };

                const measurements = [{
                        section: 'Aorta',
                        rowspan: 1,
                        fields: ['Root diam']
                    },
                    {
                        section: 'Atrium Kiri<br>(Left Atrium)',
                        rowspan: 2,
                        fields: ['LA Dimension', 'LA/Ao ratio']
                    },
                    {
                        section: 'Ventrikel Kanan<br>(Right Ventricle)',
                        rowspan: 5,
                        fields: ['RV Dimension', 'M.V.A', 'TAPSE', 'RA mayor', 'RA minor']
                    },
                    {
                        section: 'Ventrikel Kiri<br>(Left Ventricle)',
                        rowspan: 9,
                        fields: ['EDD', 'ESD', 'IVS Diastole', 'IVS Systole', 'PW Diastole', 'PW Systole', 'EF',
                            'FS', 'EPSS'
                        ]
                    }
                ];

                let html = '<div class="mt-3 mb-3">';
                html +=
                    '<div class="bg-white border border-dark text-center py-2 fw-bold">Pengukuran / Measurement</div>';
                html += '<div class="table-responsive">';
                html += '<table class="table table-sm table-bordered border-dark mb-0" style="font-size: 0.875rem;">';
                html += '<thead style="background-color: #D9E2F3;"><tr>';
                html +=
                    '<th colspan="2" class="text-center border-dark">Parameter</th><th class="text-center border-dark">Hasil</th>';
                html +=
                    '<th class="text-center border-dark text-success" style="background-color: #FFF2CC;">Normal Range</th>';
                html +=
                    '<th colspan="2" class="text-center border-dark">Parameter</th><th class="text-center border-dark">Hasil</th>';
                html +=
                    '<th class="text-center border-dark text-success" style="background-color: #FFF2CC;">Normal Range</th>';
                html += '</tr></thead><tbody>';

                // Row 1: Aorta (Root diam) | Ventrikel Kiri (EDD)
                html += '<tr>';
                html += '<td class="fw-bold bg-light border-dark align-middle">Aorta</td>';
                html += '<td class="fw-semibold bg-light border-dark">Root diam</td>';
                html += `<td class="text-center border-dark">${payload['Root diam'] || '-'}</td>`;
                html +=
                    '<td class="text-center border-dark text-success" style="background-color: #FFF2CC;">20-37 mm</td>';
                html +=
                    '<td rowspan="9" class="fw-bold bg-light border-dark align-middle">Ventrikel Kiri<br>(Left Ventricle)</td>';
                html += '<td class="fw-semibold bg-light border-dark">EDD</td>';
                html += `<td class="text-center border-dark">${payload['EDD'] || '-'}</td>`;
                html +=
                    '<td class="text-center border-dark text-success" style="background-color: #FFF2CC;">35-52 mm</td>';
                html += '</tr>';

                // Row 2: Atrium Kiri (LA Dimension) | Ventrikel Kiri (ESD)
                html += '<tr>';
                html +=
                    '<td rowspan="2" class="fw-bold bg-light border-dark align-middle">Atrium Kiri<br>(Left Atrium)</td>';
                html += '<td class="fw-semibold bg-light border-dark">LA Dimension</td>';
                html += `<td class="text-center border-dark">${payload['LA Dimension'] || '-'}</td>`;
                html +=
                    '<td class="text-center border-dark text-success" style="background-color: #FFF2CC;">15-40 mm</td>';
                html += '<td class="fw-semibold bg-light border-dark">ESD</td>';
                html += `<td class="text-center border-dark">${payload['ESD'] || '-'}</td>`;
                html +=
                    '<td class="text-center border-dark text-success" style="background-color: #FFF2CC;">26-36 mm</td>';
                html += '</tr>';

                // Row 3: Atrium Kiri (LA/Ao ratio) | Ventrikel Kiri (IVS Diastole)
                html += '<tr>';
                html += '<td class="fw-semibold bg-light border-dark">LA/Ao ratio</td>';
                html += `<td class="text-center border-dark">${payload['LA/Ao ratio'] || '-'}</td>`;
                html +=
                    '<td class="text-center border-dark text-success" style="background-color: #FFF2CC;">< 1.33</td>';
                html += '<td class="fw-semibold bg-light border-dark">IVS Diastole</td>';
                html += `<td class="text-center border-dark">${payload['IVS Diastole'] || '-'}</td>`;
                html +=
                    '<td class="text-center border-dark text-success" style="background-color: #FFF2CC;">7-11 mm</td>';
                html += '</tr>';

                // Row 4: Ventrikel Kanan (RV Dimension) | Ventrikel Kiri (IVS Systole)
                html += '<tr>';
                html +=
                    '<td rowspan="5" class="fw-bold bg-light border-dark align-middle">Ventrikel Kanan<br>(Right Ventricle)</td>';
                html += '<td class="fw-semibold bg-light border-dark">RV Dimension</td>';
                html += `<td class="text-center border-dark">${payload['RV Dimension'] || '-'}</td>`;
                html +=
                    '<td class="text-center border-dark text-success" style="background-color: #FFF2CC;">< 43 mm</td>';
                html += '<td class="fw-semibold bg-light border-dark">IVS Systole</td>';
                html += `<td class="text-center border-dark">${payload['IVS Systole'] || '-'}</td>`;
                html += '<td class="text-center border-dark text-success" style="background-color: #FFF2CC;"></td>';
                html += '</tr>';

                // Row 5: Ventrikel Kanan (M.V.A) | Ventrikel Kiri (PW Diastole)
                html += '<tr>';
                html += '<td class="fw-semibold bg-light border-dark">M.V.A</td>';
                html += `<td class="text-center border-dark">${payload['M.V.A'] || '-'}</td>`;
                html +=
                    '<td class="text-center border-dark text-success" style="background-color: #FFF2CC;">> 3 cm²</td>';
                html += '<td class="fw-semibold bg-light border-dark">PW Diastole</td>';
                html += `<td class="text-center border-dark">${payload['PW Diastole'] || '-'}</td>`;
                html +=
                    '<td class="text-center border-dark text-success" style="background-color: #FFF2CC;">7-11 mm</td>';
                html += '</tr>';

                // Row 6: Ventrikel Kanan (TAPSE) | Ventrikel Kiri (PW Systole)
                html += '<tr>';
                html += '<td class="fw-semibold bg-light border-dark">TAPSE</td>';
                html += `<td class="text-center border-dark">${payload['TAPSE'] || '-'}</td>`;
                html +=
                    '<td class="text-center border-dark text-success" style="background-color: #FFF2CC;">≥ 16 mm</td>';
                html += '<td class="fw-semibold bg-light border-dark">PW Systole</td>';
                html += `<td class="text-center border-dark">${payload['PW Systole'] || '-'}</td>`;
                html += '<td class="text-center border-dark text-success" style="background-color: #FFF2CC;"></td>';
                html += '</tr>';

                // Row 7: Ventrikel Kanan (RA mayor) | Ventrikel Kiri (EF)
                html += '<tr>';
                html += '<td class="fw-semibold bg-light border-dark">RA mayor</td>';
                html += `<td class="text-center border-dark">${payload['RA mayor'] || '-'}</td>`;
                html += '<td class="text-center border-dark text-success" style="background-color: #FFF2CC;"></td>';
                html += '<td class="fw-semibold bg-light border-dark">EF</td>';
                html += `<td class="text-center border-dark">${payload['EF'] || '-'}</td>`;
                html +=
                    '<td class="text-center border-dark text-success" style="background-color: #FFF2CC;">52-77 %</td>';
                html += '</tr>';

                // Row 8: Ventrikel Kanan (RA minor) | Ventrikel Kiri (FS)
                html += '<tr>';
                html += '<td class="fw-semibold bg-light border-dark">RA minor</td>';
                html += `<td class="text-center border-dark">${payload['RA minor'] || '-'}</td>`;
                html += '<td class="text-center border-dark text-success" style="background-color: #FFF2CC;"></td>';
                html += '<td class="fw-semibold bg-light border-dark">FS</td>';
                html += `<td class="text-center border-dark">${payload['FS'] || '-'}</td>`;
                html +=
                    '<td class="text-center border-dark text-success" style="background-color: #FFF2CC;">> 25 %</td>';
                html += '</tr>';

                // Row 9: Empty left | Ventrikel Kiri (EPSS)
                html += '<tr>';
                html += '<td colspan="4" class="border-dark"></td>';
                html += '<td class="fw-semibold bg-light border-dark">EPSS</td>';
                html += `<td class="text-center border-dark">${payload['EPSS'] || '-'}</td>`;
                html +=
                    '<td class="text-center border-dark text-success" style="background-color: #FFF2CC;">< 10 mm</td>';
                html += '</tr>';
                html += '</tbody></table></div>';

                const findingsTable1 = [{
                        label: 'Gerakan Otot / Wall Motion',
                        field: 'Gerakan Otot / Wall Motion',
                        pair: {
                            label: 'Katup Aorta / Aortic Valve',
                            field: 'Katup Aorta / Aortic Valve'
                        }
                    },
                    {
                        label: 'Katup Mitral / Mitral Valve',
                        field: 'Katup Mitral / Mitral Valve',
                        pair: {
                            label: 'Katup Pulmonal / Pulmonal Valve',
                            field: 'Katup Pulmonal / Pulmonal Valve'
                        }
                    },
                    {
                        label: 'Katup Trikuspid / Tricuspid Valve',
                        field: 'Katup Trikuspid / Tricuspid Valve',
                        colspan: true
                    }
                ];

                html +=
                    '<div class="border border-dark mt-3 mb-2"><table class="table table-sm table-bordered border-dark mb-0" style="font-size: 0.875rem;"><tbody>';
                findingsTable1.forEach(item => {
                    html += '<tr>';
                    html +=
                        `<td class="fw-semibold bg-light border-dark" style="width: 28%;">${item.label}</td><td class="border-dark" style="width: 2%;">:</td>`;
                    if (item.colspan) {
                        html += `<td colspan="4" class="border-dark">${payload[item.field] || '-'}</td>`;
                    } else {
                        html +=
                            `<td class="border-dark" style="width: 20%;">${payload[item.field] || '-'}</td>`;
                        html +=
                            `<td class="fw-semibold bg-light border-dark" style="width: 28%;">${item.pair.label}</td><td class="border-dark" style="width: 2%;">:</td>`;
                        html +=
                            `<td class="border-dark" style="width: 20%;">${payload[item.pair.field] || '-'}</td>`;
                    }
                    html += '</tr>';
                });
                html += '</tbody></table></div>';

                const findingsTable2 = [{
                        label: 'Wall motion',
                        field: 'Gerakan Otot / Wall Motion',
                        pair: {
                            label: 'Katup-Katup',
                            merged: true
                        }
                    },
                    {
                        label: 'Fungsi sistolik LV',
                        field: 'Fungsi Sistolik LV',
                        pair: {
                            label: 'Dimensi ruang jantung',
                            field: 'Dimensi Ruang Jantung'
                        }
                    }
                ];

                html +=
                    '<div class="border border-dark mb-2"><table class="table table-sm table-bordered border-dark mb-0" style="font-size: 0.875rem;"><tbody>';
                findingsTable2.forEach(item => {
                    html += '<tr>';
                    html +=
                        `<td class="fw-semibold bg-light border-dark" style="width: 28%;">${item.label}</td><td class="border-dark" style="width: 2%;">:</td>`;
                    if (item.pair.merged) {
                        const katupFields = ['Katup Mitral / Mitral Valve', 'Katup Trikuspid / Tricuspid Valve',
                            'Katup Aorta / Aortic Valve', 'Katup Pulmonal / Pulmonal Valve'
                        ];
                        const katupValues = katupFields.map(f => payload[f]).filter(v => v);
                        const mergedValue = katupValues.length > 0 ? katupValues.join(', ') : '-';
                        html +=
                            `<td class="border-dark" style="width: 20%;">${payload[item.field] || '-'}</td>`;
                        html +=
                            `<td class="fw-semibold bg-light border-dark" style="width: 28%;">${item.pair.label}</td><td class="border-dark" style="width: 2%;">:</td>`;
                        html += `<td class="border-dark" style="width: 20%;">${mergedValue}</td>`;
                    } else {
                        let funcValue = payload[item.field] || '';
                        const efValue = payload['EF'];
                        if (efValue && funcValue && !funcValue.toLowerCase().includes('ef')) {
                            funcValue += ` (EF: ${efValue} %)`;
                        }
                        html += `<td class="border-dark" style="width: 20%;">${funcValue || '-'}</td>`;
                        html +=
                            `<td class="fw-semibold bg-light border-dark" style="width: 28%;">${item.pair.label}</td><td class="border-dark" style="width: 2%;">:</td>`;
                        html +=
                            `<td class="border-dark" style="width: 20%;">${payload[item.pair.field] || '-'}</td>`;
                    }
                    html += '</tr>';
                });
                html += '</tbody></table></div></div>';
                return html;
            }

            // ==================== DELETE HANDLER ====================
            $(document).off('click', '.btn-hapus-pemeriksaan');
            $(document).on('click', '.btn-hapus-pemeriksaan', function() {
                const id = $(this).data('id');
                const type = $(this).data('type');

                const deleteUrl = (type === 'radiologi') ?
                    "{{ route('observasi.radiologi.destroy', ':id') }}".replace(':id', id) :
                    "{{ route('observasi.deletePemeriksaanPenunjang', ':id') }}".replace(':id', id);

                swal({
                    title: "Apakah Anda yakin?",
                    text: "Data ini akan dihapus!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        $.ajax({
                                url: deleteUrl,
                                type: 'DELETE',
                                data: {
                                    _token: '{{ csrf_token() }}'
                                },
                                dataType: 'json',
                                headers: {
                                    'Accept': 'application/json'
                                }
                            })
                            .done(function(resp) {
                                if (resp && (resp.success === true || resp.status === true)) {
                                    swal(resp.message || 'Data telah dihapus', {
                                        icon: 'success'
                                    });
                                    loadPenunjangList();
                                    loadLabRequests();
                                    loadRadiologyRequests();
                                } else {
                                    swal(resp.message || 'Gagal menghapus data.', {
                                        icon: 'error'
                                    });
                                }
                            })
                            .fail(function(xhr) {
                                const msg = (xhr && xhr.responseJSON && xhr.responseJSON.message) ?
                                    xhr.responseJSON.message :
                                    'Terjadi kesalahan saat menghapus data.';
                                swal(msg, {
                                    icon: 'error'
                                });
                            });
                    }
                });
            });

            // ==================== INITIALIZATION ====================
            loadPenunjangList();
            loadLabRequests();
            loadRadiologyRequests();

        })();
    </script>
@endpush
