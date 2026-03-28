@extends('layouts.app')
@section('title', 'Berikan Paket ke Pasien')

@push('style')
<style>
    .step-indicator { display: flex; gap: 0; margin-bottom: 24px; }
    .step-item { flex: 1; text-align: center; padding: 12px 8px; position: relative; }
    .step-item .step-number { width: 32px; height: 32px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.85rem; margin-bottom: 4px; background: #e9ecef; color: #6c757d; transition: all 0.3s; }
    .step-item.active .step-number { background: #0d6efd; color: #fff; }
    .step-item.done .step-number { background: #198754; color: #fff; }
    .step-item .step-label { font-size: 0.75rem; color: #6c757d; }
    .step-item.active .step-label { color: #0d6efd; font-weight: 600; }
    .step-item.done .step-label { color: #198754; }
    .step-item::after { content: ''; position: absolute; top: 27px; right: -50%; width: 100%; height: 2px; background: #e9ecef; z-index: 0; }
    .step-item:last-child::after { display: none; }
    .step-item.done::after { background: #198754; }

    .patient-card { border: 2px solid #e9ecef; border-radius: 12px; padding: 16px; transition: all 0.3s; }
    .patient-card.selected { border-color: #0d6efd; background: #f0f4ff; }
    .patient-avatar-lg { width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1rem; color: #fff; background: #0d6efd; flex-shrink: 0; }

    .search-dropdown { position: absolute; z-index: 1050; background: #fff; border: 1px solid #dee2e6; border-radius: 10px; box-shadow: 0 8px 25px rgba(0,0,0,0.12); max-height: 300px; overflow-y: auto; width: 100%; }
    .search-dropdown .search-item { display: flex; align-items: center; gap: 12px; padding: 10px 16px; cursor: pointer; transition: background 0.15s; border-bottom: 1px solid #f1f3f5; }
    .search-dropdown .search-item:last-child { border-bottom: none; }
    .search-dropdown .search-item:hover { background: #f0f4ff; }
    .search-dropdown .search-item .mini-avatar { width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.7rem; color: #fff; background: #6c757d; flex-shrink: 0; }

    .paket-option-card { border: 2px solid #e9ecef; border-radius: 10px; padding: 14px; cursor: pointer; transition: all 0.2s; }
    .paket-option-card:hover { border-color: #adb5bd; }
    .paket-option-card.selected { border-color: #0d6efd; background: #f0f4ff; }
    .paket-option-card .paket-name { font-weight: 600; font-size: 0.95rem; }
    .paket-option-card .paket-meta { font-size: 0.8rem; color: #6c757d; display: flex; gap: 12px; margin-top: 4px; }
    .paket-option-card .paket-price-tag { font-weight: 700; font-size: 1.1rem; color: #2e7d32; }
    .paket-option-card .paket-free-tag { background: #e8f5e9; color: #2e7d32; padding: 2px 10px; border-radius: 20px; font-weight: 700; font-size: 0.78rem; }

    .summary-card { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 16px; color: #fff; padding: 24px; }
    .summary-card .sum-label { font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.8; }
    .summary-card .sum-value { font-size: 1rem; font-weight: 600; }
    .summary-card .sum-divider { border-top: 1px solid rgba(255,255,255,0.2); margin: 10px 0; }
    .summary-card .sum-price { font-size: 1.3rem; font-weight: 700; }
</style>
@endpush

@section('content')
    <form action="{{ route('paket-pemeriksaan.pasien.store') }}" method="POST" id="formAssign">
        @csrf
        <div class="row gx-3">
            <div class="col-xl-8 col-lg-8">
                <div class="card mb-3">
                    <div class="card-header">
                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ route('paket-pemeriksaan.pasien.index') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="ri-arrow-left-line"></i>
                            </a>
                            <h5 class="card-title mb-0"><i class="ri-user-add-line me-1"></i> Berikan Paket ke Pasien</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        {{-- Step Indicator --}}
                        <div class="step-indicator">
                            <div class="step-item" id="step1"><div class="step-number">1</div><div class="step-label">Pilih Pasien</div></div>
                            <div class="step-item" id="step2"><div class="step-number">2</div><div class="step-label">Pilih Paket</div></div>
                            <div class="step-item" id="step3"><div class="step-number">3</div><div class="step-label">Konfirmasi</div></div>
                        </div>

                        {{-- Step 1: Search Pasien --}}
                        <h6 class="text-muted mb-3"><i class="ri-user-search-line me-1"></i> Cari Pasien</h6>
                        <div class="position-relative mb-3">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text"><i class="ri-search-line"></i></span>
                                <input type="text" class="form-control" id="searchPasien"
                                    placeholder="Ketik nama pasien atau nomor RM..." autocomplete="off"
                                    value="{{ ($pasien ?? null) ? $pasien->name : '' }}">
                            </div>
                            <input type="hidden" name="pasien_id" id="pasienId"
                                value="{{ old('pasien_id', $pasien->id ?? '') }}">
                            <div id="searchResults" class="search-dropdown" style="display:none;"></div>
                            <p class="text-danger mb-0">{{ $errors->first('pasien_id') }}</p>
                        </div>

                        {{-- Selected Patient Card --}}
                        <div id="pasienInfo" class="patient-card selected mb-4" style="{{ ($pasien ?? null) ? '' : 'display:none;' }}">
                            <div class="d-flex align-items-center gap-3">
                                <div class="patient-avatar-lg" id="pasienAvatar">
                                    {{ ($pasien ?? null) ? strtoupper(substr($pasien->name, 0, 2)) : '' }}
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-bold fs-6" id="pasienName">{{ $pasien->name ?? '' }}</div>
                                    <div class="text-muted" style="font-size: 0.85rem;">
                                        <span><i class="ri-file-list-line me-1"></i>RM: <span id="pasienRM">{{ $pasien->rekam_medis ?? '' }}</span></span>
                                        <span class="ms-3"><i class="ri-map-pin-line me-1"></i><span id="pasienAlamat">{{ $pasien->alamat ?? '-' }}</span></span>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-danger" id="btnClearPasien" title="Ganti pasien">
                                    <i class="ri-close-line"></i>
                                </button>
                            </div>
                        </div>

                        <hr class="my-3">

                        {{-- Step 2: Pilih Paket --}}
                        <h6 class="text-muted mb-3"><i class="ri-gift-line me-1"></i> Pilih Paket Pemeriksaan</h6>
                        <input type="hidden" name="paket_pemeriksaan_id" id="paketId" value="{{ old('paket_pemeriksaan_id') }}">
                        <p class="text-danger mb-0">{{ $errors->first('paket_pemeriksaan_id') }}</p>
                        <div class="row g-2 mb-3" id="paketList">
                            @foreach ($pakets as $paket)
                                <div class="col-lg-6 col-md-6 col-12">
                                    <div class="paket-option-card {{ old('paket_pemeriksaan_id') == $paket->id ? 'selected' : '' }}"
                                         data-id="{{ $paket->id }}"
                                         data-sesi="{{ $paket->jumlah_sesi }}"
                                         data-harga="{{ $paket->harga }}"
                                         data-gratis="{{ $paket->is_gratis }}"
                                         data-berlaku="{{ $paket->masa_berlaku_hari }}"
                                         data-name="{{ $paket->name }}">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <div class="paket-name">{{ $paket->name }}</div>
                                                <div class="paket-meta">
                                                    <span><i class="ri-play-circle-line"></i> {{ $paket->jumlah_sesi }} sesi</span>
                                                    <span><i class="ri-calendar-line"></i> {{ $paket->masa_berlaku_hari }} hari</span>
                                                </div>
                                            </div>
                                            <div class="text-end">
                                                @if ($paket->is_gratis)
                                                    <span class="paket-free-tag">GRATIS</span>
                                                @else
                                                    <div class="paket-price-tag">Rp {{ number_format($paket->harga, 0, ',', '.') }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <hr class="my-3">

                        {{-- Step 3: Tanggal & Catatan --}}
                        <h6 class="text-muted mb-3"><i class="ri-calendar-check-line me-1"></i> Periode & Catatan</h6>
                        <div class="row gx-3">
                            <div class="col-lg-4 col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="ri-calendar-line"></i></span>
                                        <input type="date" class="form-control" name="tanggal_mulai" id="tanggalMulai"
                                            value="{{ old('tanggal_mulai', date('Y-m-d')) }}">
                                    </div>
                                    <p class="text-danger mb-0">{{ $errors->first('tanggal_mulai') }}</p>
                                </div>
                            </div>
                            <div class="col-lg-4 col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Tanggal Expired</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="ri-calendar-event-line"></i></span>
                                        <input type="text" class="form-control" id="tanggalExpired" readonly
                                            style="background: #f8f9fa;">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="ri-file-text-line me-1"></i> Catatan</label>
                            <textarea class="form-control" name="catatan" rows="2"
                                placeholder="Catatan tambahan (opsional)...">{{ old('catatan') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Summary Sidebar --}}
            <div class="col-xl-4 col-lg-4">
                <div class="summary-card" id="summaryCard">
                    <div class="text-center mb-3">
                        <i class="ri-file-check-line" style="font-size: 2rem; opacity: 0.8;"></i>
                        <div style="font-size: 0.85rem; opacity: 0.8;">Ringkasan</div>
                    </div>
                    <div class="sum-label">PASIEN</div>
                    <div class="sum-value" id="sumPasien">Belum dipilih</div>
                    <div class="sum-divider"></div>
                    <div class="sum-label">PAKET</div>
                    <div class="sum-value" id="sumPaket">Belum dipilih</div>
                    <div class="sum-divider"></div>
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="sum-label">SESI</div>
                            <div class="sum-value" id="sumSesi">-</div>
                        </div>
                        <div class="text-end">
                            <div class="sum-label">BERLAKU</div>
                            <div class="sum-value" id="sumBerlaku">-</div>
                        </div>
                    </div>
                    <div class="sum-divider"></div>
                    <div class="text-center">
                        <div class="sum-label">HARGA</div>
                        <div class="sum-price" id="sumHarga">-</div>
                    </div>
                    <div class="sum-divider"></div>
                    <div class="text-center">
                        <div class="sum-label">PERIODE</div>
                        <div class="sum-value" id="sumPeriode">-</div>
                    </div>
                </div>

                <div class="d-grid gap-2 mt-3">
                    <button type="submit" class="btn btn-primary btn-lg" id="btn-submit">
                        <i class="ri-check-double-line me-1"></i>
                        <span class="btn-txt">Berikan Paket</span>
                        <span class="spinner-border spinner-border-sm d-none" id="spinner"></span>
                    </button>
                    <a href="{{ route('paket-pemeriksaan.pasien.index') }}" class="btn btn-outline-secondary">
                        <i class="ri-arrow-left-line me-1"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let searchTimer;

    // Update step indicators
    function updateSteps() {
        var hasPasien = !!$('#pasienId').val();
        var hasPaket = !!$('#paketId').val();
        $('#step1').removeClass('active done').addClass(hasPasien ? 'done' : 'active');
        $('#step2').removeClass('active done').addClass(hasPasien ? (hasPaket ? 'done' : 'active') : '');
        $('#step3').removeClass('active done').addClass(hasPasien && hasPaket ? 'active' : '');
    }

    // Search patients
    $('#searchPasien').on('keyup', function() {
        clearTimeout(searchTimer);
        let q = $(this).val();
        if (q.length < 2) { $('#searchResults').hide(); return; }
        searchTimer = setTimeout(function() {
            $.get('{{ route("paket-pemeriksaan.search-pasien") }}', { q: q }, function(data) {
                let html = '';
                var colors = ['#1565c0','#2e7d32','#7b1fa2','#c62828','#00838f','#ef6c00'];
                data.forEach(function(p, i) {
                    var init = p.name.split(' ').map(function(w){ return w[0]; }).slice(0,2).join('').toUpperCase();
                    var color = colors[i % colors.length];
                    html += '<div class="search-item pasien-item" data-id="' + p.id + '" data-name="' + p.name +
                        '" data-rm="' + p.rekam_medis + '" data-alamat="' + (p.alamat || '-') + '">' +
                        '<div class="mini-avatar" style="background:' + color + '">' + init + '</div>' +
                        '<div><div class="fw-semibold">' + p.name + '</div>' +
                        '<small class="text-muted">RM: ' + p.rekam_medis + '</small></div></div>';
                });
                if (data.length === 0) {
                    html = '<div class="p-3 text-center text-muted"><i class="ri-user-search-line d-block mb-1" style="font-size:1.5rem;"></i>Pasien tidak ditemukan</div>';
                }
                $('#searchResults').html(html).show();
            });
        }, 300);
    });

    $(document).on('click', '.pasien-item', function(e) {
        e.preventDefault();
        var name = $(this).data('name');
        var init = name.split(' ').map(function(w){ return w[0]; }).slice(0,2).join('').toUpperCase();
        $('#pasienId').val($(this).data('id'));
        $('#pasienName').text(name);
        $('#pasienRM').text($(this).data('rm'));
        $('#pasienAlamat').text($(this).data('alamat'));
        $('#pasienAvatar').text(init);
        $('#searchPasien').val(name);
        $('#pasienInfo').slideDown(200);
        $('#searchResults').hide();
        updateSummary();
        updateSteps();
    });

    $('#btnClearPasien').on('click', function() {
        $('#pasienId').val('');
        $('#searchPasien').val('');
        $('#pasienInfo').slideUp(200);
        updateSummary();
        updateSteps();
    });

    $(document).click(function(e) {
        if (!$(e.target).closest('#searchPasien, #searchResults').length) {
            $('#searchResults').hide();
        }
    });

    // Paket selection
    $('.paket-option-card').on('click', function() {
        $('.paket-option-card').removeClass('selected');
        $(this).addClass('selected');
        $('#paketId').val($(this).data('id'));
        updateSummary();
        updateSteps();
    });

    $('#tanggalMulai').on('change', updateSummary);

    function updateSummary() {
        var pasienName = $('#pasienName').text() || 'Belum dipilih';
        var $card = $('.paket-option-card.selected');

        $('#sumPasien').text($('#pasienId').val() ? pasienName : 'Belum dipilih');

        if ($card.length) {
            var sesi = $card.data('sesi');
            var harga = $card.data('harga');
            var gratis = $card.data('gratis');
            var berlaku = $card.data('berlaku');
            var name = $card.data('name');

            $('#sumPaket').text(name);
            $('#sumSesi').text(sesi + ' sesi');
            $('#sumBerlaku').text(berlaku + ' hari');
            $('#sumHarga').text(gratis == 1 ? 'GRATIS' : 'Rp ' + parseInt(harga).toLocaleString('id-ID'));

            var mulai = $('#tanggalMulai').val();
            if (mulai) {
                var d = new Date(mulai);
                var dMulai = d.toLocaleDateString('id-ID', {day:'numeric',month:'short',year:'numeric'});
                d.setDate(d.getDate() + parseInt(berlaku));
                var dExp = d.toLocaleDateString('id-ID', {day:'numeric',month:'short',year:'numeric'});
                $('#tanggalExpired').val(dExp);
                $('#sumPeriode').text(dMulai + ' — ' + dExp);
            }
        } else {
            $('#sumPaket').text('Belum dipilih');
            $('#sumSesi, #sumBerlaku, #sumHarga, #sumPeriode').text('-');
            $('#tanggalExpired').val('');
        }
    }

    updateSummary();
    updateSteps();

    $('#formAssign').submit(function() {
        $('#spinner').removeClass('d-none');
        $('#btn-submit').attr('disabled', true);
    });
});
</script>
@endpush
