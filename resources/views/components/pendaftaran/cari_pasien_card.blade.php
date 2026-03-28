@php
    $hasEncounter = !empty($d->no_encounter) && $d->no_encounter !== '-';
    $statusText = null;
    $badgeClass = 'bg-warning-subtle text-warning';
    $accentColor = '#6c757d';

    if ($hasEncounter && !empty($d->type) && $d->type !== '-') {
        $statusText = $d->type;
        $badgeClass = match ($d->type) {
            'Rawat Jalan' => 'bg-primary-subtle text-primary',
            'Rawat Inap' => 'bg-info-subtle text-info',
            'IGD' => 'bg-danger-subtle text-danger',
            default => 'bg-warning-subtle text-warning',
        };
        $accentColor = match ($d->type) {
            'Rawat Jalan' => '#0d6efd',
            'Rawat Inap' => '#0dcaf0',
            'IGD' => '#dc3545',
            default => '#6c757d',
        };
    } else {
        $statusText = 'Pasien Baru';
        $badgeClass = 'bg-success-subtle text-success';
        $accentColor = '#198754';
    }

    $hasKerabatFlag = false;
    $kerabatBadges = [];

    if (isset($d->is_kerabat_dokter) && $d->is_kerabat_dokter) {
        $kerabatBadges[] = ['label' => 'Kerabat Dokter', 'icon' => 'ri-stethoscope-line', 'class' => 'bg-primary text-white'];
        $hasKerabatFlag = true;
    }
    if (isset($d->is_kerabat_karyawan) && $d->is_kerabat_karyawan) {
        $kerabatBadges[] = ['label' => 'Kerabat Karyawan', 'icon' => 'ri-team-line', 'class' => 'bg-success text-white'];
        $hasKerabatFlag = true;
    }
    if (isset($d->is_kerabat_owner) && $d->is_kerabat_owner) {
        $kerabatBadges[] = ['label' => 'Kerabat Owner', 'icon' => 'ri-vip-crown-line', 'class' => 'bg-warning text-dark'];
        $hasKerabatFlag = true;
    }
@endphp

<div class="patient-result-card" style="border-left: 4px solid {{ $accentColor }};">
    {{-- Header: Avatar + Name + Badges --}}
    <div class="prc-header">
        <div class="prc-avatar" style="background: {{ $accentColor }}15; color: {{ $accentColor }};">
            <i class="ri-user-3-fill"></i>
        </div>
        <div class="prc-identity">
            <div class="prc-name">{{ $d->name }}</div>
            <div class="prc-rm">
                <i class="ri-hashtag me-1"></i>RM: <strong>{{ $d->rekam_medis }}</strong>
                @if($d->no_identitas)
                    <span class="mx-1 text-muted">|</span>
                    <span>{{ $d->jenis_identitas }}: {{ $d->no_identitas }}</span>
                @endif
            </div>
        </div>
        <div class="prc-badges">
            <span class="badge {{ $badgeClass }} rounded-pill">
                <i class="ri-circle-fill me-1" style="font-size: 6px; vertical-align: middle;"></i>{{ $statusText }}
            </span>
            @foreach ($kerabatBadges as $badge)
                <span class="badge {{ $badge['class'] }} rounded-pill">
                    <i class="{{ $badge['icon'] }} me-1"></i>{{ $badge['label'] }}
                </span>
            @endforeach
        </div>
    </div>

    {{-- Body: Info Grid --}}
    <div class="prc-body">
        <div class="prc-info-grid">
            <div class="prc-info-item">
                <div class="prc-info-icon"><i class="ri-phone-line"></i></div>
                <div>
                    <div class="prc-info-label">No. Telepon</div>
                    <div class="prc-info-value">{{ $d->no_hp ?? '-' }}</div>
                </div>
            </div>
            <div class="prc-info-item">
                <div class="prc-info-icon"><i class="ri-map-pin-line"></i></div>
                <div>
                    <div class="prc-info-label">Alamat</div>
                    <div class="prc-info-value text-truncate" style="max-width: 200px;" title="{{ $d->alamat ?? '-' }}">{{ $d->alamat ?? '-' }}</div>
                </div>
            </div>
            @if($hasEncounter)
                <div class="prc-info-item">
                    <div class="prc-info-icon"><i class="ri-calendar-check-line"></i></div>
                    <div>
                        <div class="prc-info-label">Kunjungan Terakhir</div>
                        <div class="prc-info-value">{{ $d->tgl_encounter }} &middot; <span class="badge {{ $badgeClass }} rounded-pill" style="font-size: 10px;">{{ $d->type }}</span></div>
                    </div>
                </div>
            @else
                <div class="prc-info-item">
                    <div class="prc-info-icon text-muted"><i class="ri-calendar-todo-line"></i></div>
                    <div>
                        <div class="prc-info-label">Kunjungan Terakhir</div>
                        <div class="prc-info-value text-muted fst-italic">Belum ada kunjungan</div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Footer: Actions --}}
    <div class="prc-actions">
        <button type="button" class="btn rawatJalan btn-primary btn-sm" data-bs-toggle="modal"
            data-bs-target="#modal-rawatJalan" id="{{ $d->id }}">
            <i class="ri-stethoscope-line me-1"></i>Rawat Jalan
        </button>
        <button type="button" class="btn rawatDarurat btn-danger btn-sm" data-bs-toggle="modal"
            data-bs-target="#modal-rawatDarurat" id="{{ $d->id }}">
            <i class="ri-alarm-warning-line me-1"></i>IGD
        </button>
        <button type="button" class="btn edit btn-outline-secondary btn-sm" data-bs-toggle="modal"
            data-bs-target="#form-edit-pasien" id="{{ $d->id }}">
            <i class="ri-edit-box-line me-1"></i>Edit
        </button>
    </div>
</div>
