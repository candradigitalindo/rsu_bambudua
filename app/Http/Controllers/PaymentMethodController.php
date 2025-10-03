<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    public function index()
    {
        $q = request('q');
        $methods = PaymentMethod::query()
            ->when($q, function($query, $q){
                $query->where('name','like',"%{$q}%")->orWhere('code','like',"%{$q}%");
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();
        return view('pages.masterdata.payment_methods.index', compact('methods','q'));
    }

    public function create()
    {
        return view('pages.masterdata.payment_methods.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:50|unique:payment_methods,code',
            'active' => 'nullable',
        ]);
        $data['active'] = $request->has('active');
        PaymentMethod::create($data);
        return redirect()->route('payment-methods.index')->with('success', 'Metode pembayaran ditambahkan.');
    }

    public function edit($id)
    {
        $method = PaymentMethod::findOrFail($id);
        return view('pages.masterdata.payment_methods.edit', compact('method'));
    }

    public function update(Request $request, $id)
    {
        $method = PaymentMethod::findOrFail($id);
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:50|unique:payment_methods,code,' . $method->id,
            'active' => 'nullable',
        ]);
        $data['active'] = $request->has('active');
        $method->update($data);
        return redirect()->route('payment-methods.index')->with('success', 'Metode pembayaran diperbarui.');
    }

    public function destroy($id)
    {
        PaymentMethod::findOrFail($id)->delete();
        return redirect()->route('payment-methods.index')->with('success', 'Metode pembayaran dihapus.');
    }
}
