<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockAdjustment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockAdjustmentController extends Controller
{
    public function index(Request $request)
    {
        $query = StockAdjustment::with(['product.category', 'user']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_no', 'like', "%{$search}%")
                  ->orWhere('reason', 'like', "%{$search}%")
                  ->orWhereHas('product', function ($pq) use ($search) {
                      $pq->where('name', 'like', "%{$search}%")
                         ->orWhere('sku', 'like', "%{$search}%");
                  });
            });
        }

        $adjustments = $query->latest('adjustment_date')->latest('id')->paginate(15)->withQueryString();

        return view('adjustments.index', compact('adjustments'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();

        $prefix = 'ADJ-' . date('Ymd') . '-';
        $todayCount = StockAdjustment::where('reference_no', 'like', "{$prefix}%")->count() + 1;
        $suggestedRef = $prefix . str_pad($todayCount, 3, '0', STR_PAD_LEFT);

        return view('adjustments.create', compact('products', 'suggestedRef'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'reference_no' => ['required', 'string', 'max:50', 'unique:stock_adjustments,reference_no'],
            'product_id' => ['required', 'exists:products,id'],
            'physical_stock' => ['required', 'integer', 'min:0'],
            'reason' => ['required', 'string', 'max:150'],
            'adjustment_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
        ], [
            'reference_no.unique' => 'Nomor referensi opname sudah digunakan.',
            'product_id.required' => 'Pilih produk yang akan disesuaikan.',
            'physical_stock.required' => 'Jumlah stok fisik aktual wajib diisi.',
            'reason.required' => 'Alasan penyesuaian wajib dipilih / diisi.',
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $systemStock = $product->stock;
        $physicalStock = (int) $validated['physical_stock'];
        $diff = $physicalStock - $systemStock;

        DB::transaction(function () use ($validated, $product, $systemStock, $physicalStock, $diff) {
            StockAdjustment::create([
                'reference_no' => $validated['reference_no'],
                'product_id' => $product->id,
                'user_id' => Auth::id(),
                'system_stock' => $systemStock,
                'physical_stock' => $physicalStock,
                'difference' => $diff,
                'reason' => $validated['reason'],
                'adjustment_date' => $validated['adjustment_date'],
                'notes' => $validated['notes'] ?? null,
            ]);

            // Update product stock to match physical stock
            $product->update(['stock' => $physicalStock]);
        });

        $diffText = ($diff >= 0 ? "+{$diff}" : "{$diff}") . " {$product->unit}";

        return redirect()->route('adjustments.index')
            ->with('success', "Penyesuaian stok produk '{$product->name}' berhasil disimpan! Selisih stok: {$diffText}.");
    }
}
