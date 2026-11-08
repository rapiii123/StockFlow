@extends('layouts.app')

@section('title', 'Detail Produk - ' . $product->name)

@section('content')
<div class="mb-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
    <div>
        <a href="{{ route('products.index') }}" class="text-decoration-none small text-muted d-inline-flex align-items-center gap-1 mb-2">
            <i class="bi bi-arrow-left"></i> Kembali ke Daftar Barang
        </a>
        <div class="d-flex align-items-center gap-2">
            <h3 class="fw-bold m-0 text-dark">{{ $product->name }}</h3>
            @php $status = $product->stock_status; @endphp
            <span class="badge-soft-{{ $status['class'] }}">
                <i class="bi {{ $status['icon'] }} me-1"></i>{{ $status['label'] }}
            </span>
        </div>
        <div class="text-muted small mt-1">
            <code>SKU: {{ $product->sku }}</code>
            @if($product->barcode) • <i class="bi bi-upc-scan"></i> Barcode: {{ $product->barcode }} @endif
            • Kategori: <span class="fw-medium text-dark">{{ $product->category->name ?? '-' }}</span>
        </div>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-success d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#quickRestockModal">
            <i class="bi bi-plus-circle"></i>
            <span>Restock Cepat</span>
        </button>
        <a href="{{ route('products.edit', $product) }}" class="btn btn-outline-primary d-inline-flex align-items-center gap-1">
            <i class="bi bi-pencil"></i>
            <span>Edit</span>
        </a>
        <form action="{{ route('products.destroy', $product) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?');" class="d-inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline-danger" title="Hapus Produk">
                <i class="bi bi-trash"></i>
            </button>
        </form>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Left Column: Specs & Info -->
    <div class="col-12 col-lg-5">
        <div class="card-custom p-4 mb-4">
            <div class="text-center mb-4">
                @if($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="rounded-3 border img-fluid mb-3" style="max-height: 220px; object-fit: contain;">
                @else
                    <div class="bg-light border rounded-3 p-4 d-flex flex-column align-items-center justify-content-center text-muted mb-3" style="height: 180px;">
                        <i class="bi bi-box-seam" style="font-size: 3.5rem;"></i>
                        <span class="small mt-2">Tidak ada foto produk</span>
                    </div>
                @endif
                <h5 class="fw-bold mb-1">{{ $product->name }}</h5>
                <span class="badge bg-light text-secondary border font-monospace">{{ $product->sku }}</span>
            </div>

            <div class="list-group list-group-flush border-top border-bottom small mb-3">
                <div class="list-group-item d-flex justify-content-between px-0 py-2">
                    <span class="text-muted">Kategori</span>
                    <span class="fw-semibold text-dark">{{ $product->category->name ?? '-' }}</span>
                </div>
                <div class="list-group-item d-flex justify-content-between px-0 py-2">
                    <span class="text-muted">Lokasi Penempatan Rak</span>
                    <span class="fw-semibold text-primary"><i class="bi bi-geo-alt me-1"></i>{{ $product->location ?? 'Belum Diatur' }}</span>
                </div>
                <div class="list-group-item d-flex justify-content-between px-0 py-2">
                    <span class="text-muted">Satuan Unit</span>
                    <span class="fw-semibold text-dark">{{ $product->unit }}</span>
                </div>
                <div class="list-group-item d-flex justify-content-between px-0 py-2">
                    <span class="text-muted">Harga Beli Satuan</span>
                    <span class="fw-semibold text-dark">Rp {{ number_format($product->purchase_price, 0, ',', '.') }}</span>
                </div>
                <div class="list-group-item d-flex justify-content-between px-0 py-2">
                    <span class="text-muted">Harga Jual / Nilai Keluar</span>
                    <span class="fw-semibold text-dark">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</span>
                </div>
                <div class="list-group-item d-flex justify-content-between px-0 py-2">
                    <span class="text-muted">Estimasi Nilai Total Stok</span>
                    <span class="fw-bold text-success">Rp {{ number_format($product->stock * $product->purchase_price, 0, ',', '.') }}</span>
                </div>
            </div>

            @if($product->description)
                <div>
                    <h6 class="fw-bold text-dark small mb-1">Deskripsi / Keterangan:</h6>
                    <p class="text-muted small mb-0">{{ $product->description }}</p>
                </div>
            @endif
        </div>

        <!-- Stock Health Card -->
        <div class="card-custom p-4">
            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-activity text-primary me-2"></i>Kondisi Stok</h6>
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small">Sisa Stok Fisik:</span>
                <span class="fs-4 fw-bold {{ $product->stock <= 0 ? 'text-danger' : ($product->stock <= $product->min_stock ? 'text-warning' : 'text-success') }}">
                    {{ number_format($product->stock) }} {{ $product->unit }}
                </span>
            </div>
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="text-muted small">Ambang Batas Minimum:</span>
                <span class="badge bg-secondary-subtle text-secondary">{{ $product->min_stock }} {{ $product->unit }}</span>
            </div>

            @php
                $percent = $product->min_stock > 0 ? min(100, round(($product->stock / ($product->min_stock * 2)) * 100)) : 100;
                $barColor = $product->stock <= 0 ? 'bg-danger' : ($product->stock <= $product->min_stock ? 'bg-warning' : 'bg-success');
            @endphp
            <div class="progress mb-2" style="height: 10px;">
                <div class="progress-bar {{ $barColor }}" role="progressbar" style="width: {{ $percent }}%;"></div>
            </div>
            <div class="text-muted small" style="font-size: 0.75rem;">
                @if($product->stock <= 0)
                    <span class="text-danger fw-bold"><i class="bi bi-x-circle me-1"></i>Stok saat ini kosong! Segera lakukan pemesanan ke supplier.</span>
                @elseif($product->stock <= $product->min_stock)
                    <span class="text-warning fw-bold"><i class="bi bi-exclamation-triangle me-1"></i>Stok berada di bawah batas minimum (Perlu restock).</span>
                @else
                    <span class="text-success"><i class="bi bi-check-circle me-1"></i>Jumlah stok dalam kondisi aman.</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Right Column: Transactions & Adjustments History -->
    <div class="col-12 col-lg-7">
        <div class="card-custom mb-4">
            <div class="p-3 p-md-4 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="fw-bold text-dark m-0 d-flex align-items-center gap-2">
                    <i class="bi bi-clock-history text-primary"></i>
                    Riwayat Transaksi Produk
                </h6>
                <span class="badge bg-light text-muted border">15 Terakhir</span>
            </div>

            @if($transactionItems->count() > 0)
            <div class="table-responsive">
                <table class="table table-custom align-middle mb-0 table-hover">
                    <thead>
                        <tr>
                            <th>No. Referensi</th>
                            <th>Jenis</th>
                            <th>Tanggal</th>
                            <th>Pihak / Rekanan</th>
                            <th class="text-center">Kuantitas</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactionItems as $item)
                        @php $tx = $item->transaction; @endphp
                        <tr>
                            <td>
                                <a href="{{ route('transactions.show', $tx) }}" class="fw-bold text-dark text-decoration-none">
                                    <code>{{ $tx->reference_no }}</code>
                                </a>
                            </td>
                            <td>
                                @if($tx->type === 'in')
                                    <span class="badge-soft-success"><i class="bi bi-arrow-down me-1"></i>Masuk</span>
                                @else
                                    <span class="badge-soft-danger"><i class="bi bi-arrow-up me-1"></i>Keluar</span>
                                @endif
                            </td>
                            <td>
                                <span class="small">{{ $tx->transaction_date->translatedFormat('d/m/Y') }}</span>
                            </td>
                            <td>
                                <span class="small text-muted">
                                    {{ $tx->type === 'in' ? ($tx->supplier->name ?? 'Pemasok') : ($tx->recipient ?? 'Internal') }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="fw-bold {{ $tx->type === 'in' ? 'text-success' : 'text-danger' }}">
                                    {{ $tx->type === 'in' ? '+' : '-' }}{{ $item->quantity }}
                                </span>
                            </td>
                            <td class="text-end">
                                <span class="small fw-semibold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-5">
                <i class="bi bi-clock text-muted fs-1"></i>
                <p class="text-muted small mt-2 mb-0">Belum ada riwayat transaksi untuk barang ini.</p>
            </div>
            @endif
        </div>

        <!-- Opname / Penyesuaian Terakhir -->
        @if($product->adjustments->count() > 0)
        <div class="card-custom">
            <div class="p-3 border-bottom">
                <h6 class="fw-bold text-dark m-0"><i class="bi bi-clipboard-check text-info me-2"></i>Riwayat Stock Opname</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-custom align-middle mb-0">
                    <thead>
                        <tr>
                            <th>No. Referensi</th>
                            <th>Tanggal</th>
                            <th>Stok Sistem</th>
                            <th>Fisik Aktual</th>
                            <th>Selisih</th>
                            <th>Alasan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($product->adjustments as $adj)
                        <tr>
                            <td><code>{{ $adj->reference_no }}</code></td>
                            <td>{{ $adj->adjustment_date->format('d/m/Y') }}</td>
                            <td>{{ $adj->system_stock }}</td>
                            <td>{{ $adj->physical_stock }}</td>
                            <td>
                                <span class="fw-bold {{ $adj->difference >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $adj->difference >= 0 ? '+' : '' }}{{ $adj->difference }}
                                </span>
                            </td>
                            <td><span class="small text-muted">{{ $adj->reason }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Modal Quick Restock -->
<div class="modal fade" id="quickRestockModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('products.quick-restock', $product) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-plus-circle text-success me-1"></i> Restock Cepat: {{ $product->name }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="p-3 bg-light rounded-3 mb-3">
                        <div class="small text-muted">SKU: <code>{{ $product->sku }}</code></div>
                        <div class="small text-muted">Stok Saat Ini: <strong class="text-dark">{{ $product->stock }} {{ $product->unit }}</strong></div>
                        <div class="small text-muted">Harga Beli: <strong>Rp {{ number_format($product->purchase_price, 0, ',', '.') }}</strong></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Pilih Pemasok (Opsional)</label>
                        <select name="supplier_id" class="form-select">
                            <option value="">-- Pemasok Umum / Bawaan --</option>
                            @foreach($suppliers as $sup)
                                <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Jumlah Penambahan Stok ({{ $product->unit }}) <span class="text-danger">*</span></label>
                        <input type="number" name="quantity" class="form-control" min="1" value="10" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Catatan Penerimaan (Opsional)</label>
                        <input type="text" name="notes" class="form-control" placeholder="Restock cepat via halaman detail produk">
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
@endsection
