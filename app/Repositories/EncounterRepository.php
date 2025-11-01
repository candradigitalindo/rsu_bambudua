<?php

namespace App\Repositories;

use App\Models\Encounter;
use App\Models\InpatientAdmission;
use Illuminate\Support\Facades\Auth;

class EncounterRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    protected function mapEncounter($encounter)
    {
        $encounter->status = $encounter->status == 1 ? "Progress" : "Finish";
        $encounter->jenis_jaminan = $encounter->jenis_jaminan == 1 ? "Umum" : "Lainnya";
        $encounter->tujuan_kunjungan = match ($encounter->tujuan_kunjungan) {
            1 => "Kunjungan Sehat (Promotif/Preventif)",
            2 => "Rehabilitatif",
            3 => "Kunjungan Sakit",
            4 => "Darurat",
            5 => "Kontrol / Tindak Lanjut",
            6 => "Treatment",
            7 => "Konsultasi",
            default => "-",
        };
        return $encounter;
    }

    public function getAllRawatJalan()
    {
        $query = Encounter::query()
            ->where('type', 1) // hanya rawat jalan
            ->where(function ($q) {
                $q->where('status', 1)
                    ->orWhere(function ($q2) {
                        $q2->where('status', 2)
                            ->whereDate('updated_at', now()->toDateString());
                    });
            });

        if (request('name')) {
            $query->where('name_pasien', 'like', '%' . request('name') . '%');
        }

        // Jika user dokter, filter by practitioner (tetap filter meskipun ada pencarian)
        if (Auth::user()->role == 2) {
            $query->whereHas('practitioner', function ($q) {
                $q->where('id_petugas', Auth::user()->id);
            });
        }

        $encounters = $query->orderBy('updated_at', 'asc')->get();

        return $encounters->map(fn($e) => $this->mapEncounter($e));
    }
    public function getAllRawatInap()
    {
        $query = InpatientAdmission::where('status', 'active')
            ->with(['encounter', 'patient', 'doctor', 'room', 'companions']);

        if (request('name')) {
            $query->whereHas('encounter', function ($q) {
                $q->where('name_pasien', 'like', '%' . request('name') . '%');
            });
        }

        // Filter khusus untuk dokter (role 2), tetap filter meskipun ada pencarian
        // Rawat inap menggunakan dokter_id yang sudah benar (users.id)
        if (Auth::user()->role == 2) {
            $query->where('dokter_id', Auth::user()->id);
        }

        return $query->orderBy('admission_date', 'desc')->get();
    }
    public function getAllRawatDarurat()
    {
        $query = Encounter::query()
            ->where('type', 3) // hanya rawat darurat
            ->where(function ($q) {
                $q->where('status', 1)
                    ->orWhere(function ($q2) {
                        $q2->where('status', 2)
                            ->whereDate('updated_at', now()->toDateString());
                    });
            });

        if (request('name')) {
            $query->where('name_pasien', 'like', '%' . request('name') . '%');
        }

        // Jika user dokter, filter by practitioner (tetap filter meskipun ada pencarian)
        if (Auth::user()->role == 2) {
            $query->whereHas('practitioner', function ($q) {
                $q->where('id_petugas', Auth::user()->id);
            });
        }

        $encounters = $query->orderBy('updated_at', 'asc')->get();

        return $encounters->map(fn($e) => $this->mapEncounter($e));
    }

    // Cetak Encounter
    public function getEncounterById($id)
    {
        $encounter = Encounter::with([
            'pasien',
            'practitioner.user',
            'diagnosis',
            'tindakan',
            'resep.details',
            'pemeriksaanPenunjang',
            'anamnesis',
            'tandaVital',
            'labRequests.items',
            'labRequests.requester',
            'radiologyRequests.jenis',
            'radiologyRequests.dokter'
        ])->findOrFail($id);
        return $this->mapEncounter($encounter);
    }
}
