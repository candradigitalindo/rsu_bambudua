<?php

namespace App\Repositories;

use App\Models\Category;
use App\Models\ProductApotek;
use Illuminate\Support\Str;

class ProductRepository
{
    // Ambil semua kategori
    public function getAllCategories()
    {
        return Category::all();
    }
    // Ambil semua produk dengan filter dan paginasi
    public function getAll($perPage = 20)
    {
        $query = ProductApotek::query();

        if (request('search')) {
            $query->where('name', 'like', '%' . request('search') . '%');
        }

        // Filter berdasarkan kategori
        if (request('category_id')) {
            $query->where('category_id', request('category_id'));
        }

        // Filter khusus dari dashboard
        if (request('filter') === 'habis') {
            // Filter produk yang stoknya habis (stok <= 0)
            $query->where('stok', '<=', 0);
        }

        if (request('filter') === 'kadaluarsa') {
            // Filter produk yang memiliki stok kadaluarsa
            $query->whereHas('apotekStok', function ($q) {
                $q->where('expired_at', '<', now())
                    ->where('status', 0);
            });
        }

        // Count ProductApotek yang akan expired 30 hari lagi berdasarkan data apotekStok
        $query->withCount([
            'apotekStok as expired_count' => function ($q) {
                $q->where('expired_at', '>=', now())
                    ->where('expired_at', '<=', now()->addDays(30))
                    ->where('status', 0);
            },
            'apotekStok as expired_past_count' => function ($q) {
                $q->where('expired_at', '<', now())
                    ->where('status', 0);
            }
        ]);

        // Urutkan produk yang ada expired di atas
        $query->orderByRaw('(expired_count > 0 OR expired_past_count > 0) DESC')->orderBy('created_at', 'desc');

        return $query->paginate($perPage);
    }

    // Ambil produk berdasarkan ID
    public function getById($id)
    {
        return ProductApotek::findOrFail($id);
    }

    // Tambah produk baru
    public function create(array $data)
    {
        // Generate kode produk baru berdasarkan 2 huruf pertama nama produk
        $productName = $data['name'] ?? '';
        $prefix = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $productName), 0, 2));

        // Cari produk terakhir dengan prefix yang sama untuk mendapatkan nomor urut berikutnya
        $lastProduct = ProductApotek::where('code', 'like', $prefix . '-%')
            ->orderBy('code', 'desc')
            ->first();

        $nextNumber = 1;
        if ($lastProduct) {
            $lastNumber = (int) substr($lastProduct->code, strrpos($lastProduct->code, '-') + 1);
            $nextNumber = $lastNumber + 1;
        }
        $data['code'] = $prefix . '-' . $nextNumber;

        // Pastikan harga hanya angka (integer)
        if (isset($data['harga'])) {
            $data['harga'] = (int) preg_replace('/\D/', '', $data['harga']);
        }
        return ProductApotek::create($data);
    }

    // Update produk
    public function update($id, array $data)
    {
        // Pastikan harga hanya angka (integer)
        if (isset($data['harga'])) {
            $data['harga'] = (int) preg_replace('/\D/', '', $data['harga']);
        }
        $product = ProductApotek::findOrFail($id);
        $product->update($data);
        return $product;
    }

    // Hapus produk
    public function delete($id)
    {
        $product = ProductApotek::findOrFail($id);
        return $product->delete();
    }
    // tambah stok produk
    public function addPostStock($id, $data)
    {
        $product = ProductApotek::findOrFail($id);

        if (isset($data['type']) && $data['type'] == 1) {
            // Stok keluar (stok biasa)
            $jumlahKeluar = (int) $data['stok'];
            $product->stok = max(0, $product->stok - $jumlahKeluar);

            // Ambil id ApotekStok expired terdekat yang status=0 dan belum expired
            $stokKeluarIds = \App\Models\ApotekStok::where('product_apotek_id', $product->id)
                ->where('status', 0)
                ->where(function ($q) {
                    $q->whereNull('expired_at')
                        ->orWhere('expired_at', '>=', now()->toDateString());
                })
                ->orderBy('expired_at', 'asc')
                ->limit($jumlahKeluar)
                ->pluck('id')
                ->toArray();

            // Update massal status
            if (!empty($stokKeluarIds)) {
                \App\Models\ApotekStok::whereIn('id', $stokKeluarIds)->update(['status' => 1]);
            }

            // Input histori stok keluar
            $product->historiApotek()->create([
                'product_apotek_id' => $product->id,
                'jumlah'            => $jumlahKeluar,
                'expired_at'        => $data['expired_at'] ?? null,
                'type'              => 1,
                'keterangan'        => $data['keterangan'] ?? null,
            ]);
        } elseif (isset($data['type']) && $data['type'] == 2) {
            // Stok keluar khusus expired
            $jumlahKeluar = (int) $data['stok'];
            $product->stok = max(0, $product->stok - $jumlahKeluar);

            // Ambil id ApotekStok yang status=0 dan sudah expired
            $stokKeluarIds = \App\Models\ApotekStok::where('product_apotek_id', $product->id)
                ->where('status', 0)
                ->where('expired_at', '<', now()->toDateString())
                ->orderBy('expired_at', 'asc')
                ->limit($jumlahKeluar)
                ->pluck('id')
                ->toArray();

            // Update massal status
            if (!empty($stokKeluarIds)) {
                \App\Models\ApotekStok::whereIn('id', $stokKeluarIds)->update(['status' => 1]);
            }

            // Input histori stok keluar expired
            $product->historiApotek()->create([
                'product_apotek_id' => $product->id,
                'jumlah'            => $jumlahKeluar,
                'expired_at'        => $data['expired_at'] ?? null,
                'type'              => 2,
                'keterangan'        => $data['keterangan'] ?? null,
            ]);
        } else {
            // Stok masuk
            $jumlahMasuk = (int) $data['stok'];
            $product->stok += $jumlahMasuk;

            // Insert massal ApotekStok
            $insertData = [];
            for ($i = 0; $i < $jumlahMasuk; $i++) {
                $insertData[] = [
                    'id'               => (string) Str::uuid(),
                    'product_apotek_id' => $product->id,
                    'expired_at'       => $data['expired_at'] ?? null,
                    'status'           => 0,
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ];
            }
            if (!empty($insertData)) {
                \App\Models\ApotekStok::insert($insertData);
            }

            // Input histori stok masuk
            $product->historiApotek()->create([
                'product_apotek_id' => $product->id,
                'jumlah'            => $jumlahMasuk,
                'expired_at'        => $data['expired_at'] ?? null,
                'type'              => 0,
                'keterangan'        => $data['keterangan'] ?? null,
            ]);
        }

        return $product->save();
    }
    // Ambil histori semua produk
    public function getHistori($perPage = 20)
    {
        $query = \App\Models\HistoriApotek::query();

        // Filter berdasarkan tanggal
        $startDate = request('start_date');
        $endDate = request('end_date');

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [\Carbon\Carbon::parse($startDate)->startOfDay(), \Carbon\Carbon::parse($endDate)->endOfDay()]);
        } else {
            // Default: tampilkan data 1 bulan terakhir
            $query->where('created_at', '>=', now()->subMonth());
        }

        // Filter pencarian
        if (request('search')) {
            $query->whereHas('productApotek', function ($q) {
                $q->where('name', 'like', '%' . request('search') . '%')
                    ->orWhere('code', 'like', '%' . request('search') . '%');
            });
        }
        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }
}
