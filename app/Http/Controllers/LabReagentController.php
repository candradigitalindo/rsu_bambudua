<?php

namespace App\Http\Controllers;

use App\Models\Reagent;
use App\Models\ReagentBatch;
use App\Models\ReagentTransaction;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth as FacadesAuth;

class LabReagentController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->input('q');
        $filter = $request->input('filter');

        $query = Reagent::when($q, fn($qr) => $qr->where('name', 'like', "%{$q}%"));

        // Hitung batch yang akan expired (dalam 30 hari) dan yang sudah expired
        $query->withCount([
            'batches as expiring_soon_count' => function ($q) {
                $q->where('remaining_quantity', '>', 0)
                    ->where('expiry_date', '>=', now())
                    ->where('expiry_date', '<=', now()->addDays(30));
            },
            'batches as expired_count' => function ($q) {
                $q->where('remaining_quantity', '>', 0)
                    ->where('expiry_date', '<', now());
            }
        ]);

        // Filter berdasarkan status
        if ($filter === 'habis') {
            $query->where('stock', '<=', 0);
        } elseif ($filter === 'kadaluarsa') {
            $query->whereHas('batches', function ($q) {
                $q->where('remaining_quantity', '>', 0)
                    ->where('expiry_date', '<', now());
            });
        }

        $reagents = $query->orderBy('name')->paginate(15)->appends($request->query());
        return view('pages.lab.reagents.index', compact('reagents', 'q', 'filter'));
    }

    public function create()
    {
        return view('pages.lab.reagents.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'unit' => 'nullable|string',
            'warning_stock' => 'nullable|integer',
        ]);
        $data['stock'] = 0;
        $data['warning_stock'] = $data['warning_stock'] ?? 0;
        Reagent::create($data);
        return redirect()->route('lab.reagents.index')->with('success', 'Reagensia ditambahkan.');
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
            'name' => 'required|string',
            'unit' => 'nullable|string',
            'warning_stock' => 'nullable|integer',
        ]);
        $reagent->update($data);
        return redirect()->route('lab.reagents.index')->with('success', 'Reagensia diperbarui.');
    }

    public function showStockForm(string $id)
    {
        $reagent = Reagent::findOrFail($id);
        return view('pages.lab.reagents.stock', compact('reagent'));
    }

    public function storeStock(Request $request, string $id)
    {
        $reagent = Reagent::findOrFail($id);
        $data = $request->validate([
            'type' => 'required|string|in:in,out,adjustment',
            'quantity' => 'required|integer|min:1',
            'expiry_date' => 'required_if:type,in|nullable|date',
            'notes' => 'nullable|string',
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () use ($reagent, $data) {
            if ($data['type'] === 'in') {
                $reagent->increment('stock', $data['quantity']);
                // Buat batch baru untuk stok masuk
                ReagentBatch::create([
                    'reagent_id' => $reagent->id,
                    'quantity' => $data['quantity'],
                    'remaining_quantity' => $data['quantity'],
                    'expiry_date' => $data['expiry_date'],
                ]);
            } else { // 'out' atau 'adjustment'
                if ($reagent->stock < $data['quantity']) {
                    throw new \Exception('Stok keluar melebihi stok yang tersedia.');
                }
                $reagent->decrement('stock', $data['quantity']);
                // Di sini idealnya ada logika pengurangan dari batch (FEFO)
                // Untuk saat ini, kita hanya kurangi total stok
            }

            // Catat transaksi ke histori
            ReagentTransaction::create([
                'reagent_id' => $reagent->id,
                'user_id' => FacadesAuth::id(),
                'type' => $data['type'],
                'qty' => $data['quantity'],
                'expiry_date' => $data['expiry_date'] ?? null,
                'notes' => $data['notes'],
            ]);
        });

        return redirect()->route('lab.reagents.index')->with('success', 'Stok reagen berhasil diperbarui.');
    }

    public function history(Request $request)
    {
        $query = ReagentTransaction::with(['reagent', 'user'])->latest();

        if ($q = $request->input('q')) {
            $query->whereHas('reagent', fn($sq) => $sq->where('name', 'like', "%{$q}%"));
        }
        // Tambahkan filter tanggal jika diperlukan

        $transactions = $query->paginate(20)->appends($request->query());
        return view('pages.lab.reagents.history', compact('transactions'));
    }
}
