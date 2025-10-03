<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $q = request('q');
        $suppliers = Supplier::query()
            ->when($q, function($query, $q){
                $query->where('name','like',"%{$q}%")
                      ->orWhere('contact','like',"%{$q}%")
                      ->orWhere('phone','like',"%{$q}%")
                      ->orWhere('email','like',"%{$q}%");
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();
        return view('pages.masterdata.suppliers.index', compact('suppliers','q'));
    }

    public function create()
    {
        return view('pages.masterdata.suppliers.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'contact' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:150',
            'address' => 'nullable|string',
            'npwp' => 'nullable|string|max:50',
            'payment_terms' => 'nullable|string|max:100',
            'active' => 'nullable',
        ]);
        $data['active'] = $request->has('active');
        Supplier::create($data);
        return redirect()->route('suppliers.index')->with('success', 'Supplier ditambahkan.');
    }

    public function edit($id)
    {
        $supplier = Supplier::findOrFail($id);
        return view('pages.masterdata.suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'contact' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:150',
            'address' => 'nullable|string',
            'npwp' => 'nullable|string|max:50',
            'payment_terms' => 'nullable|string|max:100',
            'active' => 'nullable',
        ]);
        $data['active'] = $request->has('active');
        $supplier->update($data);
        return redirect()->route('suppliers.index')->with('success', 'Supplier diperbarui.');
    }

    public function destroy($id)
    {
        Supplier::findOrFail($id)->delete();
        return redirect()->route('suppliers.index')->with('success', 'Supplier dihapus.');
    }
}