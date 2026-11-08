<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockTransaction;
use App\Models\StockTransactionItem;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');

        // Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        // Category Filter
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        // Stock Status Filter
        if ($request->filled('stock_status')) {
            $status = $request->input('stock_status');
            if ($status === 'out') {
                $query->where('stock', '<=', 0);
            } elseif ($status === 'low') {
                $query->where('stock', '>', 0)->whereColumn('stock', '<=', 'min_stock');
            } elseif ($status === 'safe') {
                $query->whereColumn('stock', '>', 'min_stock');
            }
        }

        // Sorting
        $sort = $request->input('sort', 'newest');
        switch ($sort) {
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'stock_asc':
                $query->orderBy('stock', 'asc');
                break;
            case 'stock_desc':
                $query->orderBy('stock', 'desc');
                break;
            case 'price_desc':
                $query->orderBy('purchase_price', 'desc');
                break;
            default:
                $query->latest('id');
                break;
        }

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::orderBy('name')->get();

        // Metrics for summary badges on top of index
        $totalItemsCount = Product::count();
        $lowStockCount = Product::lowStock()->count();
        $outOfStockCount = Product::where('stock', '<=', 0)->count();

        return view('products.index', compact(
            'products',
            'categories',
            'totalItemsCount',
            'lowStockCount',
            'outOfStockCount'
        ));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        // Generate auto SKU
        $latestId = (Product::max('id') ?? 0) + 1;
        $suggestedSku = 'SF-' . str_pad($latestId, 4, '0', STR_PAD_LEFT);

        return view('products.create', compact('categories', 'suggestedSku'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'sku' => ['required', 'string', 'max:50', 'unique:products,sku'],
            'barcode' => ['nullable', 'string', 'max:50'],
            'name' => ['required', 'string', 'max:200'],
            'unit' => ['required', 'string', 'max:30'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['nullable', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'min_stock' => ['required', 'integer', 'min:0'],
            'location' => ['nullable', 'string', 'max:100'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'description' => ['nullable', 'string'],
        ], [
            'category_id.required' => 'Kategori produk wajib dipilih.',
            'sku.required' => 'Kode SKU produk wajib diisi.',
            'sku.unique' => 'Kode SKU sudah terdaftar, gunakan kode lain.',
            'name.required' => 'Nama produk wajib diisi.',
            'unit.required' => 'Satuan produk wajib diisi.',
            'purchase_price.required' => 'Harga beli wajib diisi.',
            'stock.required' => 'Jumlah stok awal wajib diisi.',
            'min_stock.required' => 'Ambang batas stok minimum wajib diisi.',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $validated['image'] = $path;
        }

        $validated['selling_price'] = $validated['selling_price'] ?? $validated['purchase_price'];

        $product = Product::create($validated);

        return redirect()->route('products.index')
            ->with('success', "Produk '{$product->name}' berhasil ditambahkan ke inventaris!");
    }

    public function show(Product $product)
    {
        $product->load(['category', 'adjustments.user']);

        // Riwayat transaksi produk ini
        $transactionItems = StockTransactionItem::with(['transaction.supplier', 'transaction.user'])
            ->where('product_id', $product->id)
            ->latest('id')
            ->take(15)
            ->get();

        $suppliers = Supplier::orderBy('name')->get();

        return view('products.show', compact('product', 'transactionItems', 'suppliers'));
    }

    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();
        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'sku' => ['required', 'string', 'max:50', Rule::unique('products', 'sku')->ignore($product->id)],
            'barcode' => ['nullable', 'string', 'max:50'],
            'name' => ['required', 'string', 'max:200'],
            'unit' => ['required', 'string', 'max:30'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['nullable', 'numeric', 'min:0'],
            'min_stock' => ['required', 'integer', 'min:0'],
            'location' => ['nullable', 'string', 'max:100'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        if ($request->hasFile('image')) {
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $validated['is_active'] = $request->boolean('is_active', true);
        $product->update($validated);

        return redirect()->route('products.show', $product)
            ->with('success', "Informasi produk '{$product->name}' berhasil diperbarui.");
    }

    public function destroy(Product $product)
    {
        // Cek apakah produk memiliki riwayat transaksi
        $hasTransactions = StockTransactionItem::where('product_id', $product->id)->exists();

        if ($hasTransactions) {
            return back()->with('error', "Produk '{$product->name}' tidak dapat dihapus karena telah memiliki riwayat transaksi stok. Anda dapat menonaktifkannya melalui menu edit.");
        }

        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('products.index')
            ->with('success', "Produk berhasil dihapus dari sistem.");
    }

    public function quickRestock(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $quantity = (int) $request->input('quantity');

        DB::transaction(function () use ($product, $quantity, $request) {
            $refNo = 'TR-IN-' . date('Ymd') . '-' . str_pad(rand(100, 999), 3, '0', STR_PAD_LEFT);

            $transaction = StockTransaction::create([
                'reference_no' => $refNo,
                'type' => 'in',
                'transaction_date' => now()->format('Y-m-d'),
                'supplier_id' => $request->input('supplier_id'),
                'recipient' => null,
                'user_id' => Auth::id(),
                'total_items' => $quantity,
                'total_amount' => $quantity * $product->purchase_price,
                'notes' => $request->input('notes') ?? 'Quick restock langsung dari halaman produk.',
            ]);

            StockTransactionItem::create([
                'stock_transaction_id' => $transaction->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_price' => $product->purchase_price,
                'subtotal' => $quantity * $product->purchase_price,
                'notes' => 'Restock',
            ]);

            $product->increment('stock', $quantity);
        });

        return back()->with('success', "Stok produk '{$product->name}' berhasil ditambah sebanyak +{$quantity} {$product->unit}!");
    }
}
