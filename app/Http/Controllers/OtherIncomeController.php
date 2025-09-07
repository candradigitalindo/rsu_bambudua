<?php

namespace App\Http\Controllers;

use App\Models\OtherIncome;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class OtherIncomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfMonth()->toDateString());

        $incomes = OtherIncome::whereBetween('income_date', [$startDate, $endDate])
            ->orderBy('income_date', 'desc')
            ->get();

        $title = 'Hapus Data!';
        $text = "Apakah Anda yakin ingin menghapus data ini?";
        confirmDelete($title, $text);

        return view('pages.keuangan.outherincome.index', compact('incomes', 'startDate', 'endDate'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.keuangan.outherincome.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'income_date' => 'required|date',
        ], [
            'description.required' => 'Deskripsi pendapatan harus diisi.',
            'amount.required' => 'Jumlah pendapatan harus diisi.',
            'amount.numeric' => 'Jumlah harus berupa angka.',
            'income_date.required' => 'Tanggal pendapatan harus diisi.',
        ]);

        OtherIncome::create($request->all());

        Alert::success('Berhasil', 'Data pendapatan lainnya berhasil ditambahkan.');
        return redirect()->route('pendapatan-lain.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OtherIncome $pendapatan_lain)
    {
        return view('pages.keuangan.outherincome.edit', ['income' => $pendapatan_lain]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, OtherIncome $pendapatan_lain)
    {
        $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'income_date' => 'required|date',
        ], [
            'description.required' => 'Deskripsi pendapatan harus diisi.',
            'amount.required' => 'Jumlah pendapatan harus diisi.',
            'amount.numeric' => 'Jumlah harus berupa angka.',
            'income_date.required' => 'Tanggal pendapatan harus diisi.',
        ]);

        $pendapatan_lain->update($request->all());

        Alert::success('Berhasil', 'Data pendapatan lainnya berhasil diperbarui.');
        return redirect()->route('pendapatan-lain.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OtherIncome $pendapatan_lain)
    {
        $pendapatan_lain->delete();

        Alert::info('Berhasil', 'Data pendapatan lainnya berhasil dihapus.');
        return back();
    }
}
