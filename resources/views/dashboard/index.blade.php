@extends('layouts.app')

@section('title', 'Dashboard Inventaris Gudang')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold m-0 text-dark">Ringkasan Pergudangan & Stok</h3>
        <p class="text-muted small m-0">Pantau arus pergerakan inventaris, status stok menipis, dan transaksi barang secara real-time.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('transactions.create-in') }}" class="btn btn-success d-inline-flex align-items-center gap-2 shadow-sm">
            <i class="bi bi-box-arrow-in-down"></i>
            <span>Catat Barang Masuk</span>
        </a>
        <a href="{{ route('transactions.create-out') }}" class="btn btn-danger d-inline-flex align-items-center gap-2 shadow-sm">
            <i class="bi bi-box-arrow-up-right"></i>
            <span>Catat Barang Keluar</span>
        </a>
    </div>
</div>

<!-- 4 Key Stat Cards required by user -->
<div class="row g-3 mb-4">
    <!-- 1. Total Jenis Barang -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card primary">
            <div>
                <span class="text-muted small fw-semibold text-uppercase tracking-wider">Total Jenis Barang</span>
                <h2 class="fw-bold text-dark my-1">{{ number_format($totalProducts) }}</h2>
                <div class="small text-muted">
                    <span class="text-primary fw-medium"><i class="bi bi-tag-fill me-1"></i>Katalog Aktif</span> di sistem
                </div>
            </div>
            <div class="stat-icon bg-light-blue">
                <i class="bi bi-boxes"></i>
            </div>
        </div>
    </div>

    <!-- 2. Total Keseluruhan Stok -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card info">
            <div>
                <span class="text-muted small fw-semibold text-uppercase tracking-wider">Total Fisik Stok</span>
                <h2 class="fw-bold text-dark my-1">{{ number_format($totalStock) }}</h2>
                <div class="small text-muted">
                    <span class="text-info fw-medium"><i class="bi bi-stack me-1"></i>Unit Barang</span> dalam gudang
                </div>
            </div>
            <div class="stat-icon bg-light-cyan">
                <i class="bi bi-layers"></i>
            </div>
        </div>
    </div>

    <!-- 3. Jumlah Barang Masuk -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card success">
            <div>
                <span class="text-muted small fw-semibold text-uppercase tracking-wider">Total Barang Masuk</span>
                <h2 class="fw-bold text-success my-1">+{{ number_format($totalIncomingQuantity) }}</h2>
                <div class="small text-muted">
                    <span class="fw-medium text-dark">{{ $totalIncomingTransactions }}</span> transaksi penerimaan
                </div>
            </div>
            <div class="stat-icon bg-light-green">
                <i class="bi bi-box-arrow-in-down"></i>
            </div>
        </div>
    </div>

    <!-- 4. Jumlah Barang Keluar -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card danger">
            <div>
                <span class="text-muted small fw-semibold text-uppercase tracking-wider">Total Barang Keluar</span>
                <h2 class="fw-bold text-danger my-1">-{{ number_format($totalOutgoingQuantity) }}</h2>
                <div class="small text-muted">
                    <span class="fw-medium text-dark">{{ $totalOutgoingTransactions }}</span> transaksi pengeluaran
                </div>
            </div>
            <div class="stat-icon bg-light-red">
                <i class="bi bi-box-arrow-up-right"></i>
            </div>
        </div>
    </div>
</div>

<!-- Low Stock Banner Notification if any -->
@if($lowStockCount > 0)
<div class="alert alert-warning border-warning-subtle shadow-sm mb-4 d-flex align-items-center justify-content-between flex-wrap gap-2 py-3 px-4 rounded-3">
    <div class="d-flex align-items-center gap-3">
        <div class="rounded-circle bg-warning text-dark p-2 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
        </div>
        <div>
            <h6 class="fw-bold text-dark mb-0">Peringatan: Terdapat {{ $lowStockCount }} Jenis Barang dengan Stok Menipis/Habis!</h6>
            <span class="text-muted small">Segera lakukan re-order atau restock ke pemasok sebelum proses operasional terhambat.</span>
        </div>
    </div>
    <a href="#sectionLowStock" class="btn btn-sm btn-warning text-dark fw-semibold px-3">
        <i class="bi bi-arrow-down-short"></i> Lihat Daftar Barang
    </a>
</div>
@endif

<!-- Charts Section: Activity (Barang Masuk vs Keluar) + Kategori / Status -->
<div class="row g-3 mb-4">
    <!-- Chart: Aktivitas Barang Masuk dan Keluar -->
    <div class="col-12 col-xl-8">
        <div class="card-custom h-100 p-3 p-md-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h6 class="fw-bold text-dark m-0 d-flex align-items-center gap-2">
                        <i class="bi bi-graph-up text-primary"></i>
                        Grafik Aktivitas Barang Masuk dan Keluar
                    </h6>
                    <small class="text-muted">Pergerakan kuantitas stok dalam 14 hari terakhir</small>
                </div>
                <div class="d-flex align-items-center gap-2 small">
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                        <i class="bi bi-square-fill me-1" style="font-size: 0.6rem;"></i> Masuk
                    </span>
                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                        <i class="bi bi-square-fill me-1" style="font-size: 0.6rem;"></i> Keluar
                    </span>
                </div>
            </div>
            <div style="height: 300px; position: relative;">
                <canvas id="stockActivityChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Chart: Distribusi Kategori / Status Stok -->
    <div class="col-12 col-xl-4">
        <div class="card-custom h-100 p-3 p-md-4 d-flex flex-column">
            <div class="mb-3">
                <h6 class="fw-bold text-dark m-0 d-flex align-items-center gap-2">
                    <i class="bi bi-pie-chart text-info"></i>
                    Status Ketersediaan Stok
                </h6>
                <small class="text-muted">Kondisi kesehatan inventaris saat ini</small>
            </div>
            <div class="flex-grow-1 d-flex align-items-center justify-content-center" style="position: relative; max-height: 220px;">
                <canvas id="stockStatusChart"></canvas>
            </div>
            <div class="mt-3 pt-2 border-top">
                <div class="d-flex justify-content-between align-items-center small py-1">
                    <span class="text-muted"><i class="bi bi-circle-fill text-success me-1"></i>Stok Aman:</span>
                    <span class="fw-bold text-dark">{{ $stockSafeCount }} jenis</span>
                </div>
                <div class="d-flex justify-content-between align-items-center small py-1">
                    <span class="text-muted"><i class="bi bi-circle-fill text-warning me-1"></i>Stok Menipis:</span>
                    <span class="fw-bold text-warning">{{ $stockWarningCount }} jenis</span>
                </div>
                <div class="d-flex justify-content-between align-items-center small py-1">
                    <span class="text-muted"><i class="bi bi-circle-fill text-danger me-1"></i>Stok Habis (Kosong):</span>
                    <span class="fw-bold text-danger">{{ $outOfStockCount }} jenis</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Section: Informasi Stok Menipis -->
<div class="card-custom mb-4" id="sectionLowStock">
    <div class="p-3 p-md-4 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h6 class="fw-bold text-dark m-0 d-flex align-items-center gap-2">
                <i class="bi bi-exclamation-octagon-fill text-danger"></i>
                Informasi Stok Menipis & Peringatan Re-order
            </h6>
            <small class="text-muted">Daftar barang dengan jumlah stok sama atau di bawah batas minimum (min_stock)</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('products.index', ['stock_status' => 'low']) }}" class="btn btn-sm btn-outline-secondary">
                Lihat Semua ({{ $lowStockCount }})
            </a>
            <a href="{{ route('transactions.create-in') }}" class="btn btn-sm btn-primary">
                <i class="bi bi-plus-circle me-1"></i> Buat Penerimaan Stok
            </a>
        </div>
    </div>

    @if($lowStockProducts->count() > 0)
    <div class="table-responsive">
        <table class="table table-custom align-middle mb-0 table-hover">
            <thead>
                <tr>
                    <th>SKU & Produk</th>
                    <th>Kategori</th>
                    <th>Lokasi Rak</th>
                    <th class="text-center">Batas Min.</th>
                    <th class="text-center">Stok Terkini</th>
                    <th>Status</th>
                    <th class="text-end">Aksi Cepat</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lowStockProducts as $prod)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded bg-light border p-1 text-center" style="width: 36px; height: 36px;">
                                <i class="bi bi-box text-secondary fs-5"></i>
                            </div>
                            <div>
                                <a href="{{ route('products.show', $prod) }}" class="fw-semibold text-dark text-decoration-none">
                                    {{ $prod->name }}
                                </a>
                                <div class="text-muted small">
                                    <code>{{ $prod->sku }}</code>
                                    @if($prod->barcode) • <i class="bi bi-upc-scan"></i> {{ $prod->barcode }} @endif
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge bg-light text-dark border">{{ $prod->category->name ?? '-' }}</span>
                    </td>
                    <td>
                        <i class="bi bi-geo-alt text-muted me-1"></i>
                        <span class="small">{{ $prod->location ?? 'Belum ditentukan' }}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-secondary-subtle text-secondary">{{ $prod->min_stock }} {{ $prod->unit }}</span>
                    </td>
                    <td class="text-center">
                        <span class="fw-bold fs-6 {{ $prod->stock == 0 ? 'text-danger' : 'text-warning' }}">
                            {{ $prod->stock }} {{ $prod->unit }}
                        </span>
                    </td>
                    <td>
                        @if($prod->stock == 0)
                            <span class="badge-soft-danger"><i class="bi bi-x-circle me-1"></i>Habis</span>
                        @else
                            <span class="badge-soft-warning"><i class="bi bi-exclamation-triangle me-1"></i>Menipis</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#quickRestockModal{{ $prod->id }}">
                            <i class="bi bi-plus-lg me-1"></i>Restock
                        </button>
                        <a href="{{ route('products.show', $prod) }}" class="btn btn-sm btn-outline-primary" title="Detail Produk">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>

                <!-- Quick Restock Modal for each low stock product -->
                <div class="modal fade" id="quickRestockModal{{ $prod->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow">
                            <form action="{{ route('products.quick-restock', $prod) }}" method="POST">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title fw-bold">
                                        <i class="bi bi-plus-circle text-success me-1"></i> Restock Cepat Produk
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="p-3 bg-light rounded-3 mb-3">
                                        <div class="fw-bold">{{ $prod->name }}</div>
                                        <div class="text-muted small">SKU: {{ $prod->sku }} | Stok Saat Ini: <span class="fw-bold text-danger">{{ $prod->stock }} {{ $prod->unit }}</span> (Min: {{ $prod->min_stock }})</div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold small">Jumlah Tambahan Stok ({{ $prod->unit }})</label>
                                        <input type="number" name="quantity" class="form-control" min="1" value="{{ max(10, $prod->min_stock * 2) }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold small">Catatan Penerimaan (Opsional)</label>
                                        <input type="text" name="notes" class="form-control" placeholder="Restock darurat stok menipis">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-success fw-semibold">
                                        <i class="bi bi-check-lg me-1"></i> Simpan Penambahan Stok
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
    @else
    <div class="text-center py-5">
        <i class="bi bi-shield-check text-success" style="font-size: 3rem;"></i>
        <h6 class="fw-bold text-dark mt-2">Semua Stok Barang Dalam Batas Aman</h6>
        <p class="text-muted small mb-0">Tidak ada produk yang berada pada atau di bawah ambang batas stok minimum.</p>
    </div>
    @endif
</div>

<!-- Section: Ringkasan Transaksi Terbaru -->
<div class="card-custom">
    <div class="p-3 p-md-4 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h6 class="fw-bold text-dark m-0 d-flex align-items-center gap-2">
                <i class="bi bi-clock-history text-primary"></i>
                Ringkasan Transaksi Terbaru
            </h6>
            <small class="text-muted">Mutasi barang masuk dan keluar yang tercatat baru-baru ini</small>
        </div>
        <a href="{{ route('transactions.index') }}" class="btn btn-sm btn-outline-secondary">
            Lihat Semua Transaksi <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>

    @if($recentTransactions->count() > 0)
    <div class="table-responsive">
        <table class="table table-custom align-middle mb-0 table-hover">
            <thead>
                <tr>
                    <th>No. Referensi</th>
                    <th>Jenis Transaksi</th>
                    <th>Tanggal</th>
                    <th>Pihak / Rekanan</th>
                    <th class="text-center">Total Kuantitas</th>
                    <th class="text-end">Estimasi Nilai</th>
                    <th>Petugas</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentTransactions as $tx)
                <tr>
                    <td>
                        <a href="{{ route('transactions.show', $tx) }}" class="fw-bold text-dark text-decoration-none">
                            <code>{{ $tx->reference_no }}</code>
                        </a>
                    </td>
                    <td>
                        @if($tx->type === 'in')
                            <span class="badge-soft-success">
                                <i class="bi bi-box-arrow-in-down me-1"></i>Barang Masuk
                            </span>
                        @else
                            <span class="badge-soft-danger">
                                <i class="bi bi-box-arrow-up-right me-1"></i>Barang Keluar
                            </span>
                        @endif
                    </td>
                    <td>
                        <span class="small">{{ $tx->transaction_date->translatedFormat('d M Y') }}</span>
                    </td>
                    <td>
                        <div class="small fw-medium text-dark">
                            @if($tx->type === 'in')
                                <i class="bi bi-truck text-muted me-1"></i>{{ $tx->supplier->name ?? 'Pemasok Umum' }}
                            @else
                                <i class="bi bi-send text-muted me-1"></i>{{ $tx->recipient ?? 'Pengeluaran Internal' }}
                            @endif
                        </div>
                    </td>
                    <td class="text-center">
                        <span class="fw-bold">{{ number_format($tx->total_items) }}</span> <span class="small text-muted">item</span>
                    </td>
                    <td class="text-end">
                        <span class="fw-semibold text-dark">Rp {{ number_format($tx->total_amount, 0, ',', '.') }}</span>
                    </td>
                    <td>
                        <span class="small text-muted"><i class="bi bi-person me-1"></i>{{ $tx->user->name ?? '-' }}</span>
                    </td>
                    <td class="text-center">
                        <a href="{{ route('transactions.show', $tx) }}" class="btn btn-sm btn-light border px-2 py-1" title="Lihat Detail Transaksi">
                            <i class="bi bi-eye text-primary"></i>
                        </a>
                        <a href="{{ route('transactions.print', $tx) }}" target="_blank" class="btn btn-sm btn-light border px-2 py-1" title="Cetak Surat / Bukti">
                            <i class="bi bi-printer text-secondary"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="text-center py-5">
        <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
        <h6 class="fw-bold text-dark mt-2">Belum Ada Transaksi Tercatat</h6>
        <p class="text-muted small mb-0">Mulai catat barang masuk atau barang keluar dengan tombol di atas.</p>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // 1. Grafik Aktivitas Barang Masuk dan Keluar (14 Hari)
        const ctxActivity = document.getElementById('stockActivityChart').getContext('2d');
        const activityLabels = @json($chartDates);
        const incomingData = @json($chartIncoming);
        const outgoingData = @json($chartOutgoing);

        new Chart(ctxActivity, {
            type: 'bar',
            data: {
                labels: activityLabels,
                datasets: [
                    {
                        label: 'Barang Masuk (+)',
                        data: incomingData,
                        backgroundColor: 'rgba(16, 185, 129, 0.75)',
                        borderColor: '#10b981',
                        borderWidth: 1.5,
                        borderRadius: 6,
                    },
                    {
                        label: 'Barang Keluar (-)',
                        data: outgoingData,
                        backgroundColor: 'rgba(239, 68, 68, 0.75)',
                        borderColor: '#ef4444',
                        borderWidth: 1.5,
                        borderRadius: 6,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        padding: 10,
                        boxPadding: 4,
                        callbacks: {
                            label: function(context) {
                                return ` ${context.dataset.label}: ${context.raw.toLocaleString('id-ID')} unit`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: { family: 'Plus Jakarta Sans', size: 11 }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f1f5f9'
                        },
                        ticks: {
                            font: { family: 'Plus Jakarta Sans', size: 11 },
                            precision: 0
                        }
                    }
                }
            }
        });

        // 2. Status Stok Donut Chart
        const ctxStatus = document.getElementById('stockStatusChart').getContext('2d');
        const safeCount = {{ $stockSafeCount }};
        const warningCount = {{ $stockWarningCount }};
        const outCount = {{ $outOfStockCount }};

        new Chart(ctxStatus, {
            type: 'doughnut',
            data: {
                labels: ['Stok Aman', 'Stok Menipis', 'Stok Habis'],
                datasets: [{
                    data: [safeCount, warningCount, outCount],
                    backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return ` ${context.label}: ${context.raw} jenis`;
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
