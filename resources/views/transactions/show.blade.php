@extends('layouts.app')

@section('title', 'Detail Transaksi ' . $transaction->reference_no)

@section('content')
<div class="mb-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
    <div>
        <a href="{{ route('transactions.index') }}" class="text-decoration-none small text-muted d-inline-flex align-items-center gap-1 mb-2">
            <i class="bi bi-arrow-left"></i> Kembali ke Riwayat Transaksi
        </a>
        <div class="d-flex align-items-center gap-2">
            <h3 class="fw-bold m-0 text-dark">Detail Transaksi {{ $transaction->reference_no }}</h3>
            <span class="badge-soft-{{ $transaction->type_badge }}">
                <i class="bi {{ $transaction->type_icon }} me-1"></i>{{ $transaction->type_label }}
            </span>
        </div>
        <div class="text-muted small mt-1">
            Dicatat pada {{ $transaction->created_at->translatedFormat('d F Y, H:i') }} WIB oleh <strong>{{ $transaction->user->name ?? 'Sistem' }}</strong>
        </div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('transactions.print', $transaction) }}" target="_blank" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2">
            <i class="bi bi-printer"></i>
            <span>Cetak Bukti / Surat Jalan</span>
        </a>
        @if($transaction->type === 'in')
            <a href="{{ route('transactions.create-in') }}" class="btn btn-success d-inline-flex align-items-center gap-1">
                <i class="bi bi-plus-lg"></i>
                <span>Barang Masuk Baru</span>
            </a>
        @else
            <a href="{{ route('transactions.create-out') }}" class="btn btn-danger d-inline-flex align-items-center gap-1">
                <i class="bi bi-dash-lg"></i>
                <span>Barang Keluar Baru</span>
            </a>
        @endif
    </div>
</div>

<div class="row g-4">
    <!-- Meta Info -->
    <div class="col-12 col-lg-4">
        <div class="card-custom p-4 mb-4">
            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">Informasi Dokumen</h6>

            <div class="mb-3">
                <span class="text-muted small d-block">Nomor Referensi</span>
                <span class="fw-bold text-dark font-monospace fs-6">{{ $transaction->reference_no }}</span>
            </div>

            <div class="mb-3">
                <span class="text-muted small d-block">Jenis Transaksi</span>
                <span class="badge-soft-{{ $transaction->type_badge }} fs-6">
                    <i class="bi {{ $transaction->type_icon }} me-1"></i>{{ $transaction->type_label }}
                </span>
            </div>

            <div class="mb-3">
                <span class="text-muted small d-block">Tanggal Transaksi</span>
                <span class="fw-semibold text-dark">{{ $transaction->transaction_date->translatedFormat('l, d F Y') }}</span>
            </div>

            <div class="mb-3">
                <span class="text-muted small d-block">{{ $transaction->type === 'in' ? 'Pemasok / Vendor' : 'Tujuan Pengiriman / Penerima' }}</span>
                @if($transaction->type === 'in')
                    <span class="fw-semibold text-dark"><i class="bi bi-truck me-1"></i>{{ $transaction->supplier->name ?? 'Pemasok Umum' }}</span>
                    @if($transaction->supplier)
                        <div class="small text-muted mt-1">{{ $transaction->supplier->phone }} • {{ $transaction->supplier->address }}</div>
                    @endif
                @else
                    <span class="fw-semibold text-dark"><i class="bi bi-send me-1"></i>{{ $transaction->recipient ?? 'Internal Gudang' }}</span>
                @endif
            </div>

            <div class="mb-3">
                <span class="text-muted small d-block">Petugas Operator</span>
                <span class="fw-medium text-dark"><i class="bi bi-person me-1"></i>{{ $transaction->user->name ?? 'Administrator' }}</span>
            </div>

            @if($transaction->notes)
            <div class="p-3 bg-light rounded-3 border">
                <span class="text-muted small d-block fw-semibold mb-1">Catatan Tambahan:</span>
                <p class="small text-dark mb-0">{{ $transaction->notes }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Items Detail -->
    <div class="col-12 col-lg-8">
        <div class="card-custom p-4">
            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">Rincian Item Barang</h6>

            <div class="table-responsive">
                <table class="table table-custom align-middle mb-0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Barang & SKU</th>
                            <th>Kategori</th>
                            <th class="text-center">Kuantitas</th>
                            <th class="text-end">Harga Satuan</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transaction->items as $idx => $item)
                        <tr>
                            <td class="text-muted small">{{ $idx + 1 }}</td>
                            <td>
                                <a href="{{ route('products.show', $item->product) }}" class="fw-semibold text-dark text-decoration-none">
                                    {{ $item->product->name ?? 'Produk Telah Dihapus' }}
                                </a>
                                <div class="text-muted small"><code>{{ $item->product->sku ?? '-' }}</code></div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $item->product->category->name ?? '-' }}</span>
                            </td>
                            <td class="text-center">
                                <span class="fw-bold {{ $transaction->type === 'in' ? 'text-success' : 'text-danger' }}">
                                    {{ $transaction->type === 'in' ? '+' : '-' }}{{ number_format($item->quantity) }}
                                </span>
                                <span class="small text-muted">{{ $item->product->unit ?? 'Unit' }}</span>
                            </td>
                            <td class="text-end">
                                <span class="small">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</span>
                            </td>
                            <td class="text-end">
                                <span class="fw-bold text-dark">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="border-top-2">
                        <tr>
                            <th colspan="3" class="text-end">Total:</th>
                            <th class="text-center fw-bold">{{ number_format($transaction->total_items) }} item</th>
                            <th></th>
                            <th class="text-end fw-bold fs-6 text-{{ $transaction->type_badge }}">
                                Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
