<?php

namespace Database\Seeders;

use App\Models\RadiologySupply;
use App\Models\RadiologySupplyBatch;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RadiologySupplySeeder extends Seeder
{
    public function run(): void
    {
        $supplies = [
            [
                'name' => 'Film X-Ray 35x43 cm',
                'unit' => 'lembar',
                'stock' => 150,
                'warning_stock' => 20,
                'batches' => [
                    ['batch_number' => 'FXR-2024-001', 'quantity' => 100, 'remaining_quantity' => 80, 'expiry_date' => '2026-12-31'],
                    ['batch_number' => 'FXR-2025-001', 'quantity' => 100, 'remaining_quantity' => 70, 'expiry_date' => '2027-06-30'],
                ]
            ],
            [
                'name' => 'Kontras Iodin',
                'unit' => 'botol',
                'stock' => 45,
                'warning_stock' => 10,
                'batches' => [
                    ['batch_number' => 'KI-2024-015', 'quantity' => 50, 'remaining_quantity' => 30, 'expiry_date' => '2026-03-31'],
                    ['batch_number' => 'KI-2025-002', 'quantity' => 30, 'remaining_quantity' => 15, 'expiry_date' => '2027-01-15'],
                ]
            ],
            [
                'name' => 'Film X-Ray 24x30 cm',
                'unit' => 'lembar',
                'stock' => 0,
                'warning_stock' => 15,
                'batches' => []
            ],
            [
                'name' => 'Developer Solution',
                'unit' => 'liter',
                'stock' => 25,
                'warning_stock' => 5,
                'batches' => [
                    ['batch_number' => 'DEV-2024-008', 'quantity' => 30, 'remaining_quantity' => 25, 'expiry_date' => '2025-11-30'],
                ]
            ],
            [
                'name' => 'Fixer Solution',
                'unit' => 'liter',
                'stock' => 28,
                'warning_stock' => 5,
                'batches' => [
                    ['batch_number' => 'FIX-2024-012', 'quantity' => 30, 'remaining_quantity' => 28, 'expiry_date' => '2025-11-30'],
                ]
            ],
            [
                'name' => 'Barium Sulfat',
                'unit' => 'sachet',
                'stock' => 8,
                'warning_stock' => 10,
                'batches' => [
                    ['batch_number' => 'BAR-2023-020', 'quantity' => 50, 'remaining_quantity' => 8, 'expiry_date' => '2025-06-30'],
                ]
            ],
            [
                'name' => 'Lead Apron Protector',
                'unit' => 'unit',
                'stock' => 12,
                'warning_stock' => 3,
                'batches' => [
                    ['batch_number' => 'LAP-2023-001', 'quantity' => 15, 'remaining_quantity' => 12, 'expiry_date' => null],
                ]
            ],
            [
                'name' => 'Cassette X-Ray 35x43 cm',
                'unit' => 'unit',
                'stock' => 20,
                'warning_stock' => 5,
                'batches' => [
                    ['batch_number' => 'CAS-2022-005', 'quantity' => 25, 'remaining_quantity' => 20, 'expiry_date' => null],
                ]
            ],
            [
                'name' => 'Kontras Barium Enema',
                'unit' => 'botol',
                'stock' => 15,
                'warning_stock' => 8,
                'batches' => [
                    ['batch_number' => 'KBE-2024-003', 'quantity' => 20, 'remaining_quantity' => 10, 'expiry_date' => '2025-12-15'],
                    ['batch_number' => 'KBE-2024-010', 'quantity' => 10, 'remaining_quantity' => 5, 'expiry_date' => '2025-10-30'],
                ]
            ],
            [
                'name' => 'Film Marker L/R',
                'unit' => 'set',
                'stock' => 8,
                'warning_stock' => 2,
                'batches' => [
                    ['batch_number' => 'FML-2023-001', 'quantity' => 10, 'remaining_quantity' => 8, 'expiry_date' => null],
                ]
            ],
        ];

        foreach ($supplies as $supplyData) {
            $batches = $supplyData['batches'];
            unset($supplyData['batches']);

            $supply = RadiologySupply::create($supplyData);

            foreach ($batches as $batchData) {
                $batchData['supply_id'] = $supply->id;
                RadiologySupplyBatch::create($batchData);
            }
        }

        $this->command->info('Radiology supplies seeded successfully!');
    }
}
