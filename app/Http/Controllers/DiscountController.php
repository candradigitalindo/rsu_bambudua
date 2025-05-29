<?php

namespace App\Http\Controllers;

use App\Repositories\DiscountRepository;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
    // dscountRepository
    public $discountRepository;
    /**
     * Create a new class instance.
     */
    public function __construct(DiscountRepository $discountRepository)
    {
        $this->discountRepository = $discountRepository;
    }
    // index
    public function index()
    {
        $discounts = $this->discountRepository->index();
        return view('pages.discount.index', compact('discounts'));
    }
    // update
    public function update(Request $request)
    {
        $data = $request->validate([
            'diskon_tindakan' => 'required|numeric',
            'diskon_resep' => 'required|numeric',
        ],
        [
            'diskon_tindakan.required' => 'Diskon tindakan harus diisi',
            'diskon_tindakan.numeric' => 'Diskon tindakan harus berupa angka',
            'diskon_resep.required' => 'Diskon resep harus diisi',
            'diskon_resep.numeric' => 'Diskon resep harus berupa angka',
        ]);
        $discount = $this->discountRepository->update($data);
        return redirect()->route('discounts.index')->with('success', 'Diskon berhasil diupdate');
    }
}
