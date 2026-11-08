@extends('layouts.app')

@section('title', 'Katalog Barang & Inventaris')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold m-0 text-dark">Data Barang & Inventaris</h3>
        <p class="text-muted small m-0">Kelola informasi barang, lokasi penempatan rak, dan status stok gudang.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('products.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-2 shadow-sm">
            <i class="bi bi-plus-lg"></i>
            <span>Tambah Barang Baru</span>
        </a>
    </div>
</div>

<!-- Mini Stats Overview -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-4">
        <div class="card-custom p-3 d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small fw-semibold">Total Item Terdaftar</span>
                <h4 class="fw-bold text-dark m-0">{{ $totalItemsCount }}</h4>
            </div>
            <div class="stat-icon bg-light-blue" style="width: 40px; height: 40px; font-size: 1.25rem;">
                <i class="bi bi-box-seam"></i>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-4">
        <div class="card-custom p-3 d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small fw-semibold">Stok Menipis (Warning)</span>
                <h4 class="fw-bold text-warning m-0">{{ $lowStockCount }}</h4>
            </div>
            <div class="stat-icon bg-light-yellow" style="width: 40px; height: 40px; font-size: 1.25rem;">
                <i class="bi bi-exclamation-triangle"></i>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-4">
        <div class="card-custom p-3 d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small fw-semibold">Stok Kosong (Habis)</span>
                <h4 class="fw-bold text-danger m-0">{{ $outOfStockCount }}</h4>
            </div>
            <div class="stat-icon bg-light-red" style="width: 40px; height: 40px; font-size: 1.25rem;">
                <i class="bi bi-x-circle"></i>
            </div>
        </div>
    </div>
</div>

<!-- Search & Filter Card -->
<div class="card-custom p-3 mb-4">
    <form action="{{ route('products.index') }}" method="GET" class="row g-2 align-items-center">
        <div class="col-12 col-md-4">
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" name="search" class="form-control border-start-0" placeholder="Cari nama barang, SKU, barcode, lokasi..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-6 col-md-3">
            <select name="category_id" class="form-select">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-md-2">
            <select name="stock_status" class="form-select">
                <option value="">Status Stok</option>
                <option value="safe" {{ request('stock_status') == 'safe' ? 'selected' : '' }}>Stok Aman</option>
                <option value="low" {{ request('stock_status') == 'low' ? 'selected' : '' }}>Menipis (≤ Min)</option>
                <option value="out" {{ request('stock_status') == 'out' ? 'selected' : '' }}>Habis (0)</option>
            </select>
        </div>
        <div class="col-6 col-md-2">
            <select name="sort" class="form-select">
                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Terbaru</option>
                <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Nama A - Z</option>
                <option value="stock_asc" {{ request('sort') == 'stock_asc' ? 'selected' : '' }}>Stok Terendah</option>
                <option value="stock_desc" {{ request('sort') == 'stock_desc' ? 'selected' : '' }}>Stok Terbanyak</option>
                <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Harga Tertinggi</option>
            </select>
        </div>
        <div class="col-6 col-md-1 d-flex gap-1">
            <button type="submit" class="btn btn-primary w-100" title="Terapkan Filter">
                <i class="bi bi-funnel-fill"></i>
            </button>
            @if(request()->anyFilled(['search', 'category_id', 'stock_status', 'sort']))
                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary" title="Reset Filter">
                    <i class="bi bi-x-lg"></i>
                </a>
            @endif
        </div>
    </form>
</div>

<!-- Table Card -->
<div class="card-custom">
    @if($products->count() > 0)
    <div class="table-responsive">
        <table class="table table-custom align-middle mb-0 table-hover">
            <thead>
                <tr>
                    <th>SKU & Gambar</th>
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th class="text-end">Harga Beli</th>
                    <th class="text-center">Sisa Stok</th>
                    <th class="text-center">Min. Stok</th>
                    <th>Lokasi Rak</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $prod)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            @if($prod->image)
                                <img src="{{ asset('storage/' . $prod->image) }}" alt="{{ $prod->name }}" class="rounded border object-fit-cover" style="width: 40px; height: 40px;">
                            @else
                                <div class="rounded bg-light border d-flex align-items-center justify-content-center text-muted" style="width: 40px; height: 40px;">
                                    <i class="bi bi-box-seam fs-5"></i>
                                </div>
                            @endif
                            <div>
                                <span class="fw-bold text-dark font-monospace" style="font-size: 0.825rem;">{{ $prod->sku }}</span>
                                @if($prod->barcode)
                                    <div class="text-muted small" style="font-size: 0.75rem;">
                                        <i class="bi bi-upc-scan"></i> {{ $prod->barcode }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>
                        <a href="{{ route('products.show', $prod) }}" class="fw-semibold text-dark text-decoration-none">
                            {{ $prod->name }}
                        </a>
                        <div class="text-muted small">Satuan: {{ $prod->unit }}</div>
                    </td>
                    <td>
                        <span class="badge bg-light text-dark border">{{ $prod->category->name ?? '-' }}</span>
                    </td>
                    <td class="text-end">
                        <span class="fw-semibold">Rp {{ number_format($prod->purchase_price, 0, ',', '.') }}</span>
                    </td>
                    <td class="text-center">
                        <span class="fs-6 fw-bold {{ $prod->stock <= 0 ? 'text-danger' : ($prod->stock <= $prod->min_stock ? 'text-warning' : 'text-success') }}">
                            {{ number_format($prod->stock) }}
                        </span>
                        <span class="small text-muted">{{ $prod->unit }}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-secondary-subtle text-secondary">{{ $prod->min_stock }} {{ $prod->unit }}</span>
                    </td>
                    <td>
                        <small class="text-muted"><i class="bi bi-geo-alt me-1"></i>{{ $prod->location ?? '-' }}</small>
                    </td>
                    <td>
                        @php $status = $prod->stock_status; @endphp
                        <span class="badge-soft-{{ $status['class'] }}">
                            <i class="bi {{ $status['icon'] }} me-1"></i>{{ $status['label'] }}
                        </span>
                    </td>
                    <td class="text-end">
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#quickRestockModal{{ $prod->id }}" title="Restock Cepat">
                                <i class="bi bi-plus-lg"></i>
                            </button>
                            <a href="{{ route('products.show', $prod) }}" class="btn btn-outline-primary" title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('products.edit', $prod) }}" class="btn btn-outline-secondary" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                        </div>
                    </td>
                </tr>

                <!-- Quick Restock Modal -->
                <div class="modal fade" id="quickRestockModal{{ $prod->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow">
                            <form action="{{ route('products.quick-restock', $prod) }}" method="POST">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title fw-bold">
                                        <i class="bi bi-plus-circle text-success me-1"></i> Restock Cepat: {{ $prod->name }}
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="p-3 bg-light rounded-3 mb-3">
                                        <div class="small text-muted">SKU: <code>{{ $prod->sku }}</code> | Stok Saat Ini: <strong class="text-dark">{{ $prod->stock }} {{ $prod->unit }}</strong></div>
                                        <div class="small text-muted">Harga Beli: <strong>Rp {{ number_format($prod->purchase_price, 0, ',', '.') }}</strong></div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold small">Jumlah Penambahan Stok ({{ $prod->unit }})</label>
                                        <input type="number" name="quantity" class="form-control" min="1" value="10" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold small">Catatan (Opsional)</label>
                                        <input type="text" name="notes" class="form-control" placeholder="Restock langsung melalui tabel inventaris">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-success fw-semibold">
                                        <i class="bi bi-check-lg me-1"></i> Tambahkan Stok
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="p-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="text-muted small">
            Menampilkan {{ $products->firstItem() ?? 0 }} - {{ $products->lastItem() ?? 0 }} dari {{ $products->total() }} barang
        </div>
        <div>
            {{ $products->links('pagination::bootstrap-5') }}
        </div>
    </div>
    @else
    <div class="text-center py-5">
        <i class="bi bi-search text-muted" style="font-size: 3rem;"></i>
        <h6 class="fw-bold text-dark mt-2">Tidak Ditemukan Barang yang Sesuai</h6>
        <p class="text-muted small mb-3">Coba ubah kata kunci pencarian atau reset filter yang dipilih.</p>
        <a href="{{ route('products.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i> Tambah Barang Baru
        </a>
    </div>
    @endif
</div>
@endsection
