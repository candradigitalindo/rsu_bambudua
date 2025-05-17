<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Icd10Repository;
use App\Imports\Icd10Import;
use Maatwebsite\Excel\Facades\Excel;

class Icd10Controller extends Controller
{
    protected $icd10Repo;

    public function __construct(Icd10Repository $icd10Repo)
    {
        $this->icd10Repo = $icd10Repo;
    }

    public function index()
    {
        $icd10s = $this->icd10Repo->all();
        return view('pages.icd10.index', compact('icd10s'));
    }

    public function show($id)
    {
        $icd10 = $this->icd10Repo->find($id);
        return view('pages.icd10.show', compact('icd10'));
    }

    public function create()
    {
        return view('pages.icd10.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|max:10',
            'description' => 'required|string',
            'version' => 'nullable|string|max:10',
        ]);
        $this->icd10Repo->create($data);
        return redirect()->route('icd10.index')->with('success', 'ICD10 berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $icd10 = $this->icd10Repo->find($id);
        return view('pages.icd10.edit', compact('icd10'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'code' => 'required|string|max:10',
            'description' => 'required|string',
            'version' => 'nullable|string|max:10',
        ]);
        $this->icd10Repo->update($id, $data);
        return redirect()->route('icd10.index')->with('success', 'ICD10 berhasil diupdate.');
    }

    public function destroy($id)
    {
        $this->icd10Repo->delete($id);
        return redirect()->route('icd10.index')->with('success', 'ICD10 berhasil dihapus.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);
        Excel::import(new Icd10Import, $request->file('file'));
        return back()->with('success', 'Data ICD10 berhasil diimport!');
    }
}
