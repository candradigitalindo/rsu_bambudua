<?php

namespace App\Repositories;

class ApotekRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }
    // ambil encounter yang status 2 beserta resep dan resep detailnya
    public function getEncounter($status = 2)
    {
        return \App\Models\Encounter::with(['resep.details', 'practitioner.user'])
            ->where('status', $status)
            ->whereYear('created_at', date('Y'))
            ->when(request('search'), function ($query, $search) {
                $query->where('name_pasien', 'like', '%' . $search . '%');
            })
            ->orderBy('status_bayar_resep', 'asc')      // status_bayar 0 di atas
            ->orderByDesc('updated_at')           // lalu urut terbaru
            ->paginate(50);
    }
    // bayar resep
    public function bayarResep($request,$id)
    {
        $encounter = \App\Models\Encounter::findOrFail($id);
        $encounter->status_bayar_resep = 1; // ubah status bayar menjadi 1
        $encounter->metode_pembayaran_resep = $request->metode_pembayaran;
        $encounter->save();
        return $encounter;
    }
    // cetak resep dari encounter
    public function cetakResep($id)
    {
        $encounter = \App\Models\Encounter::with(['resep.details', 'practitioner.user'])
            ->findOrFail($id);
        return $encounter;
    }
}
