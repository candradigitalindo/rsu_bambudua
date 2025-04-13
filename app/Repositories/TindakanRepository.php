<?php

namespace App\Repositories;

use App\Models\Bahan;
use App\Models\Tindakan;
use App\Models\TindakanBahan;

class TindakanRepository
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
        $tindakans = Tindakan::where('name', 'like', '%' . request('name') . '%')->orderBy('updated_at', 'DESC')->paginate(10);
        return $tindakans;
    }
    public function show($id)
    {
        return Tindakan::findOrFail($id);
    }

    public function create(array $data)
    {
        return Tindakan::create($data);
    }
    public function update($id, array $data)
    {
        $tindakan = Tindakan::findOrFail($id);
        $tindakan->update($data);
        return $tindakan;
    }
    public function destroy($id)
    {
        $tindakan = Tindakan::findOrFail($id);
        $tindakan->delete();
        return $tindakan;
    }
    public function getTindakan()
    {
        return Tindakan::where('status', true)->orderBy('name', 'ASC')->get();
    }
    public function getBahan()
    {
        return Bahan::orderBy('name', 'ASC')->get();
    }
    public function storeBahan($id, array $data)
    {
        $tindakan = Tindakan::findOrFail($id);
        TindakanBahan::create([
            'tindakan_id' => $tindakan->id,
            'bahan_id' => $data['bahan_id'],
            'quantity' => $data['quantity'],
        ]);
        return $tindakan;
    }
    public function destoryBahan($id)
    {
        $tindakanbahan = TindakanBahan::findOrFail($id);
        $tindakanbahan->delete();
        return $tindakanbahan;
    }
}
