<?php

namespace App\Http\Controllers;

use App\Models\Reagent;
use App\Models\ReagentTransaction;
use Illuminate\Http\Request;

class LabReagentController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->input('q');
        $reagents = Reagent::when($q, fn($qr)=>$qr->where('name','like',"%{$q}%"))
            ->orderBy('name')->paginate(15)->withQueryString();
        return view('pages.lab.reagents.index', compact('reagents','q'));
    }

    public function create() { return view('pages.lab.reagents.create'); }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'=>'required|string',
            'unit'=>'nullable|string',
            'warning_stock'=>'nullable|integer',
        ]);
        $data['stock'] = 0;
        $data['warning_stock'] = $data['warning_stock'] ?? 0;
        Reagent::create($data);
        return redirect()->route('lab.reagents.index')->with('success','Reagensia ditambahkan.');
    }

    public function edit(string $id)
    {
        $reagent = Reagent::findOrFail($id);
        return view('pages.lab.reagents.edit', compact('reagent'));
    }

    public function update(Request $request, string $id)
    {
        $reagent = Reagent::findOrFail($id);
        $data = $request->validate([
            'name'=>'required|string',
            'unit'=>'nullable|string',
            'warning_stock'=>'nullable|integer',
        ]);
        $reagent->update($data);
        return redirect()->route('lab.reagents.index')->with('success','Reagensia diperbarui.');
    }

    public function stock(Request $request, string $id)
    {
        $reagent = Reagent::findOrFail($id);
        $data = $request->validate([
            'type' => 'required|in:in,out',
            'qty' => 'required|integer|min:1',
            'notes' => 'nullable|string'
        ]);
        ReagentTransaction::create([
            'reagent_id'=>$reagent->id,
            'type'=>$data['type'],
            'qty'=>$data['qty'],
            'notes'=>$data['notes'] ?? null,
        ]);
        if ($data['type']==='in') {
            $reagent->stock += $data['qty'];
        } else {
            $reagent->stock = max(0, $reagent->stock - $data['qty']);
        }
        $reagent->save();
        return redirect()->route('lab.reagents.index')->with('success','Stok diperbarui.');
    }
}