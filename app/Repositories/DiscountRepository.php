<?php

namespace App\Repositories;

class DiscountRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }
    // index
    public function index()
    {
        $discount = \App\Models\Discount::first();
        if (!$discount) {
            $discount = \App\Models\Discount::create([
                'diskon_tindakan' => 0,
                'diskon_resep' => 0,
            ]);
        }
        return $discount;
    }
    // update
    public function update($data)
    {
        $discount = \App\Models\Discount::first();
        if ($discount) {
            $discount->update($data);
        } else {
            $discount = \App\Models\Discount::create($data);
        }
        return $discount;
    }
}
