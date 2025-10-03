<?php

namespace App\Repositories;

use App\Models\EquipmentMaintenance;
use App\Models\MedicalEquipment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class MedicalEquipmentRepository
{
    public function index(array $filters): LengthAwarePaginator
    {
        $q = MedicalEquipment::query();

        if (!empty($filters['search'])) {
            $s = $filters['search'];
            $q->where(function($qq) use ($s) {
                $qq->where('name', 'like', "%$s%")
                   ->orWhere('serial_number', 'like', "%$s%")
                   ->orWhere('asset_tag', 'like', "%$s%");
            });
        }
        if (!empty($filters['status'])) {
            $q->where('status', $filters['status']);
        }
        if (!empty($filters['location'])) {
            $q->where('location', 'like', "%{$filters['location']}%");
        }
        if (!empty($filters['vendor'])) {
            $q->where('vendor', 'like', "%{$filters['vendor']}%");
        }
        if (!empty($filters['calibration_due'])) {
            $q->whereNotNull('next_calibration_due')->where('next_calibration_due', '<=', now());
        }
        if (!empty($filters['start_date'])) {
            $q->whereDate('purchase_date', '>=', $filters['start_date']);
        }
        if (!empty($filters['end_date'])) {
            $q->whereDate('purchase_date', '<=', $filters['end_date']);
        }

        return $q->orderBy('updated_at', 'desc')->paginate(25)->withQueryString();
    }

    public function create(array $data): MedicalEquipment
    {
        return MedicalEquipment::create($data);
    }

    public function find(string $id): MedicalEquipment
    {
        return MedicalEquipment::findOrFail($id);
    }

    public function update(string $id, array $data): MedicalEquipment
    {
        $m = MedicalEquipment::findOrFail($id);
        $m->update($data);
        return $m;
    }

    public function destroy(string $id): void
    {
        MedicalEquipment::where('id', $id)->delete();
    }

    public function maintenanceList(string $equipmentId): Collection
    {
        return EquipmentMaintenance::where('equipment_id', $equipmentId)
            ->orderBy('date', 'desc')
            ->get();
    }

    public function maintenanceCreate(string $equipmentId, array $data): EquipmentMaintenance
    {
        $data['equipment_id'] = $equipmentId;
        return EquipmentMaintenance::create($data);
    }
}
