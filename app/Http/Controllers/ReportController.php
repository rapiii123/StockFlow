<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockTransaction;
use App\Models\StockTransactionItem;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
        $categoryId = $request->input('category_id');

        // Products for current stock report
        $productsQuery = Product::with('category');
        if ($categoryId) {
            $productsQuery->where('category_id', $categoryId);
        }
        $products = $productsQuery->orderBy('name')->get();

        // Transaction Summary in selected period
        $incomingTotal = StockTransactionItem::whereHas('transaction', function ($q) use ($startDate, $endDate) {
            $q->where('type', 'in')
              ->whereDate('transaction_date', '>=', $startDate)
              ->whereDate('transaction_date', '<=', $endDate);
        })->sum('quantity');

        $outgoingTotal = StockTransactionItem::whereHas('transaction', function ($q) use ($startDate, $endDate) {
            $q->where('type', 'out')
              ->whereDate('transaction_date', '>=', $startDate)
              ->whereDate('transaction_date', '<=', $endDate);
        })->sum('quantity');

        $incomingValue = StockTransaction::where('type', 'in')
            ->whereDate('transaction_date', '>=', $startDate)
            ->whereDate('transaction_date', '<=', $endDate)
            ->sum('total_amount');

        $outgoingValue = StockTransaction::where('type', 'out')
            ->whereDate('transaction_date', '>=', $startDate)
            ->whereDate('transaction_date', '<=', $endDate)
            ->sum('total_amount');

        // Detailed transaction log for the period
        $transactions = StockTransaction::with(['items.product', 'supplier', 'user'])
            ->whereDate('transaction_date', '>=', $startDate)
            ->whereDate('transaction_date', '<=', $endDate)
            ->latest('transaction_date')
            ->get();

        $categories = Category::orderBy('name')->get();

        return view('reports.index', compact(
            'products',
            'transactions',
            'categories',
            'startDate',
            'endDate',
            'categoryId',
            'incomingTotal',
            'outgoingTotal',
            'incomingValue',
            'outgoingValue'
        ));
    }

    public function exportCsv(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        $transactions = StockTransaction::with(['items.product', 'supplier', 'user'])
            ->whereDate('transaction_date', '>=', $startDate)
            ->whereDate('transaction_date', '<=', $endDate)
            ->latest('transaction_date')
            ->get();

        $filename = "stockflow-laporan-{$startDate}-sd-{$endDate}.csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($transactions) {
            $handle = fopen('php://output', 'w');
            // Add UTF-8 BOM for Excel
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, ['No. Referensi', 'Jenis Transaksi', 'Tanggal', 'Pemasok / Penerima', 'Nama Barang', 'SKU', 'Jumlah', 'Harga Satuan (Rp)', 'Total (Rp)', 'Operator']);

            foreach ($transactions as $tx) {
                $partner = $tx->type === 'in' ? ($tx->supplier->name ?? '-') : ($tx->recipient ?? '-');
                foreach ($tx->items as $item) {
                    fputcsv($handle, [
                        $tx->reference_no,
                        $tx->type === 'in' ? 'Barang Masuk' : 'Barang Keluar',
                        $tx->transaction_date->format('d/m/Y'),
                        $partner,
                        $item->product->name ?? 'Produk Dihapus',
                        $item->product->sku ?? '-',
                        $item->quantity,
                        $item->unit_price,
                        $item->subtotal,
                        $tx->user->name ?? 'Sistem',
                    ]);
                }
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
