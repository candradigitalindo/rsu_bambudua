<?php

namespace App\Http\Controllers;

use App\Models\Satusehat;
use App\Repositories\SatusehatRepository;
use Illuminate\Http\Request;

class SatusehatController extends Controller
{
    public $satusehatRepository;
    public function __construct(SatusehatRepository $satusehatRepository)
    {
        $this->satusehatRepository = $satusehatRepository;
    }

    public function getSatusehat()
    {
        $satusehat = $this->satusehatRepository->getSatusehat();
        return view('pages.satusehat.index', compact('satusehat'));
    }

    public function saveSatusehat(Request $request)
    {
        $request->validate([
            'status'            => 'required|string',
            'organization_id'   => 'required|string',
            'client_id'         => 'required|string',
            'client_secret'     => 'required|string'
        ],[
            'status.required'           => 'Kolom masih kosong',
            'organization_id.required'  => 'Kolom masih kosong',
            'client_id.required'        => 'Kolom masih kosong',
            'client_secret.required'    => 'Kolom masih kosong'
        ]);

        $satusehat = $this->satusehatRepository->saveSatusehat($request);
        return redirect()->route('satusehat.index');
    }
}
