<?php

namespace App\Http\Controllers;

use App\Models\Incentive;
use App\Models\User;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class IncentiveController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Incentive::with('user')->where('type', 'manual')->orderBy('created_at', 'desc');

        // Filter berdasarkan bulan dan tahun
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        $query->whereMonth('created_at', $month)->whereYear('created_at', $year);

        $incentives = $query->get();

        confirmDelete('Hapus Data!', 'Apakah Anda yakin ingin menghapus insentif ini?');

        return view('pages.keuangan.insentif.index', compact('incentives', 'month', 'year'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Ambil semua user kecuali superadmin (role 1)
        $employees = User::where('role', '!=', 1)->orderBy('name')->get();
        return view('pages.keuangan.insentif.create', compact('employees'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:1',
            'description' => 'required|string|max:255',
        ], [
            'user_id.required' => 'Karyawan harus dipilih.',
            'amount.required' => 'Jumlah insentif harus diisi.',
            'description.required' => 'Deskripsi harus diisi.',
        ]);

        $amount = preg_replace('/[^\d]/', '', $request->amount);

        Incentive::create([
            'user_id' => $request->user_id,
            'amount' => $amount,
            'description' => $request->description,
            'type' => 'manual', // Tandai sebagai insentif manual
            'status' => 'pending',
            // Menggunakan bulan dan tahun dari request jika ada, jika tidak gunakan waktu sekarang
            // Ini akan berguna jika Anda ingin menambahkan insentif untuk periode lampau
            'year' => now()->year,
            'month' => now()->month,
        ]);

        Alert::success('Berhasil', 'Insentif manual berhasil ditambahkan.');
        return redirect()->route('keuangan.insentif.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Incentive $insentif_manual)
    {
        // Pastikan hanya insentif manual yang bisa diedit
        if ($insentif_manual->type !== 'manual') {
            Alert::error('Gagal', 'Insentif otomatis tidak dapat diubah dari sini.');
            return redirect()->route('keuangan.insentif.index');
        }

        $employees = User::where('role', '!=', 1)->orderBy('name')->get();
        return view('pages.keuangan.insentif.edit', [
            'incentive' => $insentif_manual,
            'employees' => $employees
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Incentive $insentif_manual)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:1',
            'description' => 'required|string|max:255',
        ]);

        // Pastikan hanya insentif manual yang bisa diupdate
        if ($insentif_manual->type !== 'manual' || $insentif_manual->status === 'paid') {
            Alert::error('Gagal', 'Insentif ini tidak dapat diubah.');
            return back();
        }

        $amount = preg_replace('/[^\d]/', '', $request->amount);

        $insentif_manual->update([
            'user_id' => $request->user_id,
            'amount' => $amount,
            'description' => $request->description,
        ]);

        Alert::success('Berhasil', 'Insentif manual berhasil diperbarui.');
        return redirect()->route('keuangan.insentif.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Incentive $insentif_manual)
    {
        // Pastikan hanya insentif manual yang bisa dihapus
        if ($insentif_manual->type !== 'manual' || $insentif_manual->status === 'paid') {
            Alert::error('Gagal', 'Insentif ini tidak dapat dihapus.');
            return back();
        }

        $insentif_manual->delete();

        Alert::info('Berhasil', 'Insentif manual berhasil dihapus.');
        return back();
    }
}
