{{-- Paket Pemeriksaan Section - Reusable for all registration modals --}}
{{-- $modalSuffix: rawatJalan, rawatInap, rawatDarurat --}}
<div class="card border-0 shadow-sm mb-4" id="paket-section-{{ $modalSuffix }}">
    <div class="card-header bg-white border-bottom">
        <div class="d-flex align-items-center justify-content-between">
            <h6 class="mb-0 text-dark fw-semibold">
                <i class="ri-gift-2-line me-2 text-info"></i>Paket Pemeriksaan
            </h6>
            <span class="badge bg-info-subtle text-info rounded-pill px-3 py-2" id="paket-count-{{ $modalSuffix }}">
                0 paket
            </span>
        </div>
    </div>
    <div class="card-body">
        {{-- Daftar Paket Pasien --}}
        <div id="paket-list-{{ $modalSuffix }}" class="mb-3">
            <div class="text-center text-muted py-2" id="paket-empty-{{ $modalSuffix }}">
                <i class="ri-inbox-line fs-4 d-block mb-1"></i>
                <small>Belum ada paket untuk pasien ini</small>
            </div>
            <div class="table-responsive d-none" id="paket-table-wrap-{{ $modalSuffix }}">
                <table class="table table-sm table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nama Paket</th>
                            <th class="text-center">Sesi</th>
                            <th class="text-end">Harga</th>
                            <th class="text-center">Status</th>
                            <th class="text-center" style="width: 60px;"></th>
                        </tr>
                    </thead>
                    <tbody id="paket-tbody-{{ $modalSuffix }}"></tbody>
                </table>
            </div>
        </div>

        {{-- Tambah Paket Baru --}}
        @if ($paketPemeriksaans->isNotEmpty())
            <div class="border-top pt-3">
                <div class="row align-items-end g-2">
                    <div class="col">
                        <label class="form-label fw-semibold mb-1">
                            <i class="ri-add-circle-line me-1 text-info"></i>Tambah Paket
                        </label>
                        <select class="form-select" id="selectPaket-{{ $modalSuffix }}">
                            <option value="" disabled selected>-- Pilih Paket --</option>
                            @foreach ($paketPemeriksaans as $pk)
                                <option value="{{ $pk->id }}"
                                    data-harga="{{ $pk->harga }}"
                                    data-gratis="{{ $pk->is_gratis ? '1' : '0' }}">
                                    {{ $pk->name }}
                                    — {{ $pk->is_gratis ? 'GRATIS' : 'Rp ' . number_format($pk->harga, 0, ',', '.') }}
                                    ({{ $pk->jumlah_sesi }} sesi)
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto">
                        <button type="button" class="btn btn-info btn-beliPaket" data-suffix="{{ $modalSuffix }}">
                            <i class="ri-add-line me-1"></i>Beli Paket
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
