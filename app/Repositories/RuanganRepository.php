<?php

namespace App\Repositories;

use App\Models\CategoryRuangan;
use App\Models\Ruangan;

class RuanganRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }
    public function index()
    {
        $ruangans = Ruangan::with('category')->orderBy('no_kamar', 'asc')->paginate(10);
        return $ruangans;
    }
    public function show($id)
    {
        return Ruangan::findOrFail($id);
    }

    public function create(array $data)
    {
        return Ruangan::create($data);
    }
    public function update($id, array $data)
    {
        $ruangan = Ruangan::findOrFail($id);
        $ruangan->update($data);
        return $ruangan;
    }
    public function destroy($id)
    {
        $ruangan = Ruangan::findOrFail($id);
        $ruangan->delete();
        return $ruangan;
    }

    public function AllCategory()
    {
        return CategoryRuangan::orderBy('created_at', 'desc')->get();
    }

    /**
     * Get bed availability information for all room types
     * Returns array with room types and available beds count
     */
    public function getBedAvailability()
    {
        $availabilityData = [];
        
        // Get all categories
        $categories = CategoryRuangan::all();
        
        foreach ($categories as $category) {
            $categoryData = [
                'category_id' => $category->id,
                'category_name' => $category->name,
                'description' => $category->description,
                'classes' => []
            ];
            
            // Get room classes for this category
            $classes = Ruangan::where('category_id', $category->id)
                ->whereNotNull('class')
                ->distinct()
                ->pluck('class')
                ->filter();
                
            foreach ($classes as $class) {
                $classData = $this->getClassBedAvailability($category->id, $class);
                $categoryData['classes'][$class] = $classData;
            }
            
            // Also get rooms without specific class
            $noClassData = $this->getClassBedAvailability($category->id, null);
            if ($noClassData['total_beds'] > 0) {
                $categoryData['classes']['Umum'] = $noClassData;
            }
            
            $availabilityData[] = $categoryData;
        }
        
        return $availabilityData;
    }
    
    /**
     * Get bed availability for specific category and class
     */
    private function getClassBedAvailability($categoryId, $class = null)
    {
        $query = Ruangan::where('category_id', $categoryId);
        
        if ($class) {
            $query->where('class', $class);
        } else {
            $query->whereNull('class');
        }
        
        $rooms = $query->get();
        
        $totalBeds = $rooms->sum('capacity') ?? 0;
        $totalRooms = $rooms->count();
        
        // Count occupied beds from active inpatient admissions only
        $occupiedBeds = 0;
        foreach ($rooms as $room) {
            $occupiedInRoom = \App\Models\InpatientAdmission::where('ruangan_id', $room->id)
                ->whereNull('discharge_date')  // Only count patients who haven't been discharged
                ->whereHas('encounter', function($query) {
                    $query->where('type', 2)      // Type 2 = Rawat Inap
                          ->orWhere('type', 3);    // Type 3 = IGD/Rawat Darurat
                })
                ->count();
            $occupiedBeds += $occupiedInRoom;
        }
        
        $availableBeds = max(0, $totalBeds - $occupiedBeds);
        
        return [
            'total_rooms' => $totalRooms,
            'total_beds' => $totalBeds,
            'occupied_beds' => $occupiedBeds,
            'available_beds' => $availableBeds,
            'occupancy_rate' => $totalBeds > 0 ? round(($occupiedBeds / $totalBeds) * 100, 1) : 0,
            'rooms' => $rooms->map(function($room) {
                $occupiedInRoom = \App\Models\InpatientAdmission::where('ruangan_id', $room->id)
                    ->whereNull('discharge_date')  // Only count active patients who haven't been discharged
                    ->whereHas('encounter', function($query) {
                        $query->where('type', 2)      // Type 2 = Rawat Inap
                              ->orWhere('type', 3);    // Type 3 = IGD/Rawat Darurat
                    })
                    ->count();
                    
                return [
                    'room_number' => $room->no_kamar,
                    'capacity' => $room->capacity ?? 0,
                    'occupied' => $occupiedInRoom,
                    'available' => max(0, ($room->capacity ?? 0) - $occupiedInRoom),
                    'price' => $room->harga,
                    'description' => $room->description
                ];
            })
        ];
    }
    
    /**
     * Get summary of bed availability across all categories
     */
    public function getBedAvailabilitySummary()
    {
        $totalBeds = Ruangan::sum('capacity') ?? 0;
        $totalRooms = Ruangan::count();
        
        $occupiedBeds = \App\Models\InpatientAdmission::whereNull('discharge_date')  // Only count active patients who haven't been discharged
            ->whereHas('encounter', function($query) {
                $query->where('type', 2)      // Type 2 = Rawat Inap
                      ->orWhere('type', 3);    // Type 3 = IGD/Rawat Darurat
            })
            ->count();
            
        $availableBeds = max(0, $totalBeds - $occupiedBeds);
        
        return [
            'total_rooms' => $totalRooms,
            'total_beds' => $totalBeds,
            'occupied_beds' => $occupiedBeds,
            'available_beds' => $availableBeds,
            'occupancy_rate' => $totalBeds > 0 ? round(($occupiedBeds / $totalBeds) * 100, 1) : 0
        ];
    }
    public function showCategory($id)
    {
        return CategoryRuangan::findOrFail($id);
    }
    public function createCategory(array $data)
    {
        return CategoryRuangan::create($data);
    }
    public function updateCategory($id, array $data)
    {
        $category = CategoryRuangan::findOrFail($id);
        $category->update($data);
        return $category;
    }
    public function destroyCategory($id)
    {
        $category = CategoryRuangan::findOrFail($id);
        $category->delete();
        return $category;
    }
}
