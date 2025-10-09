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
                // AJAX: ambil daftar Hasil Laboratorium dan render ulang container
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
                                        html += `
                        <div class="mb-3 border rounded p-2">
                          <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                              <strong>${lr.created_at || ''}</strong>
                              <span class="badge bg-secondary ms-2">${(lr.status||'').charAt(0).toUpperCase()+ (lr.status||'').slice(1)}</span>
                            </div>
                            <div class="d-flex gap-2">
                              ${(lr.status === 'completed') ? `<a href="/laboratorium/requests/${lr.id}/print?auto=1" target="_blank" class="btn btn-sm btn-success">Cetak Hasil</a>` : ''}
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
                                ${(lr.items||[]).map(function(it){
                                    const hasilPayload = (it.result_payload && typeof it.result_payload === 'object' && Object.keys(it.result_payload).length>0);
                                    let hasilHtml = '';
                                    if (hasilPayload){
                                        hasilHtml = '<dl class="row mb-0">' + Object.keys(it.result_payload).map(function(k){
                                            const label = k.replace(/_/g,' ');
                                            const v = it.result_payload[k];
                                            return `<dt class="col-sm-4 text-muted small">${label.charAt(0).toUpperCase()+label.slice(1)}</dt><dd class="col-sm-8">${v??''}</dd>`;
                                        }).join('') + '</dl>';
                                    } else {
                                        hasilHtml = ` <
                                            div class = "row g-2" >
                                            <
                                            div class = "col-md-4" > < div > < small class = "text-muted" > Nilai <
                                            /small></div > < div > $ {
                                                it.result_value ?? '-'
                                            } < /div></div >
                                            <
                                            div class = "col-md-3" > < div > < small class = "text-muted" > Satuan <
                                            /small></div > < div > $ {
                                                it.result_unit ?? '-'
                                            } < /div></div >
                                            <
                                            div class = "col-md-5" > < div > < small class = "text-muted" > Rujukan <
                                            /small></div > < div > $ {
                                                it.result_reference ?? '-'
                                            } < /div></div >
                                            <
                                            /div>`;
                                }
                                return `
                                              <tr>
                                                <td><div class="fw-semibold">${it.test_name || ''}</div></td>
                                                <td>${hasilHtml}${it.result_notes ? `<div class="mt-1"><small class="text-muted">Catatan:</small> ${it.result_notes}</div>` : ''}</td>
                                                <td class="text-end">${formatRupiah(it.price)}</td>
                                              </tr>`;
                            }).join('')
                    } <
                    /tbody> < /
                    table > <
                    /div> < /
                    div > `;
                    }); $container.html(html);
            });
            }

            $(document).on('click', '.btn-hapus-pemeriksaan', function() {
            const id = $(this).data('id');
            const type = $(this).data('type');
            const deleteUrl = (type === 'lab') ?
                "{{ route('observasi.deletePemeriksaanPenunjang', ':id') }}".replace(':id', id) :
                ` / radiologi / permintaan / $ {
                        id
                    }
                `; // Assuming DELETE method for radiology
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
                        }
                    }).done(function(resp) {
                        if (resp && (resp.success || resp.status)) {
                            swal(resp.message || 'Data telah dihapus', {
                                icon: 'success'
                            });
                            // Re-render tabel penunjang dan hasil lab secara realtime
                            loadPenunjangList();
                            loadLabRequests();
                        } else {
                            swal(resp.message || 'Gagal menghapus data.', {
                                icon: 'error'
                            });
                        }
                    }).fail(function() {
                        swal('Terjadi kesalahan saat menghapus data.', {
                            icon: 'error'
                        });
                    });
                }
            });
            });

            // Override with clean renderer and fixed delete handler
            function loadLabRequests() {
                const url = "{{ route('observasi.labRequests', ':id') }}".replace(':id', ENCOUNTER_ID);
                $.get(url).done(function(rows) {
                    const $container = $('#lab-requests-container');
                    if (!rows || rows.length === 0) {
                        $container.html('<div class="text-muted">Belum ada permintaan/hasil laboratorium untuk encounter ini.</div>');
                        return;
                    }
                    let html = '';
                    rows.forEach(function(lr) {
                        html += `
                            <div class="mb-3 border rounded p-2">
                              <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                  <strong>${lr.created_at || ''}</strong>
                                  <span class="badge bg-secondary ms-2">${(lr.status||'').charAt(0).toUpperCase()+ (lr.status||'').slice(1)}</span>
                                </div>
                                <div class="d-flex gap-2">
                                  ${(lr.status === 'completed') ? `<a href="/laboratorium/requests/${lr.id}/print" target="_blank" class="btn btn-sm btn-success">Cetak Hasil</a>` : ''}
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
                                    ${(lr.items||[]).map(function(it){
                                        const hasilPayload = (it.result_payload && typeof it.result_payload === 'object' && Object.keys(it.result_payload).length>0);
                                        let hasilHtml = '';
                                        if (hasilPayload){
                                            hasilHtml = '<dl class="row mb-0">' + Object.keys(it.result_payload).map(function(k){
                                                const label = k.replace(/_/g,' ');
                                                const v = it.result_payload[k];
                                                return `<dt class="col-sm-4 text-muted small">${label.charAt(0).toUpperCase()+label.slice(1)}</dt><dd class="col-sm-8">${v??''}</dd>`;
                                            }).join('') + '</dl>';
                                        } else {
                                            hasilHtml = `
                                                <div class="row g-2">
                                                    <div class="col-md-4">
                                                        <div><small class="text-muted">Nilai</small></div>
                                                        <div>${it.result_value ?? '-'}</div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div><small class="text-muted">Satuan</small></div>
                                                        <div>${it.result_unit ?? '-'}</div>
                                                    </div>
                                                    <div class="col-md-5">
                                                        <div><small class="text-muted">Rujukan</small></div>
                                                        <div>${it.result_reference ?? '-'}</div>
                                                    </div>
                                                </div>`;
                                        }
                                        return `
                                          <tr>
                                            <td><div class="fw-semibold">${it.test_name || ''}</div></td>
                                            <td>${hasilHtml}${it.result_notes ? `<div class="mt-1"><small class="text-muted">Catatan:</small> ${it.result_notes}</div>` : ''}</td>
                                            <td class="text-end">${formatRupiah(it.price)}</td>
                                          </tr>`;
                                    }).join('')}
                                  </tbody>
                                </table>
                              </div>
                            </div>`;
                    });
                    $container.html(html);
                });
            }

            // Load Radiology requests into its own section
            function loadRadiologyRequests() {
                const url = "{{ route('observasi.radiologyRequests', ':id') }}".replace(':id', ENCOUNTER_ID);
                $.get(url).done(function(rows) {
                    const $container = $('#radiology-requests-container');
                    if (!rows || rows.length === 0) {
                        $container.html('<div class="text-muted">Belum ada permintaan/hasil radiologi untuk encounter ini.</div>');
                        return;
                    }
                    let html = '';
                    rows.forEach(function(r) {
                        html += `
                        <div class="mb-3 border rounded p-2">
                          <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                              <strong>${r.created_at || ''}</strong>
                              <span class="badge bg-secondary ms-2">${(r.status||'').charAt(0).toUpperCase()+ (r.status||'').slice(1)}</span>
                            </div>
                            <div class="d-flex gap-2">
                              ${(r.status === 'completed') ? `<a href="/radiologi/permintaan/${r.id}/print" target="_blank" class="btn btn-sm btn-success">Cetak Hasil</a>` : ''}
                            </div>
                          </div>
                          <div>
                            <div class="fw-semibold mb-1">${r.jenis_name || ''}</div>
                            ${r.latest ? `<div class="small"><div><span class="text-muted">Findings:</span> ${r.latest.findings}</div><div><span class="text-muted">Impression:</span> ${r.latest.impression}</div></div>` : '<div class="text-muted small">Belum ada hasil.</div>'}
                          </div>
                        </div>`;
                    });
                    $container.html(html);
                });
            }

            // Rebind delete handler with radiology cancellation support
            $(document).off('click', '.btn-hapus-pemeriksaan');
            $(document).on('click', '.btn-hapus-pemeriksaan', function() {
                const id = $(this).data('id');
                const type = $(this).data('type');
                let deleteUrl, method, payload;
                if (type === 'radiologi') {
                    deleteUrl = "{{ route('observasi.radiologi.destroy', ':id') }}".replace(':id', id);
                    method = 'DELETE';
                    payload = { _token: '{{ csrf_token() }}' };
                } else {
                    deleteUrl = "{{ route('observasi.deletePemeriksaanPenunjang', ':id') }}".replace(':id', id);
                    method = 'DELETE';
                    payload = { _token: '{{ csrf_token() }}' };
                }
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
                            type: method,
                            data: payload,
                            dataType: 'json',
                            headers: { 'Accept': 'application/json' }
                        })
                            .done(function(resp){
                                if (resp && (resp.success === true || resp.status === true)) {
                                    swal((resp && resp.message) ? resp.message : 'Data telah dihapus', { icon: 'success' });
                                    loadPenunjangList();
                                    loadLabRequests();
                                    loadRadiologyRequests();
                                } else {
                                    swal((resp && resp.message) ? resp.message : 'Gagal menghapus data.', { icon: 'error' });
                                }
                            })
                            .fail(function(xhr){
                                let msg = 'Terjadi kesalahan saat menghapus data.';
                                if (xhr && xhr.responseJSON && xhr.responseJSON.message) { msg = xhr.responseJSON.message; }
                                swal(msg, { icon: 'error' });
                            });
                    }
                });
            });

            // Initial load on page open
            loadPenunjangList();
            loadLabRequests();
            loadRadiologyRequests();

            })();
    </script>
@endpush
