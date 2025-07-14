<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EncounterController extends Controller
{
    public $encounterRepository;
    public function __construct()
    {
        $this->encounterRepository = new \App\Repositories\EncounterRepository();
    }
    public function getAllRawatJalan()
    {
        $encounters = $this->encounterRepository->getAllRawatJalan();
        return view('pages.encounter.rawat-jalan', compact('encounters'));
    }
    // Cetak Encounter
    public function cetakEncounter($id)
    {
        $encounter = $this->encounterRepository->getEncounterById($id);
        return view('pages.encounter.cetak-rawat-jalan', compact('encounter'));
    }
    // rawat darurat
    public function getAllRawatDarurat()
    {
        $encounters = $this->encounterRepository->getAllRawatDarurat();
        return view('pages.encounter.rawat-darurat', compact('encounters'));
    }
    public function getAllRawatInap()
    {
        $encounters = $this->encounterRepository->getAllRawatInap();
        return view('pages.encounter.rawat-inap', compact('encounters'));
    }
}
