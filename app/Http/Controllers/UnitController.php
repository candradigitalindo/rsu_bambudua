<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index()
    {
        $q = request('q');
        $units = Unit::query()
            ->when($q, function($query, $q){
                $query->where('name','like',"%{$q}%")
                      ->orWhere('abbrev','like',"%{$q}%");
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();
        return view('pages.masterdata.units.index', compact('units','q'));
    }

    public function create()
    {
        return view('pages.masterdata.units.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'abbrev' => 'nullable|string|max:20',
            'active' => 'nullable',
        ]);
        $data['active'] = $request->has('active');
        Unit::create($data);
        return redirect()->route('units.index')->with('success', 'Satuan ditambahkan.');
    }

    public function edit($id)
    {
        $unit = Unit::findOrFail($id);
        return view('pages.masterdata.units.edit', compact('unit'));
    }

    public function update(Request $request, $id)
    {
        $unit = Unit::findOrFail($id);
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'abbrev' => 'nullable|string|max:20',
            'active' => 'nullable',
        ]);
        $data['active'] = $request->has('active');
        $unit->update($data);
        return redirect()->route('units.index')->with('success', 'Satuan diperbarui.');
    }

    public function destroy($id)
    {
        Unit::findOrFail($id)->delete();
        return redirect()->route('units.index')->with('success', 'Satuan dihapus.');
    }
}