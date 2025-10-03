<?php

namespace App\Http\Controllers;

use App\Models\CostCenter;
use Illuminate\Http\Request;

class CostCenterController extends Controller
{
    public function index()
    {
        $items = CostCenter::orderBy('name')->paginate(25);
        return view('pages.masterdata.cost_centers.index', compact('items'));
    }

    public function create()
    {
        return view('pages.masterdata.cost_centers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:cost_centers,name',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:255',
        ]);
        CostCenter::create([
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);
        return redirect()->route('master.cost-centers.index')->with('success', 'Cost Center ditambahkan.');
    }

    public function edit(CostCenter $cost_center)
    {
        return view('pages.masterdata.cost_centers.edit', compact('cost_center'));
    }

    public function update(Request $request, CostCenter $cost_center)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:cost_centers,name,' . $cost_center->id . ',id',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:255',
        ]);
        $cost_center->update([
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);
        return redirect()->route('master.cost-centers.index')->with('success', 'Cost Center diperbarui.');
    }

    public function destroy(CostCenter $cost_center)
    {
        $cost_center->delete();
        return back()->with('success', 'Cost Center dihapus.');
    }
}
