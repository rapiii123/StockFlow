<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Transaksi - {{ $transaction->reference_no }}</title>
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #111;
            background: #f8fafc;
            padding: 20px;
        }

        .print-container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 35px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }

        .header-logo {
            font-size: 1.5rem;
            font-weight: 800;
            color: #0f172a;
        }

        @media print {
            body {
                background: #fff;
                padding: 0;
            }
            .print-container {
                box-shadow: none;
                padding: 0;
                max-width: 100%;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="print-container">
        <!-- Top Action Bar (hidden on print) -->
        <div class="no-print d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
            <a href="{{ route('transactions.show', $transaction) }}" class="btn btn-sm btn-outline-secondary">
                &larr; Kembali
            </a>
            <button onclick="window.print()" class="btn btn-sm btn-primary">
                Cetak Dokumen Sekarang
            </button>
        </div>

        <!-- Document Header -->
        <div class="d-flex justify-content-between align-items-start border-bottom pb-4 mb-4">
            <div>
                <div class="header-logo">StockFlow</div>
                <div class="small text-muted">Sistem Pergudangan & Manajemen Inventaris Terpadu</div>
                <div class="small text-muted">Kawasan Industri & Logistik Pergudangan Utama</div>
            </div>
            <div class="text-end">
                <h4 class="fw-bold mb-1 {{ $transaction->type === 'in' ? 'text-success' : 'text-danger' }}">
                    {{ $transaction->type === 'in' ? 'BUKTI PENERIMAAN BARANG' : 'SURAT JALAN PENGELUARAN' }}
                </h4>
                <div class="font-monospace fw-bold">{{ $transaction->reference_no }}</div>
                <div class="small text-muted">Tanggal: {{ $transaction->transaction_date->format('d/m/Y') }}</div>
            </div>
        </div>

        <!-- Info Grid -->
        <div class="row g-3 mb-4">
            <div class="col-6">
                <div class="p-3 bg-light rounded">
                    <div class="small text-muted fw-bold mb-1">
                        {{ $transaction->type === 'in' ? 'DITERIMA DARI (PEMASOK):' : 'TUJUAN PENGIRIMAN / PENERIMA:' }}
                    </div>
                    @if($transaction->type === 'in')
                        <div class="fw-bold">{{ $transaction->supplier->name ?? 'Pemasok Umum' }}</div>
                        <div class="small">{{ $transaction->supplier->address ?? '-' }}</div>
                        <div class="small">{{ $transaction->supplier->phone ?? '-' }}</div>
                    @else
                        <div class="fw-bold">{{ $transaction->recipient ?? 'Internal Perusahaan' }}</div>
                    @endif
                </div>
            </div>
            <div class="col-6">
                <div class="p-3 bg-light rounded">
                    <div class="small text-muted fw-bold mb-1">PETUGAS GUDANG:</div>
                    <div class="fw-bold">{{ $transaction->user->name ?? 'Admin Gudang' }}</div>
                    <div class="small text-muted">Waktu Cetak: {{ now()->translatedFormat('d F Y, H:i') }} WIB</div>
                    @if($transaction->notes)
                        <div class="small text-muted mt-1">Catatan: {{ $transaction->notes }}</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <table class="table table-bordered mb-4">
            <thead class="table-light">
                <tr>
                    <th style="width: 40px;" class="text-center">No</th>
                    <th>Kode SKU</th>
                    <th>Nama Barang</th>
                    <th class="text-center" style="width: 100px;">Jumlah</th>
                    <th class="text-end" style="width: 140px;">Harga Satuan</th>
                    <th class="text-end" style="width: 150px;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transaction->items as $idx => $item)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="font-monospace small">{{ $item->product->sku ?? '-' }}</td>
                    <td>
                        <div class="fw-bold">{{ $item->product->name ?? 'Barang Dihapus' }}</div>
                        <div class="small text-muted">Kategori: {{ $item->product->category->name ?? '-' }}</div>
                    </td>
                    <td class="text-center fw-bold">
                        {{ $item->quantity }} {{ $item->product->unit ?? 'Unit' }}
                    </td>
                    <td class="text-end">
                        Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                    </td>
                    <td class="text-end fw-semibold">
                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="fw-bold">
                    <td colspan="3" class="text-end">TOTAL:</td>
                    <td class="text-center">{{ number_format($transaction->total_items) }} unit</td>
                    <td></td>
                    <td class="text-end">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        <!-- Signatures -->
        <div class="row text-center mt-5 pt-3">
            <div class="col-4">
                <p class="small text-muted mb-5">Dibuat Oleh,</p>
                <div class="fw-bold text-decoration-underline">{{ $transaction->user->name ?? 'Petugas Gudang' }}</div>
                <div class="small text-muted">Staf Administrasi</div>
            </div>
            <div class="col-4">
                <p class="small text-muted mb-5">Dikirim / Diserahkan Oleh,</p>
                <div class="fw-bold text-decoration-underline">( .................................... )</div>
                <div class="small text-muted">Operator Lapangan</div>
            </div>
            <div class="col-4">
                <p class="small text-muted mb-5">Diterima Dengan Baik Oleh,</p>
                <div class="fw-bold text-decoration-underline">( .................................... )</div>
                <div class="small text-muted">Nama Terang & Cap</div>
            </div>
        </div>
    </div>
</body>
</html>
