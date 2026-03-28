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
            'diskon_tindakan' => 'required|numeric|min:0',
            'diskon_resep' => 'required|numeric|min:0',
            'diskon_tindakan_nominal' => 'required|numeric|min:0',
            'diskon_resep_nominal' => 'required|numeric|min:0',
        ],
        [
            'diskon_tindakan.required' => 'Diskon tindakan harus diisi',
            'diskon_tindakan.numeric' => 'Diskon tindakan harus berupa angka',
            'diskon_tindakan.min' => 'Diskon tindakan minimal 0',
            'diskon_resep.required' => 'Diskon resep harus diisi',
            'diskon_resep.numeric' => 'Diskon resep harus berupa angka',
            'diskon_resep.min' => 'Diskon resep minimal 0',
            'diskon_tindakan_nominal.required' => 'Diskon tindakan nominal harus diisi',
            'diskon_tindakan_nominal.numeric' => 'Diskon tindakan nominal harus berupa angka',
            'diskon_tindakan_nominal.min' => 'Diskon tindakan nominal minimal 0',
            'diskon_resep_nominal.required' => 'Diskon resep nominal harus diisi',
            'diskon_resep_nominal.numeric' => 'Diskon resep nominal harus berupa angka',
            'diskon_resep_nominal.min' => 'Diskon resep nominal minimal 0',
        ]);
        $discount = $this->discountRepository->update($data);
        return redirect()->route('discounts.index')->with('success', 'Diskon berhasil diupdate');
    }
}
