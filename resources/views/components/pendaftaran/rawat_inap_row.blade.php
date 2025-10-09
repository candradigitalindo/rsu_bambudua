<tr>
    <td>
        <table>
            <tr>
                <td>No. Kunjungan</td>
                <td>:</td>
                <td>{{ $d->no_encounter }}</td>
            </tr>
            <tr>
                <td>No. RM</td>
                <td>:</td>
                <td>{{ $d->rekam_medis }}</td>
            </tr>
            <tr>
                <td>Waktu</td>
                <td>:</td>
                <td>{{ $d->created_at_fmt ?? '-' }}</td>
            </tr>
            <tr>
                <td>
                    @php
                        $isActive = in_array($d->status, [1, '1', true, 'true'], true);
                        $statusText = $isActive ? 'Aktif' : 'Non-Aktif';
                        $badgeClass = $isActive ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary';
                    @endphp
                    <span class="badge {{ $badgeClass }} rounded-pill">
                        <i class="ri-circle-fill me-1"></i>Status : {{ $statusText }}
                    </span>
                </td>
            </tr>
        </table>
    </td>
    <td>
        <table>
            <tr>
                <td>Pasien</td>
                <td>:</td>
                <td>{{ $d->name_pasien }}</td>
            </tr>
            <tr>
                <td>Dokter</td>
                <td>:</td>
                <td>{{ $d->dokter }}</td>
            </tr>
        </table>
    </td>
    <td>
        <table>
            <tr>
                <td>Jenis Jaminan</td>
                <td>:</td>
                <td>{{ $d->jenis_jaminan }}</td>
            </tr>
            <tr>
                <td>No. Kamar</td>
                <td>:</td>
                <td>{{ optional($d->admission->room)->no_kamar ?? '-' }}</td>
            </tr>
        </table>
    </td>
    <td>
        <button type="button" class="btn editrawatInap btn-outline-primary btn-sm" data-bs-toggle="modal"
            data-bs-target="#modal-rawatInap" id="{{ $d->id }}" title="Edit pendaftaran Rawat Inap">
            <i class="ri-edit-2-fill"></i>
            Edit
        </button>
        <button type="button" class="btn destroyEncounterRinap btn-outline-danger btn-sm" id="{{ $d->id }}" title="Hapus pendaftaran Rawat Inap">
            <i class="ri-delete-bin-5-fill"></i>
            Hapus
        </button>
    </td>
</tr>
