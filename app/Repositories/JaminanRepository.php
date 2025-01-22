<?php

namespace App\Repositories;

use App\Models\Jenisjaminan;

class JaminanRepository
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
        return Jenisjaminan::orderBy('created_at', 'DESC')->get();
    }

    public function store($request)
    {
        $jaminan = Jenisjaminan::create([
            "name"  => ucfirst($request->name),
            "status"=> $request->status
        ]);

        return $jaminan;
    }

    public function edit($id)
    {
        $jaminan = Jenisjaminan::findOrFail($id);
        if ($jaminan->status == 1) {
            $jaminan->update(['status' => 2]);
        }else {
            $jaminan->update(['status' => 1]);
        }

        return $jaminan;
    }

    public function destroy($id)
    {
        $jaminan = Jenisjaminan::findOrFail($id);
        $jaminan->delete();
        return $jaminan;
    }
}
