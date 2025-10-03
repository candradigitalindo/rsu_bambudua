<?php

namespace App\Http\Controllers;

use App\Repositories\BahanRepository;
use Illuminate\Http\Request;

class BahanController extends Controller
{
    public $bahanRepository;
    public function __construct(BahanRepository $bahanRepository)
    {
        $this->bahanRepository = $bahanRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bahans = $this->bahanRepository->index(); // Assuming a method to get all resources
        return view('pages.bahan.index', compact('bahans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Add logic to show the creation form
        return view('pages.bahan.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if ($request->is_expired == 1) {
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'is_expired' => 'string|required',
                'is_active' => 'string|required',
                'reminder' => 'required|integer|min:1',
            ],
            [
                'name.required' => 'Kolom Nama tidak boleh kosong',
                'name.string' => 'Kolom Nama Bahan harus berupa string',
                'name.max' => 'Kolom Nama Bahan tidak boleh lebih dari 255 karakter',
                'description.string' => 'Kolom Deskripsi harus berupa string',
                'description.max' => 'Kolom Deskripsi tidak boleh lebih dari 255 karakter',
                'is_expired.boolean' => 'Kolom Expired harus berupa boolean',
                'is_active.boolean' => 'Kolom Status harus berupa boolean',
                'is_expired.required' => 'Kolom Expired tidak boleh kosong',
                'is_active.required' => 'Kolom Status tidak boleh kosong',
                'reminder.required' => 'Kolom Pengingat tidak boleh kosong',
                'reminder.integer' => 'Kolom Pengingat harus berupa angka',
                'reminder.min' => 'Kolom Pengingat harus lebih dari 0',
            ]);
        }else {
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'is_expired' => 'string|required',
                'is_active' => 'string|required',
                'reminder' => 'nullable|integer|min:1',
            ],
            [
                'name.required' => 'Kolom Nama tidak boleh kosong',
                'name.string' => 'Kolom Nama Bahan harus berupa string',
                'name.max' => 'Kolom Nama Bahan tidak boleh lebih dari 255 karakter',
                'description.string' => 'Kolom Deskripsi harus berupa string',
                'description.max' => 'Kolom Deskripsi tidak boleh lebih dari 255 karakter',
                'is_expired.boolean' => 'Kolom Expired harus berupa boolean',
                'is_active.boolean' => 'Kolom Status harus berupa boolean',
                'is_expired.required' => 'Kolom Expired tidak boleh kosong',
                'is_active.required' => 'Kolom Status tidak boleh kosong',
                'reminder.required' => 'Kolom Pengingat tidak boleh kosong',
                'reminder.integer' => 'Kolom Pengingat harus berupa angka',
                'reminder.min' => 'Kolom Pengingat harus lebih dari 0',
            ]);
        }
        $data = [
            'name' => $request->name,
            'is_expired' => (int) $request->is_expired,
            'description' => $request->description,
            'is_active' => (int) $request->is_active,
            'warning' => $request->is_expired == 1 ? (int) $request->reminder : 0,
        ];

        $bahan = $this->bahanRepository->create($data); // Use $data instead of $request->all()
        return redirect()->route('bahans.index')->with('success', 'Bahan ' . $bahan->name . ' berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $bahan = $this->bahanRepository->show($id);
        return view('pages.bahan.edit', compact('bahan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if ($request->is_expired == 1) {
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'is_expired' => 'string|required',
                'is_active' => 'string|required',
                'reminder' => 'required|integer|min:1',
            ],
            [
                'name.required' => 'Kolom Nama tidak boleh kosong',
                'name.string' => 'Kolom Nama Bahan harus berupa string',
                'name.max' => 'Kolom Nama Bahan tidak boleh lebih dari 255 karakter',
                'description.string' => 'Kolom Deskripsi harus berupa string',
                'description.max' => 'Kolom Deskripsi tidak boleh lebih dari 255 karakter',
                'is_expired.boolean' => 'Kolom Expired harus berupa boolean',
                'is_active.boolean' => 'Kolom Status harus berupa boolean',
                'is_expired.required' => 'Kolom Expired tidak boleh kosong',
                'is_active.required' => 'Kolom Status tidak boleh kosong',
                'reminder.required' => 'Kolom Pengingat tidak boleh kosong',
                'reminder.integer' => 'Kolom Pengingat harus berupa angka',
                'reminder.min' => 'Kolom Pengingat harus lebih dari 0',
            ]);
        }else {
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'is_expired' => 'string|required',
                'is_active' => 'string|required',
                'reminder' => 'nullable|integer|min:1',
            ],
            [
                'name.required' => 'Kolom Nama tidak boleh kosong',
                'name.string' => 'Kolom Nama Bahan harus berupa string',
                'name.max' => 'Kolom Nama Bahan tidak boleh lebih dari 255 karakter',
                'description.string' => 'Kolom Deskripsi harus berupa string',
                'description.max' => 'Kolom Deskripsi tidak boleh lebih dari 255 karakter',
                'is_expired.boolean' => 'Kolom Expired harus berupa boolean',
                'is_active.boolean' => 'Kolom Status harus berupa boolean',
                'is_expired.required' => 'Kolom Expired tidak boleh kosong',
                'is_active.required' => 'Kolom Status tidak boleh kosong',
                'reminder.required' => 'Kolom Pengingat tidak boleh kosong',
                'reminder.integer' => 'Kolom Pengingat harus berupa angka',
                'reminder.min' => 'Kolom Pengingat harus lebih dari 0',
            ]);
        }
        $data = [
            'name' => $request->name,
            'is_expired' => (int) $request->is_expired,
            'description' => $request->description,
            'is_active' => (int) $request->is_active,
            'warning' => $request->is_expired == 1 ? (int) $request->reminder : 0,
        ];

        $bahan = $this->bahanRepository->update($id, $data);
        return redirect()->route('bahans.index')->with('success', 'Bahan '.$bahan->name.' berhasil diubah.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $bahan = $this->bahanRepository->destroy($id);
        if (!$bahan) {
            return redirect()->route('bahans.index')->with('error', 'Bahan tidak dapat dihapus karena digunakan oleh Tindakan.');
        }
        return redirect()->route('bahans.index')->with('success', 'Bahan '.$bahan->name.' berhasil dihapus.');
    }
    public function getBahan($id)
    {
        $bahan = $this->bahanRepository->getBahan($id);
        return view('pages.bahan.stokMasuk', compact('bahan'));
    }
    public function stokBahan(Request $request, $id)
    {
        $bahan = $this->bahanRepository->getBahan($id);
        if ($bahan->is_expired == 1) {
            $request->validate([
                'quantity' => 'required|integer|min:1',
                'expired_at' => 'required|date',
                'description' => 'nullable|string|max:255',
            ],
            [
                'quantity.required' => 'Kolom Jumlah tidak boleh kosong',
                'quantity.integer' => 'Kolom Jumlah harus berupa angka',
                'quantity.min' => 'Kolom Jumlah harus lebih dari 0',
                'expired_at.required' => 'Kolom Expired tidak boleh kosong',
                'expired_at.date' => 'Kolom Expired harus berupa tanggal yang valid',
                'description.string' => 'Kolom Deskripsi harus berupa string',
                'description.max' => 'Kolom Deskripsi tidak boleh lebih dari 255 karakter',
            ]);
        }else {
            $request->validate([
                'quantity' => 'required|integer|min:1',
                'expired_at' => 'nullable|date',
                'description' => 'nullable|string|max:255',
            ],
            [
                'quantity.required' => 'Kolom Jumlah tidak boleh kosong',
                'quantity.integer' => 'Kolom Jumlah harus berupa angka',
                'quantity.min' => 'Kolom Jumlah harus lebih dari 0',
                'expired_at.date' => 'Kolom Expired harus berupa tanggal yang valid',
                'description.string' => 'Kolom Deskripsi harus berupa string',
                'description.max' => 'Kolom Deskripsi tidak boleh lebih dari 255 karakter',
            ]);
        }
        $data = [
            'quantity' => $request->quantity,
            'expired_at' => $bahan->is_expired == 1 ? $request->expired_at : null,
            'description' => $request->description,
        ];

        $bahan = $this->bahanRepository->stokBahan($data, $id);
        return redirect()->route('bahans.index')->with('success', 'Stok Bahan '.$bahan->name.' berhasil ditambahkan.');
    }
    public function getBahanKeluar($id)
    {
        $bahan = $this->bahanRepository->getBahan($id);
        return view('pages.bahan.stokKeluar', compact('bahan'));
    }
    public function stokKeluar(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'created_at' => 'nullable|date',
            'description' => 'nullable|string|max:255',
        ],
        [
            'quantity.required' => 'Kolom Jumlah tidak boleh kosong',
            'quantity.integer' => 'Kolom Jumlah harus berupa angka',
            'quantity.min' => 'Kolom Jumlah harus lebih dari 0',
            'created_at.date' => 'Kolom Tanggal harus berupa tanggal yang valid',
            'description.string' => 'Kolom Deskripsi harus berupa string',
            'description.max' => 'Kolom Deskripsi tidak boleh lebih dari 255 karakter',
        ]);
        $data = [
            'quantity' => $request->quantity,
            'created_at' => $request->created_at == null ? null : $request->created_at,
            'description' => $request->description,
            'status' => 'keluar',
        ];

        $bahan = $this->bahanRepository->stokKeluar($id, $data);
        return redirect()->route('bahans.index')->with('success', 'Stok Bahan '.$bahan->name.' berhasil dikurangi.');
    }
    public function getAllHistori()
    {
        $bahan = $this->bahanRepository->getAllHistori();
        return view('pages.bahan.histori', compact('bahan'));
    }

    // AJAX: daftar tindakan yang menggunakan bahan
    public function tindakanJson($id)
    {
        $bahan = \App\Models\Bahan::with(['tindakan' => function ($q) {
            $q->select('tindakans.id', 'name', 'harga');
        }])->findOrFail($id);

        $rows = $bahan->tindakan->map(function ($t) {
            $harga = $t->harga ?? 0;
            return [
                'id' => $t->id,
                'name' => $t->name,
                'harga' => (float) $harga,
                'harga_formatted' => 'Rp ' . number_format($harga, 0, ',', '.'),
            ];
        });

        return response()->json([
            'bahan' => [
                'id' => $bahan->id,
                'name' => $bahan->name,
            ],
            'tindakan' => $rows,
        ]);
    }

    // get RequestBahan
    public function getRequestBahan()
    {
        $requestBahans = $this->bahanRepository->getRequestBahan();
        return view('pages.bahan.permintaan', compact('requestBahans'));
    }
    // bahan diserahkan
    public function bahanDiserahkan(Request $request, $id)
    {
        $request->validate([
            'kepada' => 'required|string',
            'jumlah' => 'required|string|max:255',

        ],
        [
            'kepada.required' => 'Kolom Diserahkan tidak boleh kosong',
            'kepada.string' => 'Kolom Diserahkan harus berupa string',
            'jumlah.required' => 'Kolom Jumlah tidak boleh kosong',
            'jumlah.string' => 'Kolom Jumlah harus berupa string',
            'jumlah.max' => 'Kolom Jumlah tidak boleh lebih dari 255 karakter',
        ]);
        $data = [
            'diserahkan' => $request->kepada,
            'qty' => $request->jumlah,
            'status' => 1,
        ];

        $bahan = $this->bahanRepository->bahanDiserahkan($id, $data);
        return redirect()->route('bahan.getRequestBahan')->with('success', 'Permintaan Bahan '.$bahan->nama_bahan.' berhasil diserahkan.');
    }

}
