<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockAdjustment;
use App\Models\StockTransaction;
use App\Models\StockTransactionItem;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Total Jenis Barang
        $totalProducts = Product::count();

        // 2. Total Keseluruhan Stok
        $totalStock = Product::sum('stock');

        // 3. Jumlah Barang Masuk (Total quantity & total transactions)
        $totalIncomingQuantity = StockTransactionItem::whereHas('transaction', function ($q) {
            $q->where('type', 'in');
        })->sum('quantity');

        $totalIncomingTransactions = StockTransaction::where('type', 'in')->count();

        // 4. Jumlah Barang Keluar (Total quantity & total transactions)
        $totalOutgoingQuantity = StockTransactionItem::whereHas('transaction', function ($q) {
            $q->where('type', 'out');
        })->sum('quantity');

        $totalOutgoingTransactions = StockTransaction::where('type', 'out')->count();

        // 5. Informasi Stok Menipis
        $lowStockProducts = Product::with('category')
            ->lowStock()
            ->orderBy('stock', 'asc')
            ->take(10)
            ->get();

        $lowStockCount = Product::lowStock()->count();
        $outOfStockCount = Product::where('stock', '<=', 0)->count();

        // Nilai Total Aset Inventaris (Estimasi Rp)
        $totalInventoryValue = Product::selectRaw('SUM(stock * purchase_price) as total_val')->value('total_val') ?? 0;

        // 6. Ringkasan Transaksi Terbaru
        $recentTransactions = StockTransaction::with(['supplier', 'user', 'items.product'])
            ->latest('transaction_date')
            ->latest('id')
            ->take(8)
            ->get();

        // 7. Grafik Aktivitas Barang Masuk dan Keluar (14 Hari Terakhir)
        $chartDates = [];
        $chartIncoming = [];
        $chartOutgoing = [];

        for ($i = 13; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $dateLabel = Carbon::now()->subDays($i)->translatedFormat('d M');
            $chartDates[] = $dateLabel;

            // In quantity on this date
            $inQty = StockTransactionItem::whereHas('transaction', function ($q) use ($date) {
                $q->where('type', 'in')->whereDate('transaction_date', $date);
            })->sum('quantity');

            // Out quantity on this date
            $outQty = StockTransactionItem::whereHas('transaction', function ($q) use ($date) {
                $q->where('type', 'out')->whereDate('transaction_date', $date);
            })->sum('quantity');

            $chartIncoming[] = (int) $inQty;
            $chartOutgoing[] = (int) $outQty;
        }

        // Kategori breakdown untuk chart donut
        $categoriesBreakdown = Category::withCount('products')
            ->having('products_count', '>', 0)
            ->get();

        $categoryLabels = $categoriesBreakdown->pluck('name')->toArray();
        $categoryData = $categoriesBreakdown->pluck('products_count')->toArray();

        // Status Stok Breakdown
        $stockSafeCount = Product::where('stock', '>', DB::raw('min_stock'))->count();
        $stockWarningCount = Product::where('stock', '>', 0)->where('stock', '<=', DB::raw('min_stock'))->count();

        return view('dashboard.index', compact(
            'totalProducts',
            'totalStock',
            'totalIncomingQuantity',
            'totalIncomingTransactions',
            'totalOutgoingQuantity',
            'totalOutgoingTransactions',
            'lowStockProducts',
            'lowStockCount',
            'outOfStockCount',
            'totalInventoryValue',
            'recentTransactions',
            'chartDates',
            'chartIncoming',
            'chartOutgoing',
            'categoryLabels',
            'categoryData',
            'stockSafeCount',
            'stockWarningCount'
        ));
    }
}
