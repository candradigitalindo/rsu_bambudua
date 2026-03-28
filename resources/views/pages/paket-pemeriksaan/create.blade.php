@extends('layouts.app')
@section('title', 'Buat Paket Pemeriksaan Baru')

@push('style')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container .select2-selection--multiple {
        min-height: 42px; border: 1px solid #dee2e6; border-radius: 0.375rem;
        padding: 4px 8px; background: #fff !important;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #0d6efd; border: none; color: #fff;
        padding: 3px 10px 3px 24px; border-radius: 20px; font-size: 0.82rem; margin: 2px 4px 2px 0;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: #fff; font-weight: bold; margin-right: 4px; position: absolute; left: 8px;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover { color: #ffc107; }
    .gratis-switch { display: inline-flex; align-items: center; gap: 8px; cursor: pointer; user-select: none; }
    .gratis-switch .form-check-input { width: 2.2em; height: 1.1em; margin: 0; cursor: pointer; }
    .gratis-switch .form-check-input:checked { background-color: #20c997; border-color: #20c997; }
    .gratis-switch .gratis-label { font-size: 0.85rem; color: #6c757d; transition: color 0.2s; }
    .gratis-switch .form-check-input:checked ~ .gratis-label { color: #20c997; font-weight: 600; }
    .preview-card { background: linear-gradient(135deg, #238781 0%, #1a6b66 100%); border-radius: 16px; color: #fff; padding: 24px; position: sticky; top: 20px; }
    .preview-card .preview-title { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; opacity: 0.8; }
    .preview-card .preview-value { font-size: 1.1rem; font-weight: 600; }
    .preview-card .preview-divider { border-top: 1px solid rgba(255,255,255,0.2); margin: 12px 0; }
    .preview-card .preview-price { font-size: 1.5rem; font-weight: 700; }
    .isian-tabs .nav-link { font-size: 0.82rem; padding: 6px 14px; color: #6c757d; border-radius: 20px; }
    .isian-tabs .nav-link.active { font-weight: 600; }
    .isian-tabs .nav-link .tab-badge { font-size: 0.7rem; padding: 1px 6px; border-radius: 10px; margin-left: 4px; background: #e9ecef; color: #6c757d; }
    .isian-tabs .nav-link.active .tab-badge { background: rgba(255,255,255,0.9); }
    .items-list .item-row { display: flex; align-items: center; gap: 8px; padding: 6px 10px; background: #f8f9fa; border-radius: 6px; margin-bottom: 4px; font-size: 0.85rem; }
    .items-list .item-row .qty-input { width: 62px; text-align: center; padding: 2px 4px; }
</style>
@endpush

@php
    $oldTindakan = old('tindakan_items', []);
    $oldLab = old('lab_items', []);
    $oldRadiologi = old('radiologi_items', []);
    $oldObat = old('obat_items', []);
@endphp

@section('content')
    <form action="{{ route('paket-pemeriksaan.store') }}" method="POST" id="formPaket">
        @csrf
        <div class="row gx-3">
            {{-- Form Section --}}
            <div class="col-xl-8 col-lg-8">
                <div class="card mb-3">
                    <div class="card-header">
                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ route('paket-pemeriksaan.index') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="ri-arrow-left-line"></i>
                            </a>
                            <h5 class="card-title mb-0"><i class="ri-gift-line me-1"></i> Buat Paket Baru</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        {{-- Informasi Dasar --}}
                        <h6 class="text-muted mb-3"><i class="ri-information-line me-1"></i> Informasi Dasar</h6>
                        <div class="row gx-3">
                            <div class="col-lg-6 col-sm-12">
                                <div class="mb-3">
                                    <label class="form-label">Nama Paket <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="ri-gift-line"></i></span>
                                        <input type="text" class="form-control" name="name" id="inputName"
                                            value="{{ old('name') }}" placeholder="Contoh: Paket Fisioterapi 10 Sesi">
                                    </div>
                                    <p class="text-danger mb-0">{{ $errors->first('name') }}</p>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Jumlah Sesi <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="ri-play-circle-line"></i></span>
                                        <input type="number" class="form-control" name="jumlah_sesi" id="inputSesi"
                                            value="{{ old('jumlah_sesi', 1) }}" min="1">
                                    </div>
                                    <p class="text-danger mb-0">{{ $errors->first('jumlah_sesi') }}</p>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Status <span class="text-danger">*</span></label>
                                    <select class="form-select" name="status">
                                        <option value="1" {{ old('status', 1) == 1 ? 'selected' : '' }}>Aktif</option>
                                        <option value="0" {{ old('status') === '0' ? 'selected' : '' }}>Nonaktif</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <hr class="my-3">

                        {{-- Harga & Masa Berlaku --}}
                        <h6 class="text-muted mb-3"><i class="ri-money-dollar-circle-line me-1"></i> Harga & Masa Berlaku</h6>
                        <div class="row gx-3 align-items-start">
                            <div class="col-lg-6 col-sm-6" id="hargaCol">
                                <div class="mb-3">
                                    <label class="form-label">Harga Paket <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" inputmode="numeric" class="form-control @error('harga') is-invalid @enderror"
                                            name="harga" id="harga" value="{{ old('harga') ? number_format(old('harga'), 0, ',', '.') : '' }}">
                                        @error('harga')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="mt-2">
                                        <label class="gratis-switch" id="gratisToggle">
                                            <input class="form-check-input" type="checkbox" name="is_gratis"
                                                value="1" id="isGratis" {{ old('is_gratis') ? 'checked' : '' }}>
                                            <span class="gratis-label"><i class="ri-hand-heart-line me-1"></i>Gratis</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Masa Berlaku <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="ri-calendar-line"></i></span>
                                        <input type="number" class="form-control" name="masa_berlaku_hari" id="inputBerlaku"
                                            value="{{ old('masa_berlaku_hari', 30) }}" min="1">
                                        <span class="input-group-text">hari</span>
                                    </div>
                                    <p class="text-danger mb-0">{{ $errors->first('masa_berlaku_hari') }}</p>
                                </div>
                            </div>
                        </div>

                        <hr class="my-3">

                        {{-- Isian Paket Tabs --}}
                        <h6 class="text-muted mb-3"><i class="ri-stethoscope-line me-1"></i> Isian Paket <small class="text-muted fw-normal">(qty = jumlah per sesi)</small></h6>
                        <ul class="nav nav-pills isian-tabs mb-3" role="tablist">
                            <li class="nav-item"><a class="nav-link active" data-bs-toggle="pill" href="#tabTindakan"><i class="ri-stethoscope-line me-1"></i>Tindakan <span class="tab-badge" id="badgeTindakan">0</span></a></li>
                            <li class="nav-item"><a class="nav-link" data-bs-toggle="pill" href="#tabLab"><i class="ri-test-tube-line me-1"></i>Lab <span class="tab-badge" id="badgeLab">0</span></a></li>
                            <li class="nav-item"><a class="nav-link" data-bs-toggle="pill" href="#tabRadiologi"><i class="ri-body-scan-line me-1"></i>Radiologi <span class="tab-badge" id="badgeRadiologi">0</span></a></li>
                            <li class="nav-item"><a class="nav-link" data-bs-toggle="pill" href="#tabObat"><i class="ri-capsule-line me-1"></i>Obat <span class="tab-badge" id="badgeObat">0</span></a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="tabTindakan">
                                <select class="form-select item-select" id="tindakanSelect" multiple>
                                    @foreach ($tindakans as $tindakan)
                                        <option value="{{ $tindakan->id }}"
                                            data-name="{{ $tindakan->name }}"
                                            data-harga="{{ number_format($tindakan->harga, 0, ',', '.') }}"
                                            {{ array_key_exists($tindakan->id, $oldTindakan) ? 'selected' : '' }}>
                                            {{ $tindakan->name }} — Rp {{ number_format($tindakan->harga, 0, ',', '.') }}
                                        </option>
                                    @endforeach
                                </select>
                                <div id="tindakanItems" class="items-list mt-2"></div>
                            </div>
                            <div class="tab-pane fade" id="tabLab">
                                <select class="form-select item-select" id="labSelect" multiple>
                                    @foreach ($labs as $lab)
                                        <option value="{{ $lab->id }}"
                                            data-name="{{ $lab->name }}"
                                            data-harga="{{ number_format($lab->harga, 0, ',', '.') }}"
                                            {{ array_key_exists($lab->id, $oldLab) ? 'selected' : '' }}>
                                            {{ $lab->name }} — Rp {{ number_format($lab->harga, 0, ',', '.') }}
                                        </option>
                                    @endforeach
                                </select>
                                <div id="labItems" class="items-list mt-2"></div>
                            </div>
                            <div class="tab-pane fade" id="tabRadiologi">
                                <select class="form-select item-select" id="radiologiSelect" multiple>
                                    @foreach ($radiologis as $rad)
                                        <option value="{{ $rad->id }}"
                                            data-name="{{ $rad->name }}"
                                            data-harga="{{ number_format($rad->harga, 0, ',', '.') }}"
                                            {{ array_key_exists($rad->id, $oldRadiologi) ? 'selected' : '' }}>
                                            {{ $rad->name }} — Rp {{ number_format($rad->harga, 0, ',', '.') }}
                                        </option>
                                    @endforeach
                                </select>
                                <div id="radiologiItems" class="items-list mt-2"></div>
                            </div>
                            <div class="tab-pane fade" id="tabObat">
                                <select class="form-select item-select" id="obatSelect" multiple>
                                    @foreach ($obats as $obat)
                                        <option value="{{ $obat->id }}"
                                            data-name="{{ $obat->name }} ({{ $obat->satuan }})"
                                            data-harga="{{ number_format($obat->harga, 0, ',', '.') }}"
                                            {{ array_key_exists($obat->id, $oldObat) ? 'selected' : '' }}>
                                            {{ $obat->name }} ({{ $obat->satuan }}) — Rp {{ number_format($obat->harga, 0, ',', '.') }}
                                        </option>
                                    @endforeach
                                </select>
                                <div id="obatItems" class="items-list mt-2"></div>
                            </div>
                        </div>

                        <hr class="my-3">

                        {{-- Deskripsi --}}
                        <div class="mb-3">
                            <label class="form-label"><i class="ri-file-text-line me-1"></i> Deskripsi</label>
                            <textarea class="form-control" name="description" rows="3"
                                placeholder="Keterangan paket (opsional)...">{{ old('description') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Preview Card --}}
            <div class="col-xl-4 col-lg-4">
                <div class="preview-card" id="previewCard">
                    <div class="text-center mb-3">
                        <i class="ri-gift-line" style="font-size: 2.5rem; opacity: 0.8;"></i>
                    </div>
                    <div class="preview-title">NAMA PAKET</div>
                    <div class="preview-value" id="prevName">-</div>
                    <div class="preview-divider"></div>
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="preview-title">JUMLAH SESI</div>
                            <div class="preview-value" id="prevSesi">1 sesi</div>
                        </div>
                        <div class="text-end">
                            <div class="preview-title">MASA BERLAKU</div>
                            <div class="preview-value" id="prevBerlaku">30 hari</div>
                        </div>
                    </div>
                    <div class="preview-divider"></div>
                    <div class="text-center">
                        <div class="preview-title">HARGA</div>
                        <div class="preview-price" id="prevHarga">Rp 0</div>
                    </div>
                    <div class="preview-divider"></div>
                    <div class="preview-title">ISIAN PAKET <small style="opacity:0.7;">(per sesi)</small></div>
                    <div id="prevItems" style="font-size: 0.85rem; margin-top: 4px;">
                        <div class="d-flex justify-content-between"><span><i class="ri-stethoscope-line"></i> Tindakan</span><span id="prevTindakan" class="fw-bold">0</span></div>
                        <div class="d-flex justify-content-between"><span><i class="ri-test-tube-line"></i> Lab</span><span id="prevLab" class="fw-bold">0</span></div>
                        <div class="d-flex justify-content-between"><span><i class="ri-body-scan-line"></i> Radiologi</span><span id="prevRadiologi" class="fw-bold">0</span></div>
                        <div class="d-flex justify-content-between"><span><i class="ri-capsule-line"></i> Obat</span><span id="prevObat" class="fw-bold">0</span></div>
                    </div>
                    <div class="preview-divider"></div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-light btn-lg fw-bold" id="btn-submit" style="color: #238781;">
                            <i class="ri-save-line me-1"></i>
                            <span class="btn-txt">Simpan Paket</span>
                            <span class="spinner-border spinner-border-sm d-none" id="spinner"></span>
                        </button>
                        <a href="{{ route('paket-pemeriksaan.index') }}" class="btn btn-sm" style="color: rgba(255,255,255,0.7);">
                            <i class="ri-arrow-left-line me-1"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    var selectConfig = { allowClear: true, width: '100%' };
    $('#tindakanSelect').select2($.extend({placeholder: 'Cari dan pilih tindakan...'}, selectConfig));
    $('#labSelect').select2($.extend({placeholder: 'Cari dan pilih pemeriksaan lab...'}, selectConfig));
    $('#radiologiSelect').select2($.extend({placeholder: 'Cari dan pilih pemeriksaan radiologi...'}, selectConfig));
    $('#obatSelect').select2($.extend({placeholder: 'Cari dan pilih obat...'}, selectConfig));

    var initQty = {
        tindakan: @json($oldTindakan),
        lab: @json($oldLab),
        radiologi: @json($oldRadiologi),
        obat: @json($oldObat)
    };

    function syncItems(selectId, containerId, inputName, qtyMap) {
        var selected = $(selectId).val() || [];
        var container = $(containerId);
        container.find('.item-row').each(function() {
            if (selected.indexOf(String($(this).data('id'))) === -1) $(this).remove();
        });
        selected.forEach(function(id) {
            if (container.find('.item-row[data-id="' + id + '"]').length === 0) {
                var opt = $(selectId + ' option[value="' + id + '"]');
                var name = opt.data('name');
                var harga = opt.data('harga');
                var qty = qtyMap[id] || 1;
                container.append(
                    '<div class="item-row" data-id="' + id + '">' +
                    '<span class="flex-grow-1"><i class="ri-checkbox-circle-fill text-success me-1"></i>' + name +
                    ' <small class="text-muted">Rp ' + harga + '</small></span>' +
                    '<input type="number" name="' + inputName + '[' + id + ']" class="form-control form-control-sm qty-input" value="' + qty + '" min="1">' +
                    '<span class="text-muted" style="font-size:0.72rem;white-space:nowrap;">/ sesi</span>' +
                    '<button type="button" class="btn btn-sm btn-outline-danger py-0 px-1 btn-remove-item" data-select="' + selectId + '" data-id="' + id + '">' +
                    '<i class="ri-close-line"></i></button></div>'
                );
            }
        });
        updatePreview();
    }

    $(document).on('click', '.btn-remove-item', function() {
        var selectId = $(this).data('select');
        var id = String($(this).data('id'));
        var vals = ($(selectId).val() || []).filter(function(v) { return v !== id; });
        $(selectId).val(vals).trigger('change');
    });

    $(document).on('input', '.qty-input', function() { updatePreview(); });

    var tabs = [
        { select: '#tindakanSelect', container: '#tindakanItems', input: 'tindakan_items', key: 'tindakan' },
        { select: '#labSelect', container: '#labItems', input: 'lab_items', key: 'lab' },
        { select: '#radiologiSelect', container: '#radiologiItems', input: 'radiologi_items', key: 'radiologi' },
        { select: '#obatSelect', container: '#obatItems', input: 'obat_items', key: 'obat' }
    ];
    tabs.forEach(function(t) {
        $(t.select).on('change', function() { syncItems(t.select, t.container, t.input, initQty[t.key]); });
        syncItems(t.select, t.container, t.input, initQty[t.key]);
    });

    $('#isGratis').on('change', function() { toggleHarga(); updatePreview(); });
    function toggleHarga() {
        if ($('#isGratis').is(':checked')) {
            $('#harga').val('0').prop('disabled', true);
        } else {
            $('#harga').prop('disabled', false);
        }
    }
    toggleHarga();

    $('#harga').on('keyup', function() {
        let val = this.value.replace(/\D/g, '');
        this.value = val.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        updatePreview();
    });

    $('#inputName, #inputSesi, #inputBerlaku').on('input change', updatePreview);

    function sumQty(containerId) {
        var total = 0;
        $(containerId).find('.qty-input').each(function() { total += parseInt($(this).val()) || 0; });
        return total;
    }

    function countItems(containerId) {
        return $(containerId).find('.item-row').length;
    }

    function updatePreview() {
        var name = $('#inputName').val() || '-';
        var sesi = $('#inputSesi').val() || '0';
        var berlaku = $('#inputBerlaku').val() || '0';
        var harga = $('#harga').val() || '0';
        var isGratis = $('#isGratis').is(':checked');

        var tCount = countItems('#tindakanItems');
        var lCount = countItems('#labItems');
        var rCount = countItems('#radiologiItems');
        var oCount = countItems('#obatItems');

        var tQty = sumQty('#tindakanItems');
        var lQty = sumQty('#labItems');
        var rQty = sumQty('#radiologiItems');
        var oQty = sumQty('#obatItems');

        $('#badgeTindakan').text(tCount);
        $('#badgeLab').text(lCount);
        $('#badgeRadiologi').text(rCount);
        $('#badgeObat').text(oCount);

        $('#prevName').text(name);
        $('#prevSesi').text(sesi + ' sesi');
        $('#prevBerlaku').text(berlaku + ' hari');
        $('#prevHarga').text(isGratis ? 'GRATIS' : 'Rp ' + (harga || '0'));
        $('#prevTindakan').text(tCount > 0 ? tCount + ' jenis (' + tQty + ' qty)' : '0');
        $('#prevLab').text(lCount > 0 ? lCount + ' jenis (' + lQty + ' qty)' : '0');
        $('#prevRadiologi').text(rCount > 0 ? rCount + ' jenis (' + rQty + ' qty)' : '0');
        $('#prevObat').text(oCount > 0 ? oCount + ' jenis (' + oQty + ' qty)' : '0');
    }
    updatePreview();

    $('a[data-bs-toggle="pill"]').on('shown.bs.tab', function() {
        var target = $(this).attr('href');
        $(target).find('select').each(function() {
            $(this).select2({ allowClear: true, width: '100%', placeholder: $(this).data('placeholder') || 'Pilih...' });
        });
    });

    $('#formPaket').submit(function() {
        $('#harga').prop('disabled', false);
        $('#spinner').removeClass('d-none');
        $('#btn-submit').attr('disabled', true);
    });
});
</script>
@endpush
