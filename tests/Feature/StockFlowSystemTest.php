<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockTransaction;
use App\Models\Supplier;
use App\Models\User;
use Tests\TestCase;

class StockFlowSystemTest extends TestCase
{
    public function test_guest_is_redirected_to_login()
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_user_can_view_login_page()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('StockFlow');
    }

    public function test_user_can_authenticate()
    {
        $user = User::where('email', 'admin@stockflow.test')->first();

        $response = $this->post('/login', [
            'email' => 'admin@stockflow.test',
            'password' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_dashboard_displays_all_required_metrics()
    {
        $user = User::where('email', 'admin@stockflow.test')->first();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        // User requirements check:
        // Total jenis barang
        $response->assertSee('Total Jenis Barang');
        // Total keseluruhan stok
        $response->assertSee('Total Fisik Stok');
        // Jumlah barang masuk
        $response->assertSee('Total Barang Masuk');
        // Jumlah barang keluar
        $response->assertSee('Total Barang Keluar');
        // Informasi stok menipis
        $response->assertSee('Informasi Stok Menipis');
        // Grafik aktivitas barang masuk dan keluar
        $response->assertSee('Grafik Aktivitas Barang Masuk dan Keluar');
        // Ringkasan transaksi terbaru
        $response->assertSee('Ringkasan Transaksi Terbaru');
    }

    public function test_products_catalog_is_accessible()
    {
        $user = User::where('email', 'admin@stockflow.test')->first();

        $response = $this->actingAs($user)->get('/products');
        $response->assertStatus(200);
        $response->assertSee('Data Barang');
    }

    public function test_can_create_new_product()
    {
        $user = User::where('email', 'admin@stockflow.test')->first();
        $cat = Category::first();
        $testSku = 'SKU-' . rand(10000, 99999);

        $response = $this->actingAs($user)->post('/products', [
            'category_id' => $cat->id,
            'sku' => $testSku,
            'name' => 'Produk Uji Coba StockFlow ' . $testSku,
            'unit' => 'Pcs',
            'purchase_price' => 150000,
            'selling_price' => 200000,
            'stock' => 25,
            'min_stock' => 5,
            'location' => 'Rak Z-99',
            'description' => 'Produk testing',
        ]);

        $response->assertRedirect('/products');
        $this->assertDatabaseHas('products', [
            'sku' => $testSku,
            'stock' => 25,
        ]);
    }

    public function test_stock_in_increases_product_stock()
    {
        $user = User::where('email', 'admin@stockflow.test')->first();
        $product = Product::where('sku', 'TEST-SKU-999')->first() ?? Product::first();
        $initialStock = $product->stock;
        $supplier = Supplier::first();

        $refNo = 'TR-IN-TEST-' . uniqid();

        $response = $this->actingAs($user)->post('/transactions', [
            'type' => 'in',
            'reference_no' => $refNo,
            'transaction_date' => date('Y-m-d'),
            'supplier_id' => $supplier->id,
            'notes' => 'Penerimaan uji coba',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 10,
                    'unit_price' => $product->purchase_price,
                ]
            ]
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('stock_transactions', ['reference_no' => $refNo]);

        $product->refresh();
        $this->assertEquals($initialStock + 10, $product->stock);
    }

    public function test_stock_out_decreases_product_stock()
    {
        $user = User::where('email', 'admin@stockflow.test')->first();
        $product = Product::where('sku', 'TEST-SKU-999')->first() ?? Product::where('stock', '>=', 5)->first();
        $initialStock = $product->stock;

        $refNo = 'TR-OUT-TEST-' . uniqid();

        $response = $this->actingAs($user)->post('/transactions', [
            'type' => 'out',
            'reference_no' => $refNo,
            'transaction_date' => date('Y-m-d'),
            'recipient' => 'Divisi Percobaan',
            'notes' => 'Pengeluaran uji coba',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                    'unit_price' => $product->selling_price,
                ]
            ]
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('stock_transactions', ['reference_no' => $refNo]);

        $product->refresh();
        $this->assertEquals($initialStock - 2, $product->stock);
    }

    public function test_stock_out_fails_when_insufficient_stock()
    {
        $user = User::where('email', 'admin@stockflow.test')->first();
        $product = Product::where('stock', '<', 50)->first();

        $refNo = 'TR-OUT-FAIL-' . uniqid();

        $response = $this->actingAs($user)->post('/transactions', [
            'type' => 'out',
            'reference_no' => $refNo,
            'transaction_date' => date('Y-m-d'),
            'recipient' => 'Divisi Test',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => $product->stock + 500, // Exceeds available stock
                    'unit_price' => $product->selling_price,
                ]
            ]
        ]);

        $response->assertSessionHasErrors();
        $this->assertDatabaseMissing('stock_transactions', ['reference_no' => $refNo]);
    }

    public function test_reports_and_csv_export()
    {
        $user = User::where('email', 'admin@stockflow.test')->first();

        $response = $this->actingAs($user)->get('/reports');
        $response->assertStatus(200);
        $response->assertSee('Laporan &amp; Rekapitulasi Inventaris', false);

        $csvResponse = $this->actingAs($user)->get('/reports/export-csv');
        $csvResponse->assertStatus(200);
        $csvResponse->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }
}
