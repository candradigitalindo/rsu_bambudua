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
    public function bayarResep($request, $id)
    {
        $encounter = \App\Models\Encounter::findOrFail($id);
        $encounter->status_bayar_resep = 1;
        $encounter->metode_pembayaran_resep = $request->metode_pembayaran;
        $encounter->save();

        $details = $encounter->resep && $encounter->resep->details ? $encounter->resep->details : [];
        foreach ($details as $detail) {
            // Update stok ProductApotek
            \App\Models\ProductApotek::where('id', $detail->product_apotek_id)
                ->decrement('stok', $detail->qty);

            // Ambil stok yang akan diupdate statusnya (expired_at dulu)
            $stokIds = \App\Models\ApotekStok::where('product_apotek_id', $detail->product_apotek_id)
                ->where('expired_at', $detail->expired_at)
                ->where('status', 0)
                ->orderBy('id')
                ->limit($detail->qty)
                ->pluck('id')
                ->toArray();

            // Jika stokIds kosong, ambil stok terlama tanpa filter expired_at
            if (empty($stokIds)) {
                $stokIds = \App\Models\ApotekStok::where('product_apotek_id', $detail->product_apotek_id)
                    ->where('status', 0)
                    ->orderBy('expired_at', 'asc')
                    ->orderBy('id')
                    ->limit($detail->qty)
                    ->pluck('id')
                    ->toArray();
            }

            // Update status stok sekaligus (lebih cepat)
            if (!empty($stokIds)) {
                \App\Models\ApotekStok::whereIn('id', $stokIds)->update(['status' => 1]);
            }

            // Catat histori stok keluar
            \App\Models\HistoriApotek::create([
                'product_apotek_id' => $detail->product_apotek_id,
                'jumlah' => -$detail->qty,
                'expired_at' => $detail->expired_at,
                'type' => 1,
                'keterangan' => 'Pengurangan stok karena resep ' . ($encounter->resep->kode_resep ?? ''),
            ]);
        }

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
