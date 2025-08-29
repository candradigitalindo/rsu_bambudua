<?php

namespace App\Repositories;

use App\Models\Clinic;

class ClinicRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }
    public function all()
    {
        return Clinic::all();
    }
    // Get a single clinic by ID
    public function find(string $id): ?Clinic
    {
        return Clinic::find($id);
    }
    // Create
    public function create(array $data): Clinic
    {
        return Clinic::create($data);
    }
    // Update
    public function update(string $id, array $data): ?Clinic
    {
        $clinic = $this->find($id);
        if ($clinic) {
            $clinic->update($data);
            return $clinic;
        }
        return null;
    }
    // Delete
    public function delete(string $id): bool
    {
        $clinic = $this->find($id);
        if ($clinic) {
            $clinic->delete();
            return true;
        }
        return false;
    }
}
