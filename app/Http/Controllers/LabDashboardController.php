<?php

namespace App\Http\Controllers;

use App\Models\LabRequest;

class LabDashboardController extends Controller
{
    public function index()
    {
        $statuses = ['requested','collected','processing','completed','cancelled'];
        $counts = [];
        foreach ($statuses as $st) {
            $counts[$st] = LabRequest::where('status', $st)->count();
        }
        $recent = LabRequest::with('encounter')->orderByDesc('created_at')->limit(10)->get();
        return view('pages.lab.dashboard.index', compact('counts','recent'));
    }
}
