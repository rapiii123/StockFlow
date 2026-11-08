@extends('layouts.app')

@section('title', 'Laporan & Rekapitulasi Inventaris')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold m-0 text-dark">Laporan & Rekapitulasi Inventaris</h3>
        <p class="text-muted small m-0">Ringkasan transaksi keluar-masuk barang dan valuasi aset stok gudang.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('reports.export-csv', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-outline-success d-inline-flex align-items-center gap-2">
            <i class="bi bi-file-earmark-excel"></i>
            <span>Ekspor CSV (Excel)</span>
        </a>
        <button onclick="window.print()" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2">
            <i class="bi bi-printer"></i>
            <span>Cetak Laporan</span>
        </button>
    </div>
</div>

<!-- Filter Periode -->
<div class="card-custom p-3 mb-4 no-print">
    <form action="{{ route('reports.index') }}" method="GET" class="row g-2 align-items-center">
        <div class="col-12 col-md-3">
            <label class="form-label small text-muted mb-0">Dari Tanggal:</label>
            <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
        </div>
        <div class="col-12 col-md-3">
            <label class="form-label small text-muted mb-0">Sampai Tanggal:</label>
            <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
        </div>
        <div class="col-12 col-md-4">
            <label class="form-label small text-muted mb-0">Filter Kategori Barang:</label>
            <select name="category_id" class="form-select">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-12 col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-filter me-1"></i> Tampilkan
            </button>
        </div>
    </form>
</div>

<!-- Summary Period Stats -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card-custom p-3">
            <span class="text-muted small fw-semibold">Total Item Masuk</span>
            <h4 class="fw-bold text-success my-1">+{{ number_format($incomingTotal) }}</h4>
            <span class="small text-muted">Periode terpilih</span>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card-custom p-3">
            <span class="text-muted small fw-semibold">Nilai Pengadaan (Masuk)</span>
            <h4 class="fw-bold text-success my-1">Rp {{ number_format($incomingValue, 0, ',', '.') }}</h4>
            <span class="small text-muted">Total biaya masuk</span>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card-custom p-3">
            <span class="text-muted small fw-semibold">Total Item Keluar</span>
            <h4 class="fw-bold text-danger my-1">-{{ number_format($outgoingTotal) }}</h4>
            <span class="small text-muted">Periode terpilih</span>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card-custom p-3">
            <span class="text-muted small fw-semibold">Nilai Pengeluaran</span>
            <h4 class="fw-bold text-danger my-1">Rp {{ number_format($outgoingValue, 0, ',', '.') }}</h4>
            <span class="small text-muted">Total nilai keluar</span>
        </div>
    </div>
</div>

<!-- Tabs Nav -->
<ul class="nav nav-tabs mb-3" id="reportTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active fw-semibold" id="stock-tab" data-bs-toggle="tab" data-bs-target="#stockTabContent" type="button" role="tab">
            <i class="bi bi-box-seam me-1"></i> Laporan Stok Barang & Valuasi Aset
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link fw-semibold" id="tx-tab" data-bs-toggle="tab" data-bs-target="#txTabContent" type="button" role="tab">
            <i class="bi bi-clock-history me-1"></i> Rincian Mutasi Transaksi ({{ $transactions->count() }})
        </button>
    </li>
</ul>

<div class="tab-content" id="reportTabsContent">
    <!-- Tab 1: Current Stock & Valuation -->
    <div class="tab-pane fade show active" id="stockTabContent" role="tabpanel">
        <div class="card-custom">
            <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="fw-bold text-dark m-0">Kondisi Stok & Nilai Inventaris Gudang Saat Ini</h6>
                <span class="badge bg-light text-dark border">{{ $products->count() }} Produk</span>
            </div>
            <div class="table-responsive">
                <table class="table table-custom align-middle mb-0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode SKU</th>
                            <th>Nama Barang</th>
                            <th>Kategori</th>
                            <th>Lokasi Rak</th>
                            <th class="text-center">Stok</th>
                            <th class="text-end">Harga Beli</th>
                            <th class="text-end">Total Valuasi Stok</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $grandValuation = 0; @endphp
                        @foreach($products as $idx => $p)
                        @php
                            $val = $p->stock * $p->purchase_price;
                            $grandValuation += $val;
                            $st = $p->stock_status;
                        @endphp
                        <tr>
                            <td class="text-muted small">{{ $idx + 1 }}</td>
                            <td><code>{{ $p->sku }}</code></td>
                            <td>
                                <a href="{{ route('products.show', $p) }}" class="fw-semibold text-dark text-decoration-none">
                                    {{ $p->name }}
                                </a>
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ $p->category->name ?? '-' }}</span></td>
                            <td><small class="text-muted">{{ $p->location ?? '-' }}</small></td>
                            <td class="text-center fw-bold">{{ number_format($p->stock) }} {{ $p->unit }}</td>
                            <td class="text-end">Rp {{ number_format($p->purchase_price, 0, ',', '.') }}</td>
                            <td class="text-end fw-bold">Rp {{ number_format($val, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge-soft-{{ $st['class'] }}">{{ $st['label'] }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="border-top-2 table-light">
                        <tr class="fw-bold">
                            <td colspan="7" class="text-end">TOTAL NILAI KESELURUHAN ASET GUDANG:</td>
                            <td class="text-end text-primary fs-6">Rp {{ number_format($grandValuation, 0, ',', '.') }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Tab 2: Transactions Log -->
    <div class="tab-pane fade" id="txTabContent" role="tabpanel">
        <div class="card-custom">
            <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="fw-bold text-dark m-0">Rincian Transaksi Periode {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</h6>
            </div>
            @if($transactions->count() > 0)
            <div class="table-responsive">
                <table class="table table-custom align-middle mb-0">
                    <thead>
                        <tr>
                            <th>No. Referensi</th>
                            <th>Jenis</th>
                            <th>Tanggal</th>
                            <th>Mitra / Tujuan</th>
                            <th>Rincian Barang</th>
                            <th class="text-center">Total Kuantitas</th>
                            <th class="text-end">Nilai Transaksi</th>
                            <th>Petugas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $tx)
                        <tr>
                            <td>
                                <a href="{{ route('transactions.show', $tx) }}" class="fw-bold text-dark text-decoration-none">
                                    <code>{{ $tx->reference_no }}</code>
                                </a>
                            </td>
                            <td>
                                <span class="badge-soft-{{ $tx->type_badge }}">
                                    {{ $tx->type_label }}
                                </span>
                            </td>
                            <td>{{ $tx->transaction_date->format('d/m/Y') }}</td>
                            <td>
                                <span class="small fw-medium">{{ $tx->type === 'in' ? ($tx->supplier->name ?? 'Pemasok') : ($tx->recipient ?? 'Internal') }}</span>
                            </td>
                            <td>
                                <small class="text-muted">
                                    @foreach($tx->items->take(2) as $it)
                                        <div>• {{ $it->product->name ?? 'Barang' }} ({{ $it->quantity }} {{ $it->product->unit ?? '' }})</div>
                                    @endforeach
                                    @if($tx->items->count() > 2)
                                        <div class="text-primary">+{{ $tx->items->count() - 2 }} barang lainnya...</div>
                                    @endif
                                </small>
                            </td>
                            <td class="text-center fw-bold">{{ number_format($tx->total_items) }}</td>
                            <td class="text-end fw-semibold">Rp {{ number_format($tx->total_amount, 0, ',', '.') }}</td>
                            <td><small class="text-muted">{{ $tx->user->name ?? '-' }}</small></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-5">
                <p class="text-muted mb-0">Tidak ada transaksi dalam rentang tanggal yang dipilih.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
