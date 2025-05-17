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
        $bahan = Bahan::where('name', 'like', '%' . request('name') . '%')->orderBy('updated_at', 'DESC')->paginate(50);
        // Cek bahan dipakai tindakan apa saja
        $bahan->map(function ($item) {
            $item->tindakan = $item->tindakan()->get();
            return $item;
        });
        return $bahan;
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
        $bahan->stokbahan()->where('is_available', 1)->orderBy('created_at', $data['created_at'] == null ? 'asc' : $data['created_at'])->take($data['quantity'])->delete();
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
}
