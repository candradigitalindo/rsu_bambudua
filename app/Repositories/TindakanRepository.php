<?php

namespace App\Repositories;

use App\Models\Tindakan;

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
}
