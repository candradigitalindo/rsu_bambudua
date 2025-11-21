<?php

namespace App\Http\Controllers;

use App\Models\RadiologySupply;
use App\Models\RadiologySupplyBatch;
use App\Models\RadiologySupplyTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RadiologySupplyController extends Controller
{
    public function index(Request $request)
    {
        $query = RadiologySupply::with(['batches' => function ($q) {
            $q->where('remaining_quantity', '>', 0);
        }]);

        // Filter
        $filter = $request->get('filter');

        if ($filter === 'habis') {
            $query->where('stock', '<=', 0);
        } elseif ($filter === 'kadaluarsa') {
            $query->whereHas('batches', function ($q) {
                $q->where('expiry_date', '<=', now())
                    ->where('remaining_quantity', '>', 0);
            });
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('unit', 'like', "%{$search}%");
            });
        }

        $supplies = $query->latest()->paginate(10)->appends($request->query());

        return view('pages.radiology.supplies.index', compact('supplies', 'filter'));
    }

    public function create()
    {
        return view('pages.radiology.supplies.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'nullable|string|max:50',
            'warning_stock' => 'required|integer|min:0',
        ]);

        RadiologySupply::create($validated);

        return redirect()->route('radiologi.supplies.index')
            ->with('success', 'Bahan radiologi berhasil ditambahkan');
    }

    public function edit(RadiologySupply $supply)
    {
        return view('pages.radiology.supplies.edit', compact('supply'));
    }

    public function update(Request $request, RadiologySupply $supply)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'nullable|string|max:50',
            'warning_stock' => 'required|integer|min:0',
        ]);

        $supply->update($validated);

        return redirect()->route('radiologi.supplies.index')
            ->with('success', 'Bahan radiologi berhasil diperbarui');
    }

    public function destroy(RadiologySupply $supply)
    {
        $supply->delete();

        return redirect()->route('radiologi.supplies.index')
            ->with('success', 'Bahan radiologi berhasil dihapus');
    }

    public function updateStock(Request $request, RadiologySupply $supply)
    {
        $validated = $request->validate([
            'type' => 'required|in:in,out',
            'batch_number' => 'required|string',
            'quantity' => 'required|integer|min:1',
            'expiry_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            if ($validated['type'] === 'in') {
                // Tambah stok
                $batch = RadiologySupplyBatch::create([
                    'supply_id' => $supply->id,
                    'batch_number' => $validated['batch_number'],
                    'quantity' => $validated['quantity'],
                    'remaining_quantity' => $validated['quantity'],
                    'expiry_date' => $validated['expiry_date'] ?? null,
                ]);

                $supply->stock += $validated['quantity'];
                $supply->save();

                RadiologySupplyTransaction::create([
                    'supply_id' => $supply->id,
                    'batch_id' => $batch->id,
                    'type' => 'in',
                    'quantity' => $validated['quantity'],
                    'notes' => $validated['notes'],
                    'user_id' => Auth::id(),
                ]);

                $message = 'Stok berhasil ditambahkan';
            } else {
                // Kurang stok
                if ($supply->stock < $validated['quantity']) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Stok tidak mencukupi'
                    ], 400);
                }

                $supply->stock -= $validated['quantity'];
                $supply->save();

                // Kurangi dari batch yang tersedia (FIFO - First In First Out)
                $remainingQty = $validated['quantity'];
                $batches = $supply->batches()
                    ->where('remaining_quantity', '>', 0)
                    ->orderBy('expiry_date')
                    ->orderBy('created_at')
                    ->get();

                foreach ($batches as $batch) {
                    if ($remainingQty <= 0) break;

                    $deductQty = min($remainingQty, $batch->remaining_quantity);
                    $batch->remaining_quantity -= $deductQty;
                    $batch->save();
                    $remainingQty -= $deductQty;

                    RadiologySupplyTransaction::create([
                        'supply_id' => $supply->id,
                        'batch_id' => $batch->id,
                        'type' => 'out',
                        'quantity' => $deductQty,
                        'notes' => $validated['notes'],
                        'user_id' => Auth::id(),
                    ]);
                }

                $message = 'Stok berhasil dikurangi';
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function history(Request $request)
    {
        $query = RadiologySupplyTransaction::with(['supply', 'batch', 'user'])
            ->orderBy('created_at', 'desc');

        // Filter by supply
        if ($request->filled('supply_id')) {
            $query->where('supply_id', $request->supply_id);
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $transactions = $query->paginate(20)->appends($request->query());
        $supplies = RadiologySupply::orderBy('name')->get();

        return view('pages.radiology.supplies.history', compact('transactions', 'supplies'));
    }
}
