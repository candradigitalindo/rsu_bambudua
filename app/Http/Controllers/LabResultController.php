<?php

namespace App\Http\Controllers;

use App\Models\LabRequest;
use Illuminate\Http\Request;

class LabResultController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->input('q');
        $status = $request->input('status');
        // Default to show requests that still need result input
        $defaultStatuses = ['requested','collected','processing'];
        $statuses = $status ? [$status] : $defaultStatuses;

        $requests = LabRequest::with('encounter')
            ->when($q, function($qr) use ($q){
                $qr->whereHas('encounter', function($x) use ($q){
                    $x->where('rekam_medis','like',"%{$q}%")
                      ->orWhere('name_pasien','like',"%{$q}%");
                });
            })
            ->whereIn('status', $statuses)
            ->orderByDesc('created_at')
            ->paginate(15)->withQueryString();

        return view('pages.lab.results.index', [
            'requests' => $requests,
            'q' => $q,
            'status' => $status,
            'defaultStatuses' => $defaultStatuses,
        ]);
    }
}
