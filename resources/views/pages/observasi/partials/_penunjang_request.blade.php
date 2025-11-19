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
                        <div class="mb-3 border rounded p-3" style="background-color: #f8f9fa;">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <strong class="fs-6">{{ $lr->created_at->format('d M Y H:i') }}</strong>
                                    <span class="badge bg-primary ms-2">{{ ucfirst($lr->status) }}</span>
                                </div>
                                <div class="d-flex gap-2">
                                    @if (auth()->user()->role != 2)
                                        @if ($lr->status === 'completed')
                                            <a href="{{ route('observasi.lab.print', $lr->id) }}" target="_blank"
                                                class="btn btn-sm btn-success"><i class="ri-printer-line"></i> Cetak
                                                Hasil</a>
                                        @endif
                                    @endif
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm mb-0" style="border: 2px solid #000;">
                                    <thead style="background-color: #ffffff;">
                                        <tr style="border-bottom: 2px solid #000;">
                                            <th style="width:35%; font-weight: bold; text-align: left; padding: 10px;">
                                                PEMERIKSAAN</th>
                                            <th
                                                style="width:20%; font-weight: bold; text-align: center; padding: 10px;">
                                                HASIL</th>
                                            <th
                                                style="width:15%; font-weight: bold; text-align: center; padding: 10px;">
                                                SATUAN</th>
                                            <th style="width:30%; font-weight: bold; text-align: left; padding: 10px;">
                                                NILAI NORMAL</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $groupedItems = $lr->items->groupBy(function ($item) {
                                                $name = strtolower($item->test_name);
                                                if (
                                                    str_contains($name, 'albumin') ||
                                                    str_contains($name, 'sgot') ||
                                                    str_contains($name, 'sgpt') ||
                                                    str_contains($name, 'bilirubin')
                                                ) {
                                                    return 'FUNGSI HATI';
                                                }
                                                if (
                                                    str_contains($name, 'ureum') ||
                                                    str_contains($name, 'creatinine') ||
                                                    str_contains($name, 'asam urat') ||
                                                    str_contains($name, 'uric acid')
                                                ) {
                                                    return 'FUNGSI GINJAL';
                                                }
                                                if (
                                                    str_contains($name, 'gula') ||
                                                    str_contains($name, 'glucose') ||
                                                    str_contains($name, 'andrandum') ||
                                                    str_contains($name, 'gds') ||
                                                    str_contains($name, 'gdp') ||
                                                    str_contains($name, 'gd2pp')
                                                ) {
                                                    return 'KADAR GULA DARAH';
                                                }
                                                if (
                                                    str_contains($name, 'cholesterol') ||
                                                    str_contains($name, 'kolesterol') ||
                                                    str_contains($name, 'trigliserida') ||
                                                    str_contains($name, 'hdl') ||
                                                    str_contains($name, 'ldl')
                                                ) {
                                                    return 'PROFIL LEMAK';
                                                }
                                                if (
                                                    str_contains($name, 'hemoglobin') ||
                                                    str_contains($name, 'leukosit') ||
                                                    str_contains($name, 'eritrosit') ||
                                                    str_contains($name, 'hematokrit') ||
                                                    str_contains($name, 'trombosit')
                                                ) {
                                                    return 'HEMATOLOGI';
                                                }
                                                return 'LAINNYA';
                                            });
                                            $categoryOrder = [
                                                'FUNGSI HATI',
                                                'FUNGSI GINJAL',
                                                'KADAR GULA DARAH',
                                                'PROFIL LEMAK',
                                                'HEMATOLOGI',
                                                'LAINNYA',
                                            ];
                                            $sortedGroups = collect($categoryOrder)
                                                ->filter(fn($cat) => $groupedItems->has($cat))
                                                ->mapWithKeys(fn($cat) => [$cat => $groupedItems[$cat]]);
                                        @endphp
                                        @foreach ($sortedGroups as $category => $items)
                                            @php
                                                // Check if category has non-grouped items (skip category header if all items are grouped)
                                                $hasNonGroupedItems = false;
                                                foreach ($items as $it) {
                                                    $hasPayload =
                                                        is_array($it->result_payload) && count($it->result_payload) > 0;
                                                    $isGrouped = false;
                                                    if ($hasPayload) {
                                                        foreach ($it->result_payload as $k => $v) {
                                                            if (is_array($v)) {
                                                                $isGrouped = true;
                                                                break;
                                                            }
                                                        }
                                                    }
                                                    if (!$isGrouped) {
                                                        $hasNonGroupedItems = true;
                                                        break;
                                                    }
                                                }
                                            @endphp
                                            @if ($hasNonGroupedItems)
                                                <tr>
                                                    <td colspan="4"
                                                        style="padding: 8px; background-color: #f8f9fa; font-weight: bold; font-style: italic;">
                                                        {{ $category }}
                                                    </td>
                                                </tr>
                                            @endif
                                            @foreach ($items as $it)
                                                @php
                                                    $hasPayload =
                                                        is_array($it->result_payload) && count($it->result_payload) > 0;
                                                    $isGroupedPayload = false;
                                                    if ($hasPayload) {
                                                        foreach ($it->result_payload as $k => $v) {
                                                            if (is_array($v)) {
                                                                $isGroupedPayload = true;
                                                                break;
                                                            }
                                                        }
                                                    }
                                                @endphp
                                                @if ($isGroupedPayload)
                                                    {{-- Test name sebagai header --}}
                                                    <tr>
                                                        <td colspan="4"
                                                            style="padding: 8px; background-color: #e9ecef; font-weight: bold;">
                                                            {{ $it->test_name }}
                                                        </td>
                                                    </tr>
                                                    @php
                                                        // Build template metadata map
                                                        $templateMeta = [];
                                                        if (
                                                            $it->jenisPemeriksaan &&
                                                            $it->jenisPemeriksaan->templateFields
                                                        ) {
                                                            foreach ($it->jenisPemeriksaan->templateFields as $field) {
                                                                if (
                                                                    $field->field_type === 'group' &&
                                                                    $field->fieldItems
                                                                ) {
                                                                    $groupMeta = [];
                                                                    foreach ($field->fieldItems as $item) {
                                                                        $groupMeta[$item->item_name] = [
                                                                            'label' =>
                                                                                $item->examination_name ??
                                                                                $item->item_label,
                                                                            'unit' => $item->unit,
                                                                            'normal_range' => $item->normal_range,
                                                                        ];
                                                                    }
                                                                    $templateMeta[$field->field_name] = $groupMeta;
                                                                }
                                                            }
                                                        }
                                                    @endphp
                                                    @foreach ($it->result_payload as $groupName => $groupItems)
                                                        @if (is_array($groupItems))
                                                            {{-- Sub-group header (hijau) --}}
                                                            <tr>
                                                                <td colspan="4"
                                                                    style="padding: 8px; background-color: #198754; color: white; font-weight: bold;">
                                                                    {{ ucwords(str_replace('_', ' ', $groupName)) }}
                                                                </td>
                                                            </tr>
                                                            {{-- Items dalam grup --}}
                                                            @foreach ($groupItems as $itemName => $itemValue)
                                                                @php
                                                                    $meta = $templateMeta[$groupName][$itemName] ?? [];
                                                                    $displayName =
                                                                        $meta['label'] ??
                                                                        ucwords(str_replace('_', ' ', $itemName));
                                                                    $unit = $meta['unit'] ?? '-';
                                                                    $normalRange = $meta['normal_range'] ?? '-';
                                                                @endphp
                                                                <tr>
                                                                    <td style="padding: 8px;">{{ $displayName }}</td>
                                                                    <td style="padding: 8px; text-align: center;">
                                                                        <strong>{{ $itemValue ?? '-' }}</strong>
                                                                    </td>
                                                                    <td style="padding: 8px; text-align: center;">
                                                                        {{ $unit }}</td>
                                                                    <td style="padding: 8px;">{{ $normalRange }}</td>
                                                                </tr>
                                                            @endforeach
                                                        @endif
                                                    @endforeach
                                                @else
                                                    {{-- Format biasa (non-grouped) --}}
                                                    <tr>
                                                        <td style="padding: 8px;">{{ $it->test_name }}</td>
                                                        <td style="padding: 8px; text-align: center;">
                                                            @if ($hasPayload)
                                                                @foreach ($it->result_payload as $k => $v)
                                                                    @if ($k === 'result_value' || $k === 'value' || $k === 'hasil')
                                                                        <strong>{{ is_array($v) ? json_encode($v) : $v }}</strong>
                                                                        @break
                                                                    @endif
                                                                @endforeach
                                                            @else
                                                                <strong>{{ $it->result_value ?? '-' }}</strong>
                                                            @endif
                                                        </td>
                                                        <td style="padding: 8px; text-align: center;">
                                                            {{ $it->result_unit ?? '-' }}</td>
                                                        <td style="padding: 8px;">{{ $it->result_reference ?? '-' }}
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @if ($lr->items->first()?->result_notes)
                                <div class="mt-2 text-muted small">
                                    <strong>Catatan:</strong> {{ $lr->items->first()?->result_notes }}
                                </div>
                            @endif
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
                    // Ensure data is an array
                    const items = Array.isArray(data) ? data : (data ? [data] : []);
                    items.forEach(function(item) {
                        const canDelete = (item.status === 'requested' || item.status === 'canceled');
                        const canPrint = item.status === 'completed';
                        const printUrl = item.type === 'lab' ?
                            `/laboratorium/requests/${item.request_id}/print?auto=1` :
                            `/radiologi/permintaan/${item.request_id}/print?auto=1`;
                        const printBtn = canPrint ?
                            `<a class="btn btn-sm btn-success" href="${printUrl}" target="_blank"><i class="ri-printer-line"></i> Cetak</a>` :
                            '';
                        const deleteBtn = canDelete ?
                            `<button class="btn btn-sm btn-danger btn-hapus-pemeriksaan" data-id="${item.id}" data-type="${item.type}">
                                <i class="ri-delete-bin-6-line"></i> Hapus
                            </button>` :
                            '';
                        // Show delete button for requested/canceled status, print button for completed
                        const actionsHtml = `${deleteBtn} ${printBtn}`;

                        // Status badge styling
                        let statusBadge = '';
                        let statusText = item.status ?? 'Unknown';
                        switch (statusText.toLowerCase()) {
                            case 'requested':
                                statusBadge =
                                    `<span class="badge rounded-pill bg-warning text-dark"><i class="ri-time-line"></i> Menunggu Pemeriksaan</span>`;
                                break;
                            case 'completed':
                                statusBadge =
                                    `<span class="badge rounded-pill bg-success"><i class="ri-checkbox-circle-line"></i> Selesai</span>`;
                                break;
                            case 'canceled':
                                statusBadge =
                                    `<span class="badge rounded-pill bg-danger"><i class="ri-close-circle-line"></i> Dibatalkan</span>`;
                                break;
                            case 'in_progress':
                                statusBadge =
                                    `<span class="badge rounded-pill bg-info"><i class="ri-loader-4-line"></i> Sedang Dikerjakan</span>`;
                                break;
                            default:
                                statusBadge =
                                    `<span class="badge rounded-pill bg-secondary">${statusText}</span>`;
                        }

                        // Type badge with icon
                        const typeBadge = item.type === 'lab' ?
                            `<span class="badge bg-info"><i class="ri-flask-line"></i> Laboratorium</span>` :
                            `<span class="badge bg-primary"><i class="ri-heart-pulse-line"></i> Radiologi</span>`;

                        tbody.append(`
          <tr>
            <td>
              <div class="fw-semibold mb-1">${item.jenis_pemeriksaan}</div>
              <div class="d-flex gap-1 align-items-center flex-wrap">
                ${typeBadge}
                ${statusBadge}
              </div>
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
                // Enhanced status badge with icons
                let statusBadge = '';
                const status = (lr.status || '').toLowerCase();
                switch (status) {
                    case 'completed':
                        statusBadge =
                            `<span class="badge rounded-pill bg-success ms-2"><i class="ri-checkbox-circle-line"></i> Hasil Tersedia</span>`;
                        break;
                    case 'requested':
                        statusBadge =
                            `<span class="badge rounded-pill bg-warning text-dark ms-2"><i class="ri-time-line"></i> Menunggu Hasil</span>`;
                        break;
                    case 'in_progress':
                        statusBadge =
                            `<span class="badge rounded-pill bg-info ms-2"><i class="ri-loader-4-line"></i> Sedang Diproses</span>`;
                        break;
                    case 'canceled':
                        statusBadge =
                            `<span class="badge rounded-pill bg-danger ms-2"><i class="ri-close-circle-line"></i> Dibatalkan</span>`;
                        break;
                    default:
                        statusBadge =
                            `<span class="badge rounded-pill bg-secondary ms-2">${status.charAt(0).toUpperCase()+ status.slice(1)}</span>`;
                }

                // Group items by category
                const categories = {
                    'FUNGSI HATI': [],
                    'FUNGSI GINJAL': [],
                    'KADAR GULA DARAH': [],
                    'PROFIL LEMAK': [],
                    'HEMATOLOGI': [],
                    'LAINNYA': []
                };

                (lr.items || []).forEach(function(item) {
                    const name = (item.test_name || '').toLowerCase();
                    if (name.includes('albumin') || name.includes('sgot') || name.includes('sgpt') || name
                        .includes('bilirubin')) {
                        categories['FUNGSI HATI'].push(item);
                    } else if (name.includes('ureum') || name.includes('creatinine') || name.includes(
                            'asam urat') || name.includes('uric acid')) {
                        categories['FUNGSI GINJAL'].push(item);
                    } else if (name.includes('gula') || name.includes('glucose') || name.includes(
                            'andrandum') || name.includes('gds') || name.includes('gdp') || name.includes(
                            'gd2pp')) {
                        categories['KADAR GULA DARAH'].push(item);
                    } else if (name.includes('cholesterol') || name.includes('kolesterol') || name.includes(
                            'trigliserida') || name.includes('hdl') || name.includes('ldl')) {
                        categories['PROFIL LEMAK'].push(item);
                    } else if (name.includes('hemoglobin') || name.includes('leukosit') || name.includes(
                            'eritrosit') || name.includes('hematokrit') || name.includes('trombosit')) {
                        categories['HEMATOLOGI'].push(item);
                    } else {
                        categories['LAINNYA'].push(item);
                    }
                });

                let itemsHtml = '';
                const categoryOrder = ['FUNGSI HATI', 'FUNGSI GINJAL', 'KADAR GULA DARAH', 'PROFIL LEMAK', 'HEMATOLOGI',
                    'LAINNYA'
                ];
                categoryOrder.forEach(function(category) {
                    if (categories[category].length > 0) {
                        // Check if category has non-grouped items
                        let hasNonGroupedItems = false;
                        categories[category].forEach(function(item) {
                            const payload = item.result_payload;
                            let isGrouped = false;
                            if (payload && typeof payload === 'object') {
                                for (let key in payload) {
                                    if (typeof payload[key] === 'object' && payload[key] !== null) {
                                        isGrouped = true;
                                        break;
                                    }
                                }
                            }
                            if (!isGrouped) {
                                hasNonGroupedItems = true;
                            }
                        });

                        // Only show category header if there are non-grouped items AND not LAINNYA category
                        if (hasNonGroupedItems && category !== 'LAINNYA') {
                            itemsHtml +=
                                `<tr><td colspan="4" style="padding: 8px; background-color: #e3f2fd; font-weight: bold; border-left: 4px solid #0dcaf0;">${category}</td></tr>`;
                        }

                        categories[category].forEach(function(item) {
                            itemsHtml += buildLabResultRow(item);
                        });
                    }
                });

                // Determine if there are actual results
                const hasResults = itemsHtml && itemsHtml.trim() !== '';

                // Content based on status - sama seperti radiologi
                let contentHtml = '';
                if (hasResults) {
                    contentHtml = `
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm mb-0" style="border: 2px solid #dee2e6;">
                                <thead style="background: linear-gradient(135deg, #0dcaf0 0%, #0d6efd 100%); color: white;">
                                    <tr>
                                        <th style="width:35%; font-weight: bold; text-align: left; padding: 10px;">PEMERIKSAAN</th>
                                        <th style="width:20%; font-weight: bold; text-align: center; padding: 10px;">HASIL</th>
                                        <th style="width:15%; font-weight: bold; text-align: center; padding: 10px;">SATUAN</th>
                                        <th style="width:30%; font-weight: bold; text-align: left; padding: 10px;">NILAI NORMAL</th>
                                    </tr>
                                </thead>
                                <tbody>${itemsHtml}</tbody>
                            </table>
                        </div>`;
                } else {
                    // Tampilan sederhana seperti radiologi
                    contentHtml = '<div class="text-muted small">Belum ada hasil.</div>';
                }

                return `
                    <div class="mb-3 border rounded p-3 shadow-sm" style="background: linear-gradient(135deg, #f5f7fa 0%, #ffffff 100%);">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <div class="d-flex align-items-center gap-2">
                                    <i class="ri-calendar-line text-info"></i>
                                    <strong class="text-dark">${lr.created_at || ''}</strong>
                                </div>
                                ${statusBadge}
                            </div>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-3 p-2 rounded" style="background-color: rgba(13, 202, 240, 0.08);">
                                <i class="ri-flask-line fs-5 text-info"></i>
                                <div class="fw-semibold text-info">${lr.jenis_name || 'Pemeriksaan Laboratorium'}</div>
                            </div>
                            ${contentHtml}
                        </div>
                    </div>`;
            }

            function buildLabResultRow(item) {
                const testName = item.test_name || '';
                const payload = item.result_payload;
                const templateMeta = item.template_meta || {};
                let html = '';

                // Check if payload has grouped structure (contains objects)
                let isGroupedPayload = false;
                if (payload && typeof payload === 'object') {
                    for (let key in payload) {
                        if (typeof payload[key] === 'object' && payload[key] !== null) {
                            isGroupedPayload = true;
                            break;
                        }
                    }
                }

                if (isGroupedPayload) {
                    // Test name sebagai header
                    html +=
                        `<tr><td colspan="4" style="padding: 8px; background-color: #e9ecef; font-weight: bold;">${testName}</td></tr>`;

                    // Loop through groups
                    for (let groupName in payload) {
                        if (typeof payload[groupName] === 'object' && payload[groupName] !== null) {
                            // Convert group name from snake_case to Title Case
                            const displayGroupName = groupName.split('_').map(w => w.charAt(0).toUpperCase() + w.slice(
                                1)).join(' ');

                            // Sub-group header (hijau)
                            html +=
                                `<tr><td colspan="4" style="padding: 8px; background-color: #198754; color: white; font-weight: bold;">${displayGroupName}</td></tr>`;

                            // Get metadata for this group
                            const groupMeta = templateMeta[groupName] || {};

                            // Items dalam grup
                            for (let itemName in payload[groupName]) {
                                const itemValue = payload[groupName][itemName] || '-';
                                const meta = groupMeta[itemName] || {};
                                const displayItemName = meta.label || itemName.split('_').map(w => w.charAt(0)
                                    .toUpperCase() + w.slice(1)).join(' ');
                                const unit = meta.unit || '-';
                                const normalRange = meta.normal_range || '-';

                                html +=
                                    `<tr><td style="padding: 8px;">${displayItemName}</td><td style="padding: 8px; text-align: center;"><strong>${itemValue}</strong></td><td style="padding: 8px; text-align: center;">${unit}</td><td style="padding: 8px;">${normalRange}</td></tr>`;
                            }
                        }
                    }
                } else {
                    // Format biasa (non-grouped)
                    const resultValue = item.result_value || (payload && payload.value) || '-';
                    const resultUnit = item.result_unit || '-';
                    const resultReference = item.result_reference || '-';
                    html =
                        `<tr><td style="padding: 8px;">${testName}</td><td style="padding: 8px; text-align: center;"><strong>${resultValue}</strong></td><td style="padding: 8px; text-align: center;">${resultUnit}</td><td style="padding: 8px;">${resultReference}</td></tr>`;
                }

                return html;
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
                // Enhanced status badge with icons for radiology
                let statusBadge = '';
                const status = (r.status || '').toLowerCase();
                switch (status) {
                    case 'completed':
                        statusBadge =
                            `<span class="badge rounded-pill bg-success ms-2"><i class="ri-checkbox-circle-line"></i> Hasil Tersedia</span>`;
                        break;
                    case 'requested':
                        statusBadge =
                            `<span class="badge rounded-pill bg-warning text-dark ms-2"><i class="ri-time-line"></i> Menunggu Hasil</span>`;
                        break;
                    case 'in_progress':
                        statusBadge =
                            `<span class="badge rounded-pill bg-info ms-2"><i class="ri-loader-4-line"></i> Sedang Diproses</span>`;
                        break;
                    case 'canceled':
                        statusBadge =
                            `<span class="badge rounded-pill bg-danger ms-2"><i class="ri-close-circle-line"></i> Dibatalkan</span>`;
                        break;
                    default:
                        statusBadge =
                            `<span class="badge rounded-pill bg-secondary ms-2">${status.charAt(0).toUpperCase()+ status.slice(1)}</span>`;
                }

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

                return `
                    <div class="mb-3 border rounded p-3 shadow-sm" style="background: linear-gradient(135deg, #f5f7fa 0%, #ffffff 100%);">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <div class="d-flex align-items-center gap-2">
                                    <i class="ri-calendar-line text-primary"></i>
                                    <strong class="text-dark">${r.created_at || ''}</strong>
                                </div>
                                ${statusBadge}
                            </div>
                            <div>${printBtn}</div>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-3 p-2 rounded" style="background-color: rgba(13, 110, 253, 0.08);">
                                <i class="ri-heart-pulse-line fs-5 text-primary"></i>
                                <div class="fw-semibold text-primary">${r.jenis_name || ''}</div>
                            </div>
                            ${contentHtml}
                        </div>
                    </div>`;
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
