<?php

namespace App\Repositories;

use App\Models\Bahan;
use App\Models\Historibahan;

class BahanRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }
    public function index()
    {
        // Ambil parameter pencarian jika ada
        $name = request('name');

        // Query dasar
        $query = Bahan::query()
            ->select(['id', 'name', 'is_expired', 'is_active', 'warning', 'updated_at'])
            // Hitung stok dengan withCount agar efisien
            ->withCount([
                'stokbahan as available_count' => function ($q) {
                    $q->where('is_available', 1);
                },
                'stokbahan as expired_count' => function ($q) {
                    $q->where('is_available', 1)->where('expired_at', '<', now());
                },
                'stokbahan as warning_count' => function ($q) {
                    // expired_at <= NOW() + INTERVAL bahans.warning DAY
                    $q->where('is_available', 1)
                      ->where('expired_at', '>=', now())
                      ->whereRaw('expired_at <= DATE_ADD(NOW(), INTERVAL bahans.warning DAY)');
                },
            ]);

        // Filter pencarian jika ada
        if ($name) {
            $query->where('name', 'like', '%' . $name . '%');
        }

        // Hindari eager load tindakan di index (akan dimuat via AJAX saat diperlukan)
        // Urutkan dan paginate
        return $query->orderBy('updated_at', 'DESC')->paginate(25);
    }
    public function show($id)
    {
        return Bahan::findOrFail($id);
    }
    public function create(array $data)
    {
        return Bahan::create($data);
    }
    public function update($id, array $data)
    {
        $bahan = Bahan::findOrFail($id);
        $bahan->update($data);
        return $bahan;
    }
    public function destroy($id)
    {
        $bahan = Bahan::findOrFail($id);
        // Cek apakah bahan sudah dipakai tindakan
        if ($bahan->tindakan->isNotEmpty()) {
            return false;
        } else {
            $bahan->delete();
            return $bahan;
        }
    }
    public function getBahan($id)
    {
        return Bahan::where('id', $id)->first();
    }
    public function stokBahan(array $data, $id)
    {

        $bahan = Bahan::findOrFail($id);
        $bahan->historibahan()->create($data);
        for ($i = 0; $i < $data['quantity']; $i++) {
            $bahan->stokbahan()->create([
                'expired_at' => $bahan->is_expired == 1 ? $data['expired_at'] : null,
                'description' => $data['description'],
                'is_available' => 1,
            ]);
        }
        return $bahan;
    }
    public function stokKeluar($id, array $data)
    {
        $bahan = Bahan::findOrFail($id);
        $bahan->historibahan()->create($data);

        $q = $bahan->stokbahan()->where('is_available', 1);
        // Jika user memberikan tanggal, batasi stok yang dikeluarkan hingga tanggal tsb (opsional)
        if (!empty($data['created_at'])) {
            $q->whereDate('created_at', '<=', $data['created_at']);
        }
        // Gunakan FIFO berdasarkan created_at asc
        $q->orderBy('created_at', 'asc')->take($data['quantity'])->delete();

        return $bahan;
    }
    public function getAllHistori()
    {
        return Historibahan::when(request('created_at'), function ($query) {
            return $query->where('created_at', request('created_at'));
        })
            ->whereMonth('created_at', date('m'))
            ->whereYear('created_at', date('Y'))
            ->with('bahan')
            ->orderBy('created_at', 'DESC')
            ->paginate(50);
    }
    // get RequestBahan
    public function getRequestBahan()
    {
        // Ambil data encounter yang hanya mempunyai request_bahan, urut encounter dan request_bahan desc
        return \App\Models\Encounter::where('status', 1)
            ->whereHas('requestBahan')
            ->with(['requestBahan' => function($q) {
                $q->orderBy('created_at', 'DESC');
            }])
            ->orderBy('updated_at', 'DESC')
            ->paginate(50);
    }
    // bahan diserahkan
    public function bahanDiserahkan($id, array $data)
    {
        $requestBahan = \App\Models\RequestBahan::findOrFail($id);
        $requestBahan->update($data);

        // Update stokbahan expired terdekat sebanyak qty yang diminta
        \App\Models\StokBahan::where('bahan_id', $requestBahan->bahan_id)
            ->where('is_available', 1)
            ->orderBy('expired_at', 'asc')
            ->limit($requestBahan->qty)
            ->update([
                'is_available' => 0,
                'description' => $requestBahan->keterangan,
                'date_used' => now(),
            ]);

        // Catat histori keluar
        \App\Models\Historibahan::create([
            'bahan_id'    => $requestBahan->bahan_id,
            'quantity'         => $requestBahan->qty,
            'description' => $requestBahan->keterangan,
            'status'      => 'keluar',
        ]);

        return $requestBahan;
    }
}
