<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockAdjustment;
use App\Models\StockTransaction;
use App\Models\StockTransactionItem;
use App\Models\Supplier;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Users
        $admin = User::firstOrCreate(
            ['email' => 'admin@stockflow.test'],
            [
                'name' => 'Budi Santoso (Admin Gudang)',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone' => '081234567890',
                'is_active' => true,
            ]
        );

        $staff = User::firstOrCreate(
            ['email' => 'staff@stockflow.test'],
            [
                'name' => 'Dewi Lestari (Operator Stok)',
                'password' => Hash::make('password'),
                'role' => 'staff',
                'phone' => '081987654321',
                'is_active' => true,
            ]
        );

        $manager = User::firstOrCreate(
            ['email' => 'manager@stockflow.test'],
            [
                'name' => 'Hendra Wijaya (Warehouse Manager)',
                'password' => Hash::make('password'),
                'role' => 'manager',
                'phone' => '081345678912',
                'is_active' => true,
            ]
        );

        // 2. Categories
        $categoriesData = [
            ['code' => 'CAT-ELK', 'name' => 'Elektronik & Perangkat', 'description' => 'Komponen elektronik, adaptor, kabel, dan perlengkapan digital.'],
            ['code' => 'CAT-PRK', 'name' => 'Alat & Perkakas Gudang', 'description' => 'Peralatan mekanik, obeng, bor, tangga, pallet jack.'],
            ['code' => 'CAT-BBK', 'name' => 'Bahan Baku & Material', 'description' => 'Plastik, besi plat, kawat, resin, dan material produksi.'],
            ['code' => 'CAT-PCK', 'name' => 'Packaging & Pengemasan', 'description' => 'Kardus, bubble wrap, stretch film, lakban, dan palet.'],
            ['code' => 'CAT-ATK', 'name' => 'Alat Tulis & Inventaris Kantor', 'description' => 'Kertas, barcode label, thermal roll, spidol marker.'],
            ['code' => 'CAT-SCD', 'name' => 'Suku Cadang Mesin', 'description' => 'Spare part mesin conveyor, bearing, vanbelt, pelumas oli.'],
        ];

        $categories = [];
        foreach ($categoriesData as $cat) {
            $categories[$cat['code']] = Category::firstOrCreate(['code' => $cat['code']], $cat);
        }

        // 3. Suppliers
        $suppliersData = [
            [
                'code' => 'SUP-001',
                'name' => 'PT Surya Elektronika Abadi',
                'contact_person' => 'Rian Pratama',
                'phone' => '021-55678901',
                'email' => 'sales@suryaelektronik.co.id',
                'address' => 'Kawasan Industri MM2100 Blok C-4, Cikarang Barat, Bekasi',
            ],
            [
                'code' => 'SUP-002',
                'name' => 'CV Maju Jaya Perkakas',
                'contact_person' => 'Slamet Riyadi',
                'phone' => '021-88991234',
                'email' => 'order@majujayaperkakas.com',
                'address' => 'Jl. Pangeran Jayakarta No. 45, Mangga Dua, Jakarta Pusat',
            ],
            [
                'code' => 'SUP-003',
                'name' => 'PT Nusantara Pack Lestari',
                'contact_person' => 'Siti Rahmawati',
                'phone' => '031-78901234',
                'email' => 'marketing@nusantarapack.id',
                'address' => 'Jl. Rungkut Industri IV No. 12, Surabaya',
            ],
            [
                'code' => 'SUP-004',
                'name' => 'PT Mitra Teknik Suku Cadang',
                'contact_person' => 'Agus Hermanto',
                'phone' => '022-77889900',
                'email' => 'cs@mitrateknik.co.id',
                'address' => 'Kawasan Industri Soekarno Hatta No. 88, Bandung',
            ],
        ];

        $suppliers = [];
        foreach ($suppliersData as $sup) {
            $suppliers[$sup['code']] = Supplier::firstOrCreate(['code' => $sup['code']], $sup);
        }

        // 4. Products (variasi: stok normal, stok menipis <= min_stock, stok habis = 0)
        $productsData = [
            [
                'category_id' => $categories['CAT-ELK']->id,
                'sku' => 'ELK-001',
                'barcode' => '899100100011',
                'name' => 'Barcode Scanner Wireless 2D Handheld',
                'unit' => 'Unit',
                'purchase_price' => 450000,
                'selling_price' => 620000,
                'stock' => 18,
                'min_stock' => 5,
                'location' => 'Rak A-01 (Elektronik)',
                'description' => 'Scanner barcode & QR code nirkabel dengan jangkauan 20 meter.',
            ],
            [
                'category_id' => $categories['CAT-ELK']->id,
                'sku' => 'ELK-002',
                'barcode' => '899100100028',
                'name' => 'Thermal Receipt & Label Printer 80mm USB',
                'unit' => 'Unit',
                'purchase_price' => 850000,
                'selling_price' => 1150000,
                'stock' => 4, // Menipis!
                'min_stock' => 6,
                'location' => 'Rak A-02 (Elektronik)',
                'description' => 'Printer kasir dan resi pergudangan direct thermal berkecepatan tinggi.',
            ],
            [
                'category_id' => $categories['CAT-ELK']->id,
                'sku' => 'ELK-003',
                'barcode' => '899100100035',
                'name' => 'Kabel Power Extension Heavy Duty 10M',
                'unit' => 'Roll',
                'purchase_price' => 95000,
                'selling_price' => 135000,
                'stock' => 0, // Habis!
                'min_stock' => 10,
                'location' => 'Rak A-03 (Elektronik)',
                'description' => 'Kabel gulung tembaga murni tahan panas untuk operasional gudang.',
            ],
            [
                'category_id' => $categories['CAT-PRK']->id,
                'sku' => 'PRK-001',
                'barcode' => '899200200012',
                'name' => 'Hand Pallet Truck Manual 3 Ton',
                'unit' => 'Unit',
                'purchase_price' => 3200000,
                'selling_price' => 4100000,
                'stock' => 3, // Menipis!
                'min_stock' => 5,
                'location' => 'Area Heavy Pallet - Bay 1',
                'description' => 'Alat angkat manual hidrolik kapasitas 3.000 kg roda polyurethane.',
            ],
            [
                'category_id' => $categories['CAT-PRK']->id,
                'sku' => 'PRK-002',
                'barcode' => '899200200029',
                'name' => 'Cordless Drill Driver 20V + Battery Kit',
                'unit' => 'Set',
                'purchase_price' => 650000,
                'selling_price' => 890000,
                'stock' => 12,
                'min_stock' => 4,
                'location' => 'Rak B-01 (Perkakas)',
                'description' => 'Bor baterai brushless torsi tinggi untuk perakitan rak gudang.',
            ],
            [
                'category_id' => $categories['CAT-PRK']->id,
                'sku' => 'PRK-003',
                'barcode' => '899200200036',
                'name' => 'Tangga Lipat Aluminium Teleskopik 3.8M',
                'unit' => 'Unit',
                'purchase_price' => 780000,
                'selling_price' => 1050000,
                'stock' => 2, // Menipis!
                'min_stock' => 5,
                'location' => 'Rak B-04 (Perkakas)',
                'description' => 'Tangga portabel kuat tahan beban hingga 150 kg.',
            ],
            [
                'category_id' => $categories['CAT-PCK']->id,
                'sku' => 'PCK-001',
                'barcode' => '899300300013',
                'name' => 'Kardus Box Single Wall Ukuran M (30x20x15cm)',
                'unit' => 'Pcs',
                'purchase_price' => 3500,
                'selling_price' => 5500,
                'stock' => 1250,
                'min_stock' => 200,
                'location' => 'Lantai 2 - Zona Packaging C-1',
                'description' => 'Kardus packing coklat kualitas kokoh corrugated k125.',
            ],
            [
                'category_id' => $categories['CAT-PCK']->id,
                'sku' => 'PCK-002',
                'barcode' => '899300300020',
                'name' => 'Stretch Film Plastik Wrapping 50cm x 300M',
                'unit' => 'Roll',
                'purchase_price' => 52000,
                'selling_price' => 75000,
                'stock' => 8, // Menipis!
                'min_stock' => 25,
                'location' => 'Lantai 2 - Zona Packaging C-2',
                'description' => 'Plastik pembungkus palet bening elastis daya rekat kuat.',
            ],
            [
                'category_id' => $categories['CAT-PCK']->id,
                'sku' => 'PCK-003',
                'barcode' => '899300300037',
                'name' => 'Lakban Bening Daimaru 48mm x 90 Yard',
                'unit' => 'Roll',
                'purchase_price' => 8500,
                'selling_price' => 12500,
                'stock' => 180,
                'min_stock' => 50,
                'location' => 'Lantai 2 - Zona Packaging C-3',
                'description' => 'Lakban segel dus OPP tape perekat premium.',
            ],
            [
                'category_id' => $categories['CAT-ATK']->id,
                'sku' => 'ATK-001',
                'barcode' => '899400400014',
                'name' => 'Stiker Label Barcode Thermal Direct 100x150mm',
                'unit' => 'Roll',
                'purchase_price' => 42000,
                'selling_price' => 60000,
                'stock' => 65,
                'min_stock' => 20,
                'location' => 'Rak D-01 (Office & ATK)',
                'description' => 'Label sticker pengiriman ekspedisi dan resi warehouse, isi 500 pcs/roll.',
            ],
            [
                'category_id' => $categories['CAT-ATK']->id,
                'sku' => 'ATK-002',
                'barcode' => '899400400021',
                'name' => 'Spidol Marker Permanent Hitam Snowman Jumbo',
                'unit' => 'Box',
                'purchase_price' => 85000,
                'selling_price' => 110000,
                'stock' => 5, // Menipis!
                'min_stock' => 8,
                'location' => 'Rak D-02 (Office & ATK)',
                'description' => 'Spidol tahan air untuk penulisan kode dus kardus, isi 12 pcs/box.',
            ],
            [
                'category_id' => $categories['CAT-SCD']->id,
                'sku' => 'SCD-001',
                'barcode' => '899500500015',
                'name' => 'Bearing Industri SKF 6205-2RSH Deep Groove',
                'unit' => 'Pcs',
                'purchase_price' => 68000,
                'selling_price' => 95000,
                'stock' => 35,
                'min_stock' => 10,
                'location' => 'Rak E-01 (Spare Parts)',
                'description' => 'Bearing presisi tinggi untuk roller conveyor dan motor listrik.',
            ],
            [
                'category_id' => $categories['CAT-SCD']->id,
                'sku' => 'SCD-002',
                'barcode' => '899500500022',
                'name' => 'Pelumas Rantai & Conveyor Spray 500ml',
                'unit' => 'Can',
                'purchase_price' => 48000,
                'selling_price' => 70000,
                'stock' => 14,
                'min_stock' => 12,
                'location' => 'Rak E-03 (Spare Parts)',
                'description' => 'Aerosol synthetic lubricant anti debu dan anti karat.',
            ],
        ];

        $products = [];
        foreach ($productsData as $prod) {
            $products[$prod['sku']] = Product::firstOrCreate(['sku' => $prod['sku']], $prod);
        }

        // 5. Stock Transactions (Incoming & Outgoing over past 14 days)
        $transactionsData = [
            // Masuk: 10 hari lalu
            [
                'reference_no' => 'TR-IN-20261028-001',
                'type' => 'in',
                'transaction_date' => Carbon::now()->subDays(12)->format('Y-m-d'),
                'supplier_id' => $suppliers['SUP-001']->id,
                'recipient' => null,
                'user_id' => $admin->id,
                'notes' => 'Penerimaan stok berkala scanner dan printer thermal via PO-2026-1025.',
                'items' => [
                    ['product_id' => $products['ELK-001']->id, 'quantity' => 10, 'unit_price' => 450000],
                    ['product_id' => $products['ELK-002']->id, 'quantity' => 5, 'unit_price' => 850000],
                ]
            ],
            // Masuk: 8 hari lalu
            [
                'reference_no' => 'TR-IN-20261101-001',
                'type' => 'in',
                'transaction_date' => Carbon::now()->subDays(8)->format('Y-m-d'),
                'supplier_id' => $suppliers['SUP-003']->id,
                'recipient' => null,
                'user_id' => $staff->id,
                'notes' => 'Restock kardus packing dan lakban OPP pengiriman gelombang I.',
                'items' => [
                    ['product_id' => $products['PCK-001']->id, 'quantity' => 500, 'unit_price' => 3500],
                    ['product_id' => $products['PCK-003']->id, 'quantity' => 100, 'unit_price' => 8500],
                ]
            ],
            // Keluar: 7 hari lalu
            [
                'reference_no' => 'TR-OUT-20261102-001',
                'type' => 'out',
                'transaction_date' => Carbon::now()->subDays(7)->format('Y-m-d'),
                'supplier_id' => null,
                'recipient' => 'Divisi Pengemasan & Ekspedisi Gudang B',
                'user_id' => $staff->id,
                'notes' => 'Pengeluaran packaging untuk operasional harian ekspedisi.',
                'items' => [
                    ['product_id' => $products['PCK-001']->id, 'quantity' => 200, 'unit_price' => 5500],
                    ['product_id' => $products['PCK-003']->id, 'quantity' => 25, 'unit_price' => 12500],
                ]
            ],
            // Masuk: 5 hari lalu
            [
                'reference_no' => 'TR-IN-20261104-001',
                'type' => 'in',
                'transaction_date' => Carbon::now()->subDays(5)->format('Y-m-d'),
                'supplier_id' => $suppliers['SUP-002']->id,
                'recipient' => null,
                'user_id' => $admin->id,
                'notes' => 'Pengadaan perkakas baru unit pallet truck & bor cordless.',
                'items' => [
                    ['product_id' => $products['PRK-001']->id, 'quantity' => 2, 'unit_price' => 3200000],
                    ['product_id' => $products['PRK-002']->id, 'quantity' => 6, 'unit_price' => 650000],
                ]
            ],
            // Keluar: 4 hari lalu
            [
                'reference_no' => 'TR-OUT-20261105-001',
                'type' => 'out',
                'transaction_date' => Carbon::now()->subDays(4)->format('Y-m-d'),
                'supplier_id' => null,
                'recipient' => 'Cabang Distribusi Cakung',
                'user_id' => $staff->id,
                'notes' => 'Distribusi unit scanner barcode dan spidol penandaan pallet.',
                'items' => [
                    ['product_id' => $products['ELK-001']->id, 'quantity' => 4, 'unit_price' => 620000],
                    ['product_id' => $products['ATK-002']->id, 'quantity' => 3, 'unit_price' => 110000],
                ]
            ],
            // Masuk: 3 hari lalu
            [
                'reference_no' => 'TR-IN-20261106-001',
                'type' => 'in',
                'transaction_date' => Carbon::now()->subDays(3)->format('Y-m-d'),
                'supplier_id' => $suppliers['SUP-004']->id,
                'recipient' => null,
                'user_id' => $admin->id,
                'notes' => 'Penerimaan suku cadang mesin conveyor SKF Bearing dan pelumas.',
                'items' => [
                    ['product_id' => $products['SCD-001']->id, 'quantity' => 20, 'unit_price' => 68000],
                    ['product_id' => $products['SCD-002']->id, 'quantity' => 15, 'unit_price' => 48000],
                ]
            ],
            // Keluar: 2 hari lalu
            [
                'reference_no' => 'TR-OUT-20261107-001',
                'type' => 'out',
                'transaction_date' => Carbon::now()->subDays(2)->format('Y-m-d'),
                'supplier_id' => null,
                'recipient' => 'Tim Maintenance Jalur Conveyor 3',
                'user_id' => $staff->id,
                'notes' => 'Penggantian rutin bearing rusak dan pemakaian oli semprot.',
                'items' => [
                    ['product_id' => $products['SCD-001']->id, 'quantity' => 6, 'unit_price' => 95000],
                    ['product_id' => $products['SCD-002']->id, 'quantity' => 3, 'unit_price' => 70000],
                ]
            ],
            // Masuk: Kemarin
            [
                'reference_no' => 'TR-IN-20261108-001',
                'type' => 'in',
                'transaction_date' => Carbon::now()->subDay()->format('Y-m-d'),
                'supplier_id' => $suppliers['SUP-003']->id,
                'recipient' => null,
                'user_id' => $admin->id,
                'notes' => 'Penerimaan label barcode thermal 100x150mm gelombang darurat.',
                'items' => [
                    ['product_id' => $products['ATK-001']->id, 'quantity' => 40, 'unit_price' => 42000],
                ]
            ],
            // Keluar: Hari ini
            [
                'reference_no' => 'TR-OUT-20261109-001',
                'type' => 'out',
                'transaction_date' => Carbon::now()->format('Y-m-d'),
                'supplier_id' => null,
                'recipient' => 'Gudang Packaging E-Commerce',
                'user_id' => $staff->id,
                'notes' => 'Supply roll plastik stretch film dan kardus pengiriman pesanan.',
                'items' => [
                    ['product_id' => $products['PCK-002']->id, 'quantity' => 5, 'unit_price' => 75000],
                    ['product_id' => $products['PCK-001']->id, 'quantity' => 150, 'unit_price' => 5500],
                ]
            ],
        ];

        foreach ($transactionsData as $trans) {
            $items = $trans['items'];
            unset($trans['items']);

            $totalItems = 0;
            $totalAmount = 0;

            foreach ($items as $item) {
                $totalItems += $item['quantity'];
                $totalAmount += ($item['quantity'] * $item['unit_price']);
            }

            $trans['total_items'] = $totalItems;
            $trans['total_amount'] = $totalAmount;

            $stockTx = StockTransaction::firstOrCreate(['reference_no' => $trans['reference_no']], $trans);

            foreach ($items as $item) {
                StockTransactionItem::firstOrCreate(
                    [
                        'stock_transaction_id' => $stockTx->id,
                        'product_id' => $item['product_id'],
                    ],
                    [
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'subtotal' => $item['quantity'] * $item['unit_price'],
                    ]
                );
            }
        }

        // 6. Stock Adjustment (Opname) sample
        StockAdjustment::firstOrCreate(
            ['reference_no' => 'ADJ-20261105-001'],
            [
                'product_id' => $products['ELK-003']->id,
                'user_id' => $admin->id,
                'system_stock' => 2,
                'physical_stock' => 0,
                'difference' => -2,
                'reason' => 'Kerusakan fisik kabel robek saat pemindahan rak',
                'adjustment_date' => Carbon::now()->subDays(4)->format('Y-m-d'),
                'notes' => 'Kabel sudah tidak layak pakai dan diafkir.',
            ]
        );
    }
}
