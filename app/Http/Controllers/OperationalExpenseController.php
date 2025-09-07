<?php

namespace App\Http\Controllers;

use App\Models\OperationalExpense;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class OperationalExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Default ke bulan ini jika tidak ada filter tanggal
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfMonth()->toDateString());

        $query = OperationalExpense::query()
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->orderBy('expense_date', 'desc');

        if ($request->has('search') && $request->search != '') {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        $expenses = $query->get();

        $title = 'Hapus Data!';
        $text = "Apakah Anda yakin ingin menghapus data ini?";
        confirmDelete($title, $text);

        return view('pages.keuangan.operasional.index', compact('expenses', 'startDate', 'endDate'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.keuangan.operasional.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
        ], [
            'description.required' => 'Deskripsi pengeluaran harus diisi.',
            'amount.required' => 'Jumlah pengeluaran harus diisi.',
            'amount.numeric' => 'Jumlah harus berupa angka.',
            'expense_date.required' => 'Tanggal pengeluaran harus diisi.',
        ]);

        OperationalExpense::create($request->all());

        Alert::success('Berhasil', 'Data pengeluaran operasional berhasil ditambahkan.');
        return redirect()->route('operasional.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OperationalExpense $operasional)
    {
        return view('pages.keuangan.operasional.edit', ['expense' => $operasional]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, OperationalExpense $operasional)
    {
        $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
        ], [
            'description.required' => 'Deskripsi pengeluaran harus diisi.',
            'amount.required' => 'Jumlah pengeluaran harus diisi.',
            'amount.numeric' => 'Jumlah harus berupa angka.',
            'expense_date.required' => 'Tanggal pengeluaran harus diisi.',
        ]);

        $operasional->update($request->all());

        Alert::success('Berhasil', 'Data pengeluaran operasional berhasil diperbarui.');
        return redirect()->route('operasional.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OperationalExpense $operasional)
    {
        $operasional->delete();

        Alert::info('Berhasil', 'Data pengeluaran operasional berhasil dihapus.');
        return back();
    }
}
