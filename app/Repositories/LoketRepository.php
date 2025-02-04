<?php

namespace App\Repositories;

use App\Models\LokasiLoket;
use App\Models\Loket;
use App\Models\User;

class LoketRepository
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
        $lokets     = Loket::orderBy('created_at', 'DESC')->get();
        $lokasis    = LokasiLoket::latest()->get();
        $users      = User::where('role', 5)->latest()->get();
        $lokets->map( function ($loket) {
            $loket['lokasi'] = $loket->lokasiloket->lokasi_loket;
            $user   = User::where('id', $loket->user_id)->first();
            $loket['user']   = $user == null ? null : $user->name;
        });
        return ['lokets' => $lokets, 'lokasis' => $lokasis, 'users' => $users];
    }

    public function store($request)
    {
        $loket = Loket::create(['lokasiloket_id' => $request->lokasi, 'kode_loket' => strtoupper($request->kode_loket), 'user_id' => $request->user]);
        return $loket;
    }

    public function destroy($id)
    {
        $loket = Loket::findOrFail($id);
        $loket->delete();
        return $loket;
    }
}
