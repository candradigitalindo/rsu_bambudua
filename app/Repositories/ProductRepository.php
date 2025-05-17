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

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    // Ambil produk berdasarkan ID
    public function getById($id)
    {
        return ProductApotek::findOrFail($id);
    }

    // Tambah produk baru
    public function create(array $data)
    {
        // Generate kode otomatis unik
        $maxTry = 100;
        $try = 0;
        do {
            $last = ProductApotek::orderBy('id', 'desc')->first();
            $nextId = $last ? ((int) $last->id + 1 + $try) : 1 + $try;
            $kode = 'PRD-' . date('Ymd') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
            $exists = ProductApotek::where('code', $kode)->exists();
            $try++;
        } while ($exists && $try < $maxTry);
        $data['code'] = $kode;

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
            // Stok keluar
            $jumlahKeluar = (int) $data['stok'];
            $product->stok = max(0, $product->stok - $jumlahKeluar);

            // Ambil id ApotekStok expired terdekat yang status=0
            $stokKeluarIds = \App\Models\ApotekStok::where('product_apotek_id', $product->id)
                ->where('status', 0)
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
        } else {
            // Stok masuk
            $jumlahMasuk = (int) $data['stok'];
            $product->stok += $jumlahMasuk;

            // Insert massal ApotekStok
            $insertData = [];
            for ($i = 0; $i < $jumlahMasuk; $i++) {
                $insertData[] = [
                    'id'               => (string) Str::uuid(),
                    'product_apotek_id'=> $product->id,
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
}
