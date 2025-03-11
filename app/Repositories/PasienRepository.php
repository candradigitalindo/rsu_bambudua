<?php

namespace App\Repositories;

use App\Models\Pasien;

class PasienRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function edit($id)
    {
        $pasien = Pasien::findOrFail($id);
    }
}
