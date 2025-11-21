<?php

namespace App\Http\Controllers;

use App\Models\LabRequest;
use App\Models\Reagent;

class LabDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'today' => LabRequest::whereDate('created_at', now()->toDateString())->count(),
            'requested' => LabRequest::where('status', 'requested')->count(),
            'collected' => LabRequest::where('status', 'collected')->count(),
            'processing' => LabRequest::where('status', 'processing')->count(),
            'completed' => LabRequest::where('status', 'completed')->count(),
        ];

        // Reagent statistics
        $reagentStats = [
            'habis' => Reagent::where('stock', '<=', 0)->count(),
            'kadaluarsa' => Reagent::whereHas('batches', function ($q) {
                $q->where('expiry_date', '<=', now())
                    ->where('remaining_quantity', '>', 0);
            })->count(),
        ];

        $recent = LabRequest::with('encounter')->orderByDesc('created_at')->limit(10)->get();
        return view('pages.lab.dashboard.index', compact('stats', 'recent', 'reagentStats'));
    }
}
