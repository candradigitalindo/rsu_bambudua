<?php

namespace App\Repositories;

use App\Events\AntrianEvent;
use App\Models\Antrian;
use App\Models\Loket;
use App\Models\Pasien;
use Illuminate\Support\Facades\Auth;

class PendaftaranRepository
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
        $loket   = Loket::where('user_id', Auth::user()->id)->first();
        if ($loket) {
            $antrian = Antrian::whereDate('created_at', date('Y-m-d'))->where('lokasiloket_id', $loket->lokasiloket_id)->where('status', 2)->orderBy('updated_at', 'DESC')->first();
            $jumlah  = Antrian::whereDate('created_at', date('Y-m-d'))->where('lokasiloket_id', $loket->lokasiloket_id)->where('status', 1)->count();
            return ['antrian' => $antrian == null ? 0 : $antrian->prefix." ".$antrian->nomor, 'jumlah' => $jumlah];
        }else{
            return ['antrian' => "--", 'jumlah' => 0];
        }
    }

    public function update_antrian()
    {
        $loket   = Loket::where('user_id', Auth::user()->id)->first();
        if ($loket) {
            $antrian = Antrian::whereDate('created_at', date('Y-m-d'))->where('lokasiloket_id', $loket->lokasiloket_id)->where('status', 1)->orderBy('nomor', 'ASC')->first();
            if ($antrian) {
                $d = AntrianEvent::dispatch($loket->lokasiloket_id);
                dd($d);
                $antrian->update(['status' => 2]);
            }
            $jumlah  = Antrian::whereDate('created_at', date('Y-m-d'))->where('lokasiloket_id', $loket->lokasiloket_id)->where('status', 1)->count();
            return ['antrian' => $antrian, 'loket' => $loket, 'jumlah' => $jumlah];
        }else {
            return null;
        }
    }

    public function cariPasien($request)
    {
        $pasiens = Pasien::where('name', 'like', '%' . $request->q . '%')->orWhere('rekam_medis', $request->q)->orWhere('no_identitas', $request->q)->orWhere('no_hp', $request->q)->orWhere('mr_lama', $request->q)->get();
        return $pasiens;
    }

    public function rawatJalan($request)
    {
        //
    }
}
