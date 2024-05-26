<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Customer;
use App\Models\ItemStock;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        \App\Models\User::factory()->create([
            'name' => 'Rahayu',
            'email' => 'rahayuributwibowo@gmail.com'
        ]);

        Customer::factory(10)->create();

        $uomData = [
            [
                'name' => 'PCS',
                'description' => 'Pieces'
            ],
            [
                'name' => 'BOX',
                'description' => 'Box'
            ],
            [
                'name' => 'KG',
                'description' => 'Kilogram'
            ],
            [
                'name' => 'MTR',
                'description' => 'Meter'
            ]
        ];

        collect($uomData)->each(fn($uom) => \App\Models\Uom::create($uom));

        $itemData = [
            [
                'name' => 'Keramik',
                'uom_id' => 1,
                'description' => 'Keramik Lantai',
                'price' => 100000,
                'code' => 'KRMK'
            ],
            [
                'name' => 'Cat',
                'uom_id' => 2,
                'description' => 'Cat Tembok',
                'price' => 200000,
                'code' => 'CT'
            ],
            [
                'name' => 'Paku',
                'uom_id' => 3,
                'description' => 'Paku Besi',
                'price' => 300000,
                'code' => 'PK'
            ]
        ];

        collect($itemData)->each(fn($item) => \App\Models\Item::create($item));

        $itemStock = [
            [
                'item_id' => 1,
                'warehouse_id' => 1,
                'quantity' => 100
            ],
            [
                'item_id' => 1,
                'warehouse_id' => 2,
                'quantity' => 200
            ],
            [
                'item_id' => 1,
                'warehouse_id' => 3,
                'quantity' => 300
            ],
            [
                'item_id' => 2,
                'warehouse_id' => 1,
                'quantity' => 400
            ],
            [
                'item_id' => 2,
                'warehouse_id' => 2,
                'quantity' => 500
            ],
            [
                'item_id' => 2,
                'warehouse_id' => 3,
                'quantity' => 600
            ],
            [
                'item_id' => 3,
                'warehouse_id' => 1,
                'quantity' => 700
            ],
            [
                'item_id' => 3,
                'warehouse_id' => 2,
                'quantity' => 800
            ],
            [
                'item_id' => 3,
                'warehouse_id' => 3,
                'quantity' => 900
            ],
        ];

        $dataWarehouse = [
            [
                'name' => 'MDN',
                'address' => 'Jl. Kebun, Kota Medan, Sumatera Utara'
            ],
            [
                'name' => 'JKT',
                'address' => 'Jl. Kebun, Kota Jakarta, DKI Jakarta'
            ],
            [
                'name' => 'BDG',
                'address' => 'Jl. Kebun, Kota Bandung, Jawa Barat'
            ],
            [
                'name' => 'SBY',
                'address' => 'Jl. Kebun, Kota Surabaya, Jawa Timur'
            ]
        ];

        collect($dataWarehouse)->each(fn($warehouse) => Warehouse::create($warehouse));

        collect($itemStock)->each(fn($stock) => ItemStock::create($stock));

    }
}
