<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class MedicalEquipmentController extends Controller
{
public function __construct(private \App\Repositories\MedicalEquipmentRepository $repo) {}

    public function index(Request $request)
    {
        $filters = [
            'search' => $request->input('search'),
            'status' => $request->input('status'),
            'location' => $request->input('location'),
            'vendor' => $request->input('vendor'),
            'calibration_due' => $request->boolean('calibration_due'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
        ];

        $equipments = $this->repo->index($filters);

        return view('pages.inventory.alat_medis.index', compact('filters', 'equipments'));
    }

    public function create()
    {
        return view('pages.inventory.alat_medis.create');
    }

    public function store(Request $request)
    {
        // Placeholder validation
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'asset_tag' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'vendor' => 'nullable|string|max:255',
            'status' => 'required|string|in:available,in_use,maintenance,repair,decommissioned',
            'purchase_date' => 'nullable|date',
            'warranty_expiry' => 'nullable|date',
            'last_calibration_date' => 'nullable|date',
            'next_calibration_due' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $data = $request->only([
            'name','category','serial_number','asset_tag','location','vendor','status','purchase_date','warranty_expiry','last_calibration_date','next_calibration_due','notes'
        ]);
        $data['is_active'] = true;
        $this->repo->create($data);

        return redirect()->route('inventory.equipment.index')
            ->with('success', 'Alat medis berhasil dibuat.');
    }

    public function show($id)
    {
        $equipment = $this->repo->find($id);
        $maintenance = $this->repo->maintenanceList($id);
        return view('pages.inventory.alat_medis.show', compact('equipment', 'maintenance'));
    }

    public function edit($id)
    {
        $equipment = $this->repo->find($id);
        return view('pages.inventory.alat_medis.edit', compact('equipment'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|string|in:available,in_use,maintenance,repair,decommissioned',
            'purchase_date' => 'nullable|date',
            'warranty_expiry' => 'nullable|date',
            'last_calibration_date' => 'nullable|date',
            'next_calibration_due' => 'nullable|date',
        ]);

        $data = $request->only([
            'name','category','serial_number','asset_tag','location','vendor','status','purchase_date','warranty_expiry','last_calibration_date','next_calibration_due','notes'
        ]);
        $this->repo->update($id, $data);
        return redirect()->route('inventory.equipment.show', $id)
            ->with('success', 'Alat medis berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $this->repo->destroy($id);
        return redirect()->route('inventory.equipment.index')
            ->with('success', 'Alat medis berhasil dihapus.');
    }

    public function maintenance(Request $request, $id)
    {
        // Legacy: tampilkan semua log dan form generik
        $equipment = $this->repo->find($id);
        $logs = $this->repo->maintenanceList($id);
        $categories = \App\Models\ExpenseCategory::where('is_active', true)->orderBy('name')->get();
        $costCenters = \App\Models\CostCenter::where('is_active', true)->orderBy('name')->get();
        $methods = \App\Models\PaymentMethod::where('active', true)->orderBy('name')->get();
        return view('pages.inventory.alat_medis.maintenance', compact('equipment', 'logs','categories','costCenters','methods'));
    }

    public function perawatan(Request $request, $id)
    {
        $equipment = $this->repo->find($id);
        $logs = \App\Models\EquipmentMaintenance::where('equipment_id', $id)
            ->whereIn('type', ['preventive','corrective'])
            ->orderBy('date','desc')
            ->get();
        $categories = \App\Models\ExpenseCategory::where('is_active', true)->orderBy('name')->get();
        $costCenters = \App\Models\CostCenter::where('is_active', true)->orderBy('name')->get();
        $methods = \App\Models\PaymentMethod::where('active', true)->orderBy('name')->get();
        return view('pages.inventory.alat_medis.perawatan', compact('equipment','logs','categories','costCenters','methods'));
    }

    public function kalibrasi(Request $request, $id)
    {
        $equipment = $this->repo->find($id);
        $logs = \App\Models\EquipmentMaintenance::where('equipment_id', $id)
            ->where('type', 'calibration')
            ->orderBy('date','desc')
            ->get();
        $categories = \App\Models\ExpenseCategory::where('is_active', true)->orderBy('name')->get();
        $costCenters = \App\Models\CostCenter::where('is_active', true)->orderBy('name')->get();
        $methods = \App\Models\PaymentMethod::where('active', true)->orderBy('name')->get();
        return view('pages.inventory.alat_medis.kalibrasi', compact('equipment','logs','categories','costCenters','methods'));
    }

    public function perawatanStore(Request $request, $id)
    {
        // Hanya perawatan (preventive/corrective)
        $request->merge(['type' => $request->input('type', 'preventive')]);
        return $this->handleMaintenanceSave($request, $id, ['preventive','corrective']);
    }

    public function kalibrasiStore(Request $request, $id)
    {
        // Kalibrasi selalu type=calibration
        $request->merge(['type' => 'calibration']);
        return $this->handleMaintenanceSave($request, $id, ['calibration']);
    }

    public function maintenanceStore(Request $request, $id)
    {
        $request->validate([
            'date' => 'required|date',
            'type' => 'required|string|in:preventive,corrective,calibration',
            'performed_by' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'cost' => 'nullable|numeric|min:0',
            'attachment' => 'nullable|file|max:10240|mimetypes:application/pdf,image/jpeg,image/png',
        ]);
        // Validasi tambahan saat posting ke keuangan
        if ($request->boolean('post_to_finance')) {
            $request->validate([
                'cost' => 'required|numeric|min:0.01',
                'finance_category_id' => 'required|uuid',
                'payment_method_code' => 'required|string',
            ]);
        }
        $data = $request->only(['date','type','performed_by','notes','cost']);
        // Handle attachment upload first
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $path = $file->store('maintenance', 'local');
            $data['attachment_path'] = $path;
            $data['attachment_name'] = $file->getClientOriginalName();
            $data['attachment_mime'] = $file->getClientMimeType();
            $data['attachment_size'] = $file->getSize();
        }
        $log = $this->repo->maintenanceCreate($id, $data);

        // Integrasi ke Keuangan (OperationalExpense) bila diminta
        if ($request->boolean('post_to_finance') && !empty($data['cost']) && (float)$data['cost'] > 0) {
            $equipment = $this->repo->find($id);
            $catId = $request->input('finance_category_id');
            $ccId = $request->input('finance_cost_center_id');
            $cat = $catId ? \App\Models\ExpenseCategory::find($catId) : null;
            $cc  = $ccId ? \App\Models\CostCenter::find($ccId) : null;
            $expense = \App\Models\OperationalExpense::create([
                'description' => 'Perawatan Alat Medis: ' . ($equipment->name ?? 'Perangkat') . ' (' . $data['type'] . ')',
                'category' => $cat->name ?? 'Perawatan Alat Medis',
                'expense_category_id' => $cat?->id,
                'cost_center' => $cc->name ?? null,
                'cost_center_id' => $cc?->id,
'amount' => $data['cost'],
                'expense_date' => $data['date'],
                'payment_method_code' => $request->input('payment_method_code'),
                'payment_method_name' => optional(\App\Models\PaymentMethod::where('code', $request->input('payment_method_code'))->first())->name,
                'payment_reference' => $request->input('payment_reference'),
            ]);
            $log->operational_expense_id = $expense->id;
            $log->save();
        }

        return redirect()->back()->with('success', 'Log tersimpan.');
    }

    private function handleMaintenanceSave(Request $request, $id, array $allowedTypes)
    {
        $request->validate([
            'date' => 'required|date',
            'type' => 'required|string|in:' . implode(',', $allowedTypes),
            'performed_by' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'cost' => 'nullable|numeric|min:0',
            'attachment' => 'nullable|file|max:10240|mimetypes:application/pdf,image/jpeg,image/png',
        ]);
        if ($request->boolean('post_to_finance')) {
            $request->validate([
                'cost' => 'required|numeric|min:0.01',
                'finance_category_id' => 'required|uuid',
                'payment_method_code' => 'required|string',
            ]);
        }
        $data = $request->only(['date','type','performed_by','notes','cost']);
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $path = $file->store('maintenance', 'local');
            $data['attachment_path'] = $path;
            $data['attachment_name'] = $file->getClientOriginalName();
            $data['attachment_mime'] = $file->getClientMimeType();
            $data['attachment_size'] = $file->getSize();
        }
        $log = $this->repo->maintenanceCreate($id, $data);

        if ($request->boolean('post_to_finance') && !empty($data['cost']) && (float)$data['cost'] > 0) {
            $equipment = $this->repo->find($id);
            $catId = $request->input('finance_category_id');
            $ccId = $request->input('finance_cost_center_id');
            $cat = $catId ? \App\Models\ExpenseCategory::find($catId) : null;
            $cc  = $ccId ? \App\Models\CostCenter::find($ccId) : null;
            $expense = \App\Models\OperationalExpense::create([
                'description' => 'Perawatan Alat Medis: ' . ($equipment->name ?? 'Perangkat') . ' (' . $data['type'] . ')',
                'category' => $cat->name ?? 'Perawatan Alat Medis',
                'expense_category_id' => $cat?->id,
                'cost_center' => $cc->name ?? null,
                'cost_center_id' => $cc?->id,
                'amount' => $data['cost'],
                'expense_date' => $data['date'],
                'payment_method_code' => $request->input('payment_method_code'),
                'payment_method_name' => optional(\App\Models\PaymentMethod::where('code', $request->input('payment_method_code'))->first())->name,
                'payment_reference' => $request->input('payment_reference'),
            ]);
            $log->operational_expense_id = $expense->id;
            $log->save();
        }

        // Redirect sesuai halaman asal
        if (in_array('calibration', $allowedTypes)) {
            return redirect()->route('inventory.equipment.kalibrasi', $id)->with('success', 'Log kalibrasi tersimpan.');
        }
        return redirect()->route('inventory.equipment.perawatan', $id)->with('success', 'Log perawatan tersimpan.');
    }

    public function maintenanceDownload($logId)
    {
        $log = \App\Models\EquipmentMaintenance::findOrFail($logId);
        if (!$log->attachment_path) {
            abort(404);
        }
        $filePath = storage_path('app/' . $log->attachment_path);
        return response()->download($filePath, $log->attachment_name ?? basename($filePath));
    }

    public function maintenanceEdit($logId)
    {
        $log = \App\Models\EquipmentMaintenance::findOrFail($logId);
        $equipment = $this->repo->find($log->equipment_id);
        $categories = \App\Models\ExpenseCategory::where('is_active', true)->orderBy('name')->get();
        $costCenters = \App\Models\CostCenter::where('is_active', true)->orderBy('name')->get();
        $methods = \App\Models\PaymentMethod::where('active', true)->orderBy('name')->get();
        $expense = $log->operational_expense_id ? \App\Models\OperationalExpense::find($log->operational_expense_id) : null;
        return view('pages.inventory.alat_medis.maintenance_edit', compact('log','equipment','categories','costCenters','methods','expense'));
    }

    public function maintenanceUpdate(Request $request, $logId)
    {
        $log = \App\Models\EquipmentMaintenance::findOrFail($logId);
        // allowed types based on current log type
        $allowed = $log->type === 'calibration' ? ['calibration'] : ['preventive','corrective'];
        $request->merge(['type' => $log->type === 'calibration' ? 'calibration' : $request->input('type', $log->type)]);
        // reuse validation
        $request->validate([
            'date' => 'required|date',
            'type' => 'required|string|in:' . implode(',', $allowed),
            'performed_by' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'cost' => 'nullable|numeric|min:0',
            'attachment' => 'nullable|file|max:10240|mimetypes:application/pdf,image/jpeg,image/png',
        ]);
        if ($request->boolean('post_to_finance')) {
            $request->validate([
                'cost' => 'required|numeric|min:0.01',
                'finance_category_id' => 'required|uuid',
                'payment_method_code' => 'required|string',
            ]);
        }

        $update = $request->only(['date','type','performed_by','notes','cost']);
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $path = $file->store('maintenance', 'local');
            $update['attachment_path'] = $path;
            $update['attachment_name'] = $file->getClientOriginalName();
            $update['attachment_mime'] = $file->getClientMimeType();
            $update['attachment_size'] = $file->getSize();
        }
        $log->update($update);

        if ($request->boolean('post_to_finance') && !empty($update['cost']) && (float)$update['cost'] > 0) {
            $equipment = $this->repo->find($log->equipment_id);
            $catId = $request->input('finance_category_id');
            $ccId = $request->input('finance_cost_center_id');
            $cat = $catId ? \App\Models\ExpenseCategory::find($catId) : null;
            $cc  = $ccId ? \App\Models\CostCenter::find($ccId) : null;
            if ($log->operational_expense_id) {
                // Update existing expense
                \App\Models\OperationalExpense::where('id', $log->operational_expense_id)->update([
                    'description' => 'Perawatan Alat Medis: ' . ($equipment->name ?? 'Perangkat') . ' (' . $update['type'] . ')',
                    'category' => $cat->name ?? 'Perawatan Alat Medis',
                    'expense_category_id' => $cat?->id,
                    'cost_center' => $cc->name ?? null,
                    'cost_center_id' => $cc?->id,
                    'amount' => $update['cost'],
                    'expense_date' => $update['date'],
                    'payment_method_code' => $request->input('payment_method_code'),
                    'payment_method_name' => optional(\App\Models\PaymentMethod::where('code', $request->input('payment_method_code'))->first())->name,
                    'payment_reference' => $request->input('payment_reference'),
                ]);
            } else {
                $expense = \App\Models\OperationalExpense::create([
                    'description' => 'Perawatan Alat Medis: ' . ($equipment->name ?? 'Perangkat') . ' (' . $update['type'] . ')',
                    'category' => $cat->name ?? 'Perawatan Alat Medis',
                    'expense_category_id' => $cat?->id,
                    'cost_center' => $cc->name ?? null,
                    'cost_center_id' => $cc?->id,
                    'amount' => $update['cost'],
                    'expense_date' => $update['date'],
                    'payment_method_code' => $request->input('payment_method_code'),
                    'payment_method_name' => optional(\App\Models\PaymentMethod::where('code', $request->input('payment_method_code'))->first())->name,
                    'payment_reference' => $request->input('payment_reference'),
                ]);
                $log->operational_expense_id = $expense->id;
                $log->save();
            }
        }

        // Redirect to appropriate page
        if ($log->type === 'calibration') {
            return redirect()->route('inventory.equipment.kalibrasi', $log->equipment_id)->with('success', 'Log kalibrasi diperbarui.');
        }
        return redirect()->route('inventory.equipment.perawatan', $log->equipment_id)->with('success', 'Log perawatan diperbarui.');
    }

    public function maintenanceDestroy($logId)
    {
        $log = \App\Models\EquipmentMaintenance::findOrFail($logId);
        $equipmentId = $log->equipment_id;
        $type = $log->type;
        // Hapus file lampiran jika ada
        if ($log->attachment_path) {
            @unlink(storage_path('app/' . $log->attachment_path));
        }
        $log->delete();
        if ($type === 'calibration') {
            return redirect()->route('inventory.equipment.kalibrasi', $equipmentId)->with('success', 'Log kalibrasi dihapus.');
        }
        return redirect()->route('inventory.equipment.perawatan', $equipmentId)->with('success', 'Log perawatan dihapus.');
    }

    public function exportPdf(Request $request)
    {
        return response('Export PDF Alat Medis (placeholder).', 200);
    }

    public function exportExcel(Request $request)
    {
        // Simple CSV placeholder
        $csv = "Name,Category,Status\n";
        $filename = 'alat_medis_' . now()->format('Ymd_His') . '.csv';
        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
