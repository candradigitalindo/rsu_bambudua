<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\ProductRepository;
use App\Models\Unit;

class ProductController extends Controller
{
    protected $productRepo;

    public function __construct(ProductRepository $productRepo)
    {
        $this->productRepo = $productRepo;
    }

    // List & search
    public function index(Request $request)
    {
        $categories = $this->productRepo->getAllCategories();
        $products = $this->productRepo->getAll();
        return view('pages.products.index', compact('products', 'categories'));
    }

    // Show create form
    public function create()
    {
        $categories = $this->productRepo->getAllCategories();
        $units = Unit::orderBy('name')->get();
        return view('pages.products.create', compact('categories', 'units'));
    }

    // Store new product
    public function store(Request $request)
    {
        $request->validate(
            [
                'name'        => 'required|string|max:255|unique:product_apoteks',
                'category_id' => 'required|string',
                'satuan'       => 'required|string',
                'harga'       => 'required|string',
                'type'        => 'required|integer',
                'expired'     => 'required|integer',
                'warning_stok' => 'required|integer',
                'expired_warning' => 'required_if:expired,0|nullable|integer',
                // tambahkan validasi lain sesuai kebutuhan
            ],
            [
                'name.required' => 'Nama produk harus diisi.',
                'name.unique' => 'Nama produk sudah ada.',
                'category_id.required' => 'Kategori produk harus dipilih.',
                'satuan.required' => 'Satuan produk harus diisi.',
                'harga.required' => 'Harga produk harus diisi.',
                'type.required' => 'Tipe produk harus dipilih.',
                'expired.required' => 'Expired produk harus dipilih.',
                'warning_stok.required' => 'Warning stok produk harus diisi.',
                'expired_warning.required_if' => 'Warning expired produk harus diisi jika produk memiliki tanggal expired.',
            ]
        );
        $this->productRepo->create($request->all());
        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    // Show edit form
    public function edit($id)
    {
        $product = $this->productRepo->getById($id);
        $categories = $this->productRepo->getAllCategories();
        $units = Unit::orderBy('name')->get();
        return view('pages.products.edit', compact('product', 'categories', 'units'));
    }

    // Update product
    public function update(Request $request, $id)
    {
        $request->validate(
            [
                'name'        => 'required|string|max:255|unique:product_apoteks,name,' . $id,
                'category_id' => 'required|string',
                'satuan'       => 'required|string',
                'harga'       => 'required|string',
                'type'        => 'required|integer',
                'expired'     => 'required|integer',
                'warning_stok' => 'required|integer',
                'expired_warning' => 'required_if:expired,0|nullable|integer',
                // tambahkan validasi lain sesuai kebutuhan
            ],
            [
                'name.required' => 'Nama produk harus diisi.',
                'name.unique' => 'Nama produk sudah ada.',
                'category_id.required' => 'Kategori produk harus dipilih.',
                'satuan.required' => 'Satuan produk harus diisi.',
                'harga.required' => 'Harga produk harus diisi.',
                'type.required' => 'Tipe produk harus dipilih.',
                'expired.required' => 'Expired produk harus dipilih.',
                'warning_stok.required' => 'Warning stok produk harus diisi.',
                'expired_warning.required_if' => 'Warning expired produk harus diisi jika produk memiliki tanggal expired.',
            ]
        );
        $this->productRepo->update($id, $request->all());
        return redirect()->route('products.index')->with('success', 'Produk berhasil diupdate.');
    }

    // Delete product
    public function destroy($id)
    {
        $this->productRepo->delete($id);
        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus.');
    }
    // Show add stock form
    public function addStock($id)
    {
        $product = $this->productRepo->getById($id);
        return view('pages.products.stok', compact('product'));
    }
    // Store stock
    public function storeStock(Request $request, $id)
    {
        $request->validate(
            [
                'type' => 'required|integer',
                'stok' => 'required|integer|min:1',
                'keterangan' => 'nullable|string|max:255',
                'expired_at' => 'nullable|date',
            ],
            [
                'type.required' => 'Tipe harus dipilih.',
                'type.integer' => 'Tipe harus berupa angka.',
                'stok.integer' => 'Stok harus berupa angka.',
                'stok.min' => 'Stok tidak valid.',
                'keterangan.string' => 'Keterangan harus berupa teks.',
                'keterangan.max' => 'Keterangan tidak boleh lebih dari 255 karakter.',
                'expired_at.date' => 'Tanggal expired harus berupa tanggal yang valid.',
            ]
        );

        $data = $request->all();

        // Jika ada expired_at, tambahkan ke data yang akan diproses repository
        if ($request->filled('expired_at')) {
            $data['expired_at'] = $request->input('expired_at');
        }

        $this->productRepo->addPostStock($id, $data);
        return redirect()->route('products.index')->with('success', 'Stok berhasil ditambahkan.');
    }
    // get Histori
    public function getHistori()
    {
        $historis = $this->productRepo->getHistori();
        return view('pages.products.histori', compact('historis'));
    }
}
