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
                <td>
                    <span class="badge bg-primary-subtle rounded-pill text-primary">
                        <i class="ri-circle-fill me-1"></i>Status : {{ $d->status }}
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
                <td>{{ $d->jenis_jaminan }}</td>
            </tr>
            <tr>
                <td>{{ $d->tujuan_kunjungan }}</td>
            </tr>
        </table>
    </td>
    <td>
        <button type="button" class="btn editRawatDarurat btn-outline-primary btn-sm" data-bs-toggle="modal"
            data-bs-target="#modal-rawatDarurat" id="{{ $d->id }}">
            <i class="ri-edit-2-fill"></i>
            Edit
        </button>
        <button type="button" class="btn destroyRawatDarurat btn-outline-danger btn-sm" id="{{ $d->id }}">
            <i class="ri-delete-bin-5-fill"></i>
            Hapus
        </button>
    </td>
</tr>
