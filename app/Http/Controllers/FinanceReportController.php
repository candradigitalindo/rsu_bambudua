<?php

namespace App\Http\Controllers;

use App\Models\Encounter;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FinanceReportController extends Controller
{
    public function kasBank(Request $request)
    {
        // TODO: query data berdasarkan $request->start_date, $request->end_date, dsb.
        return view('pages.keuangan.laporan.kas_bank');
    }

    public function kasBankExportPdf(Request $request)
    {
        return response('Export PDF Kas/Bank (placeholder). Tambahkan generator PDF di sini.', 200);
    }

    public function kasBankExportExcel(Request $request)
    {
        return response('Export Excel Kas/Bank (placeholder). Tambahkan generator Excel di sini.', 200);
    }

    public function arBpjs(Request $request)
    {
        return view('pages.keuangan.laporan.ar_bpjs');
    }

    public function arBpjsExportPdf(Request $request)
    {
        return response('Export PDF AR/Claim BPJS (placeholder).', 200);
    }

    public function arBpjsExportExcel(Request $request)
    {
        return response('Export Excel AR/Claim BPJS (placeholder).', 200);
    }

    public function apSupplier(Request $request)
    {
        return view('pages.keuangan.laporan.ap_supplier');
    }

    public function apSupplierExportPdf(Request $request)
    {
        return response('Export PDF AP/Supplier (placeholder).', 200);
    }

    public function apSupplierExportExcel(Request $request)
    {
        return response('Export Excel AP/Supplier (placeholder).', 200);
    }

    public function labaRugi(Request $request)
    {
        return view('pages.keuangan.laporan.laba_rugi');
    }

    public function labaRugiExportPdf(Request $request)
    {
        return response('Export PDF Laba-Rugi (placeholder).', 200);
    }

    public function labaRugiExportExcel(Request $request)
    {
        return response('Export Excel Laba-Rugi (placeholder).', 200);
    }

    // AR Aging
    public function arAging(Request $request)
    {
        $start = $request->input('start_date') ? Carbon::parse($request->input('start_date'))->startOfDay() : Carbon::now()->startOfMonth();
        $end   = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : Carbon::now()->endOfDay();
        $payer = $request->input('payer');

        $query = Encounter::query()
            ->whereBetween('created_at', [$start, $end])
            ->where(function ($q) {
                $q->where(function ($q1) {
                    $q1->where('total_bayar_tindakan', '>', 0)
                       ->where('status_bayar_tindakan', false);
                })->orWhere(function ($q2) {
                    $q2->where('total_bayar_resep', '>', 0)
                       ->where('status_bayar_resep', false);
                });
            });

        if (!empty($payer)) {
            $query->where('jenis_jaminan', $payer);
        }

        $encounters = $query->get();

        $rows = [];
        $overall = [ '0_30' => 0, '31_60' => 0, '61_90' => 0, '90p' => 0, 'total' => 0 ];
        $now = Carbon::now();

        foreach ($encounters as $enc) {
            $payerKey = $enc->jenis_jaminan ?: 'Lainnya/Umum';
            $ageDays = $now->diffInDays(Carbon::parse($enc->created_at));

            $outstanding = 0;
            if (!$enc->status_bayar_tindakan && $enc->total_bayar_tindakan > 0) {
                $outstanding += (float) $enc->total_bayar_tindakan;
            }
            if (!$enc->status_bayar_resep && $enc->total_bayar_resep > 0) {
                $outstanding += (float) $enc->total_bayar_resep;
            }
            if ($outstanding <= 0) continue;

            if (!isset($rows[$payerKey])) {
                $rows[$payerKey] = [ '0_30' => 0, '31_60' => 0, '61_90' => 0, '90p' => 0, 'total' => 0 ];
            }

            $bucket = '0_30';
            if ($ageDays >= 31 && $ageDays <= 60) $bucket = '31_60';
            elseif ($ageDays >= 61 && $ageDays <= 90) $bucket = '61_90';
            elseif ($ageDays > 90) $bucket = '90p';

            $rows[$payerKey][$bucket] += $outstanding;
            $rows[$payerKey]['total'] += $outstanding;

            $overall[$bucket] += $outstanding;
            $overall['total'] += $outstanding;
        }

        ksort($rows);

        return view('pages.keuangan.laporan.ar_aging', [
            'rows' => $rows,
            'overall' => $overall,
            'filters' => [
                'start_date' => $start->toDateString(),
                'end_date' => $end->toDateString(),
                'payer' => $payer,
            ],
        ]);
    }
    public function arAgingExportPdf(Request $request)
    {
        return response('Export PDF AR Aging (placeholder).', 200);
    }
    public function arAgingExportExcel(Request $request)
    {
        // Simple CSV export
        $start = $request->input('start_date');
        $end   = $request->input('end_date');
        $payer = $request->input('payer');

        // Reuse calculation
        $request2 = new Request(compact('start', 'end', 'payer'));
        $viewData = $this->arAging($request2)->getData();
        $rows = (array) ($viewData->rows ?? []);

        $csv = "Payer,0-30,31-60,61-90,>90,Total\n";
        foreach ($rows as $payerName => $data) {
            $csv .= sprintf("%s,%s,%s,%s,%s,%s\n",
                str_replace([",", "\n"], [';', ' '], $payerName),
                (int)($data['0_30'] ?? 0),
                (int)($data['31_60'] ?? 0),
                (int)($data['61_90'] ?? 0),
                (int)($data['90p'] ?? 0),
                (int)($data['total'] ?? 0)
            );
        }

        $filename = 'ar_aging_' . now()->format('Ymd_His') . '.csv';
        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    // AP Aging
    public function apAging(Request $request)
    {
        return view('pages.keuangan.laporan.ap_aging');
    }
    public function apAgingExportPdf(Request $request)
    {
        return response('Export PDF AP Aging (placeholder).', 200);
    }
    public function apAgingExportExcel(Request $request)
    {
        return response('Export Excel AP Aging (placeholder).', 200);
    }

    // Payor Mix
    public function payorMix(Request $request)
    {
        $start = $request->input('start_date') ? Carbon::parse($request->input('start_date'))->startOfDay() : Carbon::now()->startOfMonth();
        $end   = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : Carbon::now()->endOfDay();

        $encounters = Encounter::query()
            ->where(function ($q) {
                $q->where('status_bayar_tindakan', true)
                  ->orWhere('status_bayar_resep', true);
            })
            ->whereBetween('updated_at', [$start, $end])
            ->get();

        $mix = [];
        $total = 0;
        foreach ($encounters as $enc) {
            $payerKey = $enc->jenis_jaminan ?: 'Lainnya/Umum';
            $amount = 0;
            if ($enc->status_bayar_tindakan && $enc->total_bayar_tindakan > 0) $amount += (float) $enc->total_bayar_tindakan;
            if ($enc->status_bayar_resep && $enc->total_bayar_resep > 0) $amount += (float) $enc->total_bayar_resep;
            if ($amount <= 0) continue;
            if (!isset($mix[$payerKey])) $mix[$payerKey] = 0;
            $mix[$payerKey] += $amount;
            $total += $amount;
        }
        ksort($mix);

        $seriesData = array_values($mix);
        $categories = array_keys($mix);
        $percentages = [];
        foreach ($mix as $k => $v) {
            $percentages[$k] = $total > 0 ? round(($v / $total) * 100, 2) : 0;
        }

        return view('pages.keuangan.laporan.payor_mix', [
            'mix' => $mix,
            'total' => $total,
            'percentages' => $percentages,
            'chart' => [
                'categories' => $categories,
                'series' => [
                    [ 'name' => 'Pendapatan', 'data' => $seriesData ]
                ],
            ],
            'filters' => [
                'start_date' => $start->toDateString(),
                'end_date' => $end->toDateString(),
            ],
        ]);
    }
    public function payorMixExportPdf(Request $request)
    {
        return response('Export PDF Payor Mix (placeholder).', 200);
    }
    public function payorMixExportExcel(Request $request)
    {
        $start = $request->input('start_date');
        $end   = $request->input('end_date');

        $request2 = new Request(compact('start', 'end'));
        $viewData = $this->payorMix($request2)->getData();
        $mix = (array) ($viewData->mix ?? []);
        $total = (float) ($viewData->total ?? 0);
        $percentages = (array) ($viewData->percentages ?? []);

        $csv = "Payer,Amount,Percentage\n";
        foreach ($mix as $payer => $amt) {
            $csv .= sprintf("%s,%s,%s%%\n",
                str_replace([",", "\n"], [';', ' '], $payer),
                (int)$amt,
                $percentages[$payer] ?? 0
            );
        }
        $csv .= sprintf("Total,%s,100%%\n", (int)$total);

        $filename = 'payor_mix_' . now()->format('Ymd_His') . '.csv';
        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    // P&L per Cost Center
    public function pnlCostCenter(Request $request)
    {
        return view('pages.keuangan.laporan.pnl_cost_center');
    }
    public function pnlCostCenterExportPdf(Request $request)
    {
        return response('Export PDF P&L Cost Center (placeholder).', 200);
    }
    public function pnlCostCenterExportExcel(Request $request)
    {
        return response('Export Excel P&L Cost Center (placeholder).', 200);
    }

    // Cash Flow
    public function cashFlow(Request $request)
    {
        return view('pages.keuangan.laporan.cash_flow');
    }
    public function cashFlowExportPdf(Request $request)
    {
        return response('Export PDF Cash Flow (placeholder).', 200);
    }
    public function cashFlowExportExcel(Request $request)
    {
        return response('Export Excel Cash Flow (placeholder).', 200);
    }

    // Inventory
    public function inventory(Request $request)
    {
        return view('pages.keuangan.laporan.inventory');
    }
    public function inventoryExportPdf(Request $request)
    {
        return response('Export PDF Inventory (placeholder).', 200);
    }
    public function inventoryExportExcel(Request $request)
    {
        return response('Export Excel Inventory (placeholder).', 200);
    }
}
