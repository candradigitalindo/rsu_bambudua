<?php

namespace App\Http\Controllers;

use App\Repositories\AntrianRepository;
use Illuminate\Http\Request;

class AntrianController extends Controller
{
    public $antrianRepository;
    public function __construct(AntrianRepository $antrianRepository)
    {
        $this->antrianRepository = $antrianRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $lokasis = $this->antrianRepository->index();
        return view('pages.antrian.index', compact('lokasis'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store($id)
    {
        $antrian = $this->antrianRepository->store($id);
        return view('pages.antrian.cetak', compact('antrian'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $lokasi = $this->antrianRepository->show($id);
        return view('pages.antrian.show', compact('lokasi'));

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
