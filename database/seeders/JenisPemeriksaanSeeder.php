<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\JenisPemeriksaanPenunjang;
use App\Models\TemplateField;

class JenisPemeriksaanSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Contoh jenis pemeriksaan radiologi dengan grup
        $radioJenis = JenisPemeriksaanPenunjang::create([
            'name' => 'CT SCAN Thorax',
            'type' => 'radiologi',
            'harga' => 850000
        ]);

        // Tambahkan grup field untuk pemeriksaan jantung
        $jantungField = $radioJenis->templateFields()->create([
            'field_name' => 'pemeriksaan_jantung',
            'field_label' => 'Pemeriksaan Jantung',
            'field_type' => 'group',
            'placeholder' => null,
            'order' => 1
        ]);

        // Contoh pemeriksaan dalam grup jantung
        $jantungField->fieldItems()->create([
            'item_name' => 'diameter_aorta',
            'item_label' => 'Hasil',
            'item_type' => 'text',
            'examination_name' => 'Diameter Aorta',
            'unit' => 'mm',
            'normal_range' => '20-37 mm',
            'placeholder' => 'Masukkan hasil diameter aorta',
            'order' => 1
        ]);

        $jantungField->fieldItems()->create([
            'item_name' => 'lv_edd',
            'item_label' => 'Hasil',
            'item_type' => 'text',
            'examination_name' => 'LV EDD',
            'unit' => 'mm',
            'normal_range' => '35-52 mm',
            'placeholder' => 'Masukkan hasil LV EDD',
            'order' => 2
        ]);

        $jantungField->fieldItems()->create([
            'item_name' => 'ejection_fraction',
            'item_label' => 'Hasil',
            'item_type' => 'text',
            'examination_name' => 'Ejection Fraction',
            'unit' => '%',
            'normal_range' => '52-77%',
            'placeholder' => 'Masukkan hasil ejection fraction',
            'order' => 3
        ]);

        // Tambahkan grup field untuk pemeriksaan paru
        $paruField = $radioJenis->templateFields()->create([
            'field_name' => 'pemeriksaan_paru',
            'field_label' => 'Pemeriksaan Paru-paru',
            'field_type' => 'group',
            'placeholder' => null,
            'order' => 2
        ]);

        // Contoh pemeriksaan dalam grup paru
        $paruField->fieldItems()->create([
            'item_name' => 'cor_pulmonale',
            'item_label' => 'Hasil',
            'item_type' => 'text',
            'examination_name' => 'Cor Pulmonale',
            'unit' => '-',
            'normal_range' => 'Tidak ditemukan',
            'placeholder' => 'Masukkan hasil cor pulmonale',
            'order' => 1
        ]);

        $paruField->fieldItems()->create([
            'item_name' => 'infiltrat',
            'item_label' => 'Hasil',
            'item_type' => 'text',
            'examination_name' => 'Infiltrat',
            'unit' => '-',
            'normal_range' => 'Tidak ada infiltrat',
            'placeholder' => 'Masukkan hasil infiltrat',
            'order' => 2
        ]);

        // Tambah field biasa untuk kesimpulan
        $radioJenis->templateFields()->create([
            'field_name' => 'kesimpulan',
            'field_label' => 'Kesimpulan',
            'field_type' => 'textarea',
            'placeholder' => 'Masukkan kesimpulan pemeriksaan',
            'order' => 3
        ]);

        // Contoh jenis pemeriksaan laboratorium dengan grup
        $labJenis = JenisPemeriksaanPenunjang::create([
            'name' => 'Pemeriksaan Darah Lengkap',
            'type' => 'lab',
            'harga' => 150000
        ]);

        // Tambahkan grup field untuk hematologi
        $hematologiField = $labJenis->templateFields()->create([
            'field_name' => 'hematologi',
            'field_label' => 'Parameter Hematologi',
            'field_type' => 'group',
            'placeholder' => null,
            'order' => 1
        ]);

        // Sub-field untuk hematologi
        $hematologiField->fieldItems()->create([
            'item_name' => 'hemoglobin',
            'item_label' => 'Hasil',
            'item_type' => 'text',
            'examination_name' => 'Hemoglobin',
            'unit' => 'g/dL',
            'normal_range' => 'L: 13.2-16.6, P: 12.0-15.5',
            'placeholder' => 'Masukkan nilai hemoglobin',
            'order' => 1
        ]);

        $hematologiField->fieldItems()->create([
            'item_name' => 'hematokrit',
            'item_label' => 'Hasil',
            'item_type' => 'text',
            'examination_name' => 'Hematokrit',
            'unit' => '%',
            'normal_range' => 'L: 39-49, P: 35-45',
            'placeholder' => 'Masukkan nilai hematokrit',
            'order' => 2
        ]);

        $hematologiField->fieldItems()->create([
            'item_name' => 'leukosit',
            'item_label' => 'Hasil',
            'item_type' => 'text',
            'examination_name' => 'Leukosit',
            'unit' => '/μL',
            'normal_range' => '4,800-10,800',
            'placeholder' => 'Masukkan jumlah leukosit',
            'order' => 3
        ]);

        // Grup untuk kimia darah
        $kimiaField = $labJenis->templateFields()->create([
            'field_name' => 'kimia_darah',
            'field_label' => 'Kimia Darah',
            'field_type' => 'group',
            'placeholder' => null,
            'order' => 2
        ]);

        $kimiaField->fieldItems()->create([
            'item_name' => 'gula_darah',
            'item_label' => 'Hasil',
            'item_type' => 'text',
            'examination_name' => 'Gula Darah Sewaktu',
            'unit' => 'mg/dL',
            'normal_range' => '< 200',
            'placeholder' => 'Masukkan nilai gula darah',
            'order' => 1
        ]);

        $kimiaField->fieldItems()->create([
            'item_name' => 'kolesterol',
            'item_label' => 'Hasil',
            'item_type' => 'text',
            'examination_name' => 'Kolesterol Total',
            'unit' => 'mg/dL',
            'normal_range' => '< 200',
            'placeholder' => 'Masukkan nilai kolesterol',
            'order' => 2
        ]);

        echo "Seeder berhasil dijalankan!\n";
        echo "Ditambahkan jenis pemeriksaan: CT SCAN Thorax, Pemeriksaan Darah Lengkap\n";
        echo "Dengan grup field: Pemeriksaan Jantung, Pemeriksaan Paru-paru, Parameter Hematologi, Kimia Darah\n";
    }
}
