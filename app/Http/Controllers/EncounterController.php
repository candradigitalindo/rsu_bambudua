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
}
