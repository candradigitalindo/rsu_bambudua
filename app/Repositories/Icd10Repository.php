<?php

namespace App\Repositories;

use App\Models\Icd10;

class Icd10Repository
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
        // Jia ada pencarian berdasarkan code atau description
        if (request()->has('search')) {
            $search = request()->input('search');
            return Icd10::where('code', 'like', "%$search%")
                ->orWhere('description', 'like', "%$search%")
                ->paginate(10);
        }
        return Icd10::paginate(50);
    }

    public function find($id)
    {
        return Icd10::find($id);
    }

    public function create(array $data)
    {
        return Icd10::create($data);
    }

    public function update($id, array $data)
    {
        $icd10 = Icd10::find($id);
        if ($icd10) {
            $icd10->update($data);
            return $icd10;
        }
        return null;
    }

    public function delete($id)
    {
        $icd10 = Icd10::find($id);
        if ($icd10) {
            $icd10->delete();
            return true;
        }
        return false;
    }
}
