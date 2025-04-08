<?php

namespace App\Http\Controllers;

use App\Repositories\RuanganRepository;
use Illuminate\Http\Request;

class RuanganController extends Controller
{
    public $ruanganRepository;
    public function __construct(RuanganRepository $ruanganRepository) {
        $this->ruanganRepository = $ruanganRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ruangans = $this->ruanganRepository->index(); // Fetch all ruangan data
        return view('pages.ruangan.index', compact('ruangans')); // Pass data to the view
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
