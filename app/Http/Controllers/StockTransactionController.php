<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockTransaction;
use App\Models\StockTransactionItem;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StockTransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = StockTransaction::with(['supplier', 'user', 'items.product']);

        // Filter Type (in/out)
        if ($request->filled('type') && in_array($request->type, ['in', 'out'])) {
            $query->where('type', $request->type);
        }

        // Filter Date Range
        if ($request->filled('start_date')) {
            $query->whereDate('transaction_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('transaction_date', '<=', $request->end_date);
        }

        // Search Reference, Recipient, or Notes
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_no', 'like', "%{$search}%")
                  ->orWhere('recipient', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhereHas('supplier', function ($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $transactions = $query->latest('transaction_date')->latest('id')->paginate(15)->withQueryString();

        // Metrics for top badges
        $countIn = StockTransaction::where('type', 'in')->count();
        $countOut = StockTransaction::where('type', 'out')->count();
        $sumInItems = StockTransaction::where('type', 'in')->sum('total_items');
        $sumOutItems = StockTransaction::where('type', 'out')->sum('total_items');

        return view('transactions.index', compact(
            'transactions',
            'countIn',
            'countOut',
            'sumInItems',
            'sumOutItems'
        ));
    }

    public function createIn(Request $request)
    {
        $suppliers = Supplier::orderBy('name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();

        $prefix = 'TR-IN-' . date('Ymd') . '-';
        $todayCount = StockTransaction::where('reference_no', 'like', "{$prefix}%")->count() + 1;
        $suggestedRef = $prefix . str_pad($todayCount, 3, '0', STR_PAD_LEFT);

        $selectedProductId = $request->input('product_id');

        return view('transactions.create_in', compact('suppliers', 'products', 'suggestedRef', 'selectedProductId'));
    }

    public function createOut(Request $request)
    {
        $products = Product::where('is_active', true)->where('stock', '>', 0)->orderBy('name')->get();

        $prefix = 'TR-OUT-' . date('Ymd') . '-';
        $todayCount = StockTransaction::where('reference_no', 'like', "{$prefix}%")->count() + 1;
        $suggestedRef = $prefix . str_pad($todayCount, 3, '0', STR_PAD_LEFT);

        $selectedProductId = $request->input('product_id');

        return view('transactions.create_out', compact('products', 'suggestedRef', 'selectedProductId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => ['required', 'in:in,out'],
            'reference_no' => ['required', 'string', 'max:50', 'unique:stock_transactions,reference_no'],
            'transaction_date' => ['required', 'date'],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'recipient' => ['nullable', 'string', 'max:150'],
            'notes' => ['nullable', 'string', 'max:500'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ], [
            'reference_no.unique' => 'Nomor referensi ini sudah digunakan.',
            'items.required' => 'Minimal harus menambahkan satu produk pada transaksi ini.',
            'items.*.quantity.min' => 'Jumlah barang minimal 1.',
        ]);

        $type = $validated['type'];
        $itemsData = $validated['items'];

        // If Barang Keluar (out), validate that stock is sufficient for all items
        if ($type === 'out') {
            foreach ($itemsData as $index => $item) {
                $product = Product::find($item['product_id']);
                if (!$product || $product->stock < $item['quantity']) {
                    throw ValidationException::withMessages([
                        "items.{$index}.quantity" => "Stok untuk '{$product->name}' tidak mencukupi (Tersedia: {$product->stock} {$product->unit}, Diminta: {$item['quantity']})."
                    ]);
                }
            }
        }

        $transaction = DB::transaction(function () use ($validated, $type, $itemsData) {
            $totalItems = 0;
            $totalAmount = 0;

            foreach ($itemsData as $item) {
                $qty = (int) $item['quantity'];
                $price = (float) $item['unit_price'];
                $totalItems += $qty;
                $totalAmount += ($qty * $price);
            }

            $tx = StockTransaction::create([
                'reference_no' => $validated['reference_no'],
                'type' => $type,
                'transaction_date' => $validated['transaction_date'],
                'supplier_id' => $type === 'in' ? ($validated['supplier_id'] ?? null) : null,
                'recipient' => $type === 'out' ? ($validated['recipient'] ?? 'Tujuan Internal') : null,
                'user_id' => Auth::id(),
                'total_items' => $totalItems,
                'total_amount' => $totalAmount,
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($itemsData as $item) {
                $qty = (int) $item['quantity'];
                $price = (float) $item['unit_price'];

                StockTransactionItem::create([
                    'stock_transaction_id' => $tx->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $qty,
                    'unit_price' => $price,
                    'subtotal' => $qty * $price,
                    'notes' => $item['notes'] ?? null,
                ]);

                // Update product stock
                $product = Product::find($item['product_id']);
                if ($type === 'in') {
                    $product->increment('stock', $qty);
                } else {
                    $product->decrement('stock', $qty);
                }
            }

            return $tx;
        });

        $typeLabel = $type === 'in' ? 'Barang Masuk' : 'Barang Keluar';

        return redirect()->route('transactions.show', $transaction)
            ->with('success', "Transaksi {$typeLabel} dengan No. {$transaction->reference_no} berhasil disimpan!");
    }

    public function show(StockTransaction $transaction)
    {
        $transaction->load(['supplier', 'user', 'items.product.category']);
        return view('transactions.show', compact('transaction'));
    }

    public function print(StockTransaction $transaction)
    {
        $transaction->load(['supplier', 'user', 'items.product.category']);
        return view('transactions.print', compact('transaction'));
    }
}
