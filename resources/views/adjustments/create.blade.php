@extends('layouts.app')

@section('title', 'Input Stock Opname Baru')

@section('content')
<div class="mb-4">
    <a href="{{ route('adjustments.index') }}" class="text-decoration-none small text-muted d-inline-flex align-items-center gap-1 mb-2">
        <i class="bi bi-arrow-left"></i> Kembali ke Daftar Opname
    </a>
    <h3 class="fw-bold m-0 text-dark">Form Penyesuaian Stok (Stock Opname)</h3>
    <p class="text-muted small m-0">Sesuaikan catatan stok sistem agar sinkron dengan hasil perhitungan fisik di lapangan.</p>
</div>

<div class="row">
    <div class="col-12 col-lg-8">
        <div class="card-custom p-4">
            <form action="{{ route('adjustments.store') }}" method="POST">
                @csrf

                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-semibold">No. Dokumen Opname <span class="text-danger">*</span></label>
                        <input type="text" name="reference_no" class="form-control font-monospace" value="{{ old('reference_no', $suggestedRef) }}" required>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-semibold">Tanggal Audit / Opname <span class="text-danger">*</span></label>
                        <input type="date" name="adjustment_date" class="form-control" value="{{ old('adjustment_date', date('Y-m-d')) }}" required>
                    </div>

                    <div class="col-12">
                        <label class="form-label small fw-semibold">Pilih Barang yang Diaudit <span class="text-danger">*</span></label>
                        <select name="product_id" id="productSelect" class="form-select @error('product_id') is-invalid @enderror" required onchange="handleProductSelect()">
                            <option value="">-- Cari & Pilih Barang --</option>
                            @foreach($products as $prod)
                                <option value="{{ $prod->id }}" data-stock="{{ $prod->stock }}" data-unit="{{ $prod->unit }}" {{ old('product_id') == $prod->id ? 'selected' : '' }}>
                                    {{ $prod->name }} ({{ $prod->sku }}) - Lokasi: {{ $prod->location ?? '-' }}
                                </option>
                            @endforeach
                        </select>
                        @error('product_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Comparison Box -->
                    <div class="col-12">
                        <div class="p-3 bg-light rounded-3 border">
                            <div class="row align-items-center">
                                <div class="col-12 col-md-4 text-center border-end-md">
                                    <div class="text-muted small mb-1">Stok Menurut Sistem</div>
                                    <h4 class="fw-bold text-dark m-0" id="systemStockDisplay">0</h4>
                                    <span class="small text-muted" id="unitDisplay">Unit</span>
                                </div>
                                <div class="col-12 col-md-4 text-center my-3 my-md-0">
                                    <label class="form-label small fw-semibold text-primary mb-1">Stok Fisik Aktual di Gudang</label>
                                    <input type="number" name="physical_stock" id="physicalStockInput" class="form-control text-center fw-bold fs-5" min="0" value="{{ old('physical_stock', 0) }}" required oninput="calculateDiff()">
                                </div>
                                <div class="col-12 col-md-4 text-center border-start-md">
                                    <div class="text-muted small mb-1">Hasil Selisih</div>
                                    <h4 class="fw-bold m-0 text-secondary" id="diffDisplay">0</h4>
                                    <span class="small text-muted" id="diffStatus">Sama</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label small fw-semibold">Alasan Penyesuaian <span class="text-danger">*</span></label>
                        <select name="reason" class="form-select @error('reason') is-invalid @enderror" required>
                            <option value="">-- Pilih Alasan --</option>
                            <option value="Selisih Hitung / Audit Rutin">Selisih Hitung / Audit Rutin</option>
                            <option value="Kerusakan Fisik Barang (Afkir)">Kerusakan Fisik Barang (Afkir)</option>
                            <option value="Kehilangan Barang">Kehilangan Barang</option>
                            <option value="Koreksi Kesalahan Input Transaksi">Koreksi Kesalahan Input Transaksi</option>
                            <option value="Kadaluwarsa / Expired">Kadaluwarsa / Expired</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                        @error('reason') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label small fw-semibold">Catatan Pemeriksaan</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Keterangan tambahan kronologi kondisi barang..."></textarea>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-top d-flex gap-2 justify-content-end">
                    <a href="{{ route('adjustments.index') }}" class="btn btn-light border px-4">Batal</a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-check-lg me-1"></i> Simpan Penyesuaian
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let currentSystemStock = 0;

    function handleProductSelect() {
        const select = document.getElementById('productSelect');
        const selected = select.options[select.selectedIndex];

        if (selected && selected.value) {
            currentSystemStock = parseInt(selected.getAttribute('data-stock')) || 0;
            const unit = selected.getAttribute('data-unit') || 'Unit';

            document.getElementById('systemStockDisplay').innerText = currentSystemStock.toLocaleString('id-ID');
            document.getElementById('unitDisplay').innerText = unit;
            document.getElementById('physicalStockInput').value = currentSystemStock;
        } else {
            currentSystemStock = 0;
            document.getElementById('systemStockDisplay').innerText = '0';
            document.getElementById('unitDisplay').innerText = 'Unit';
        }
        calculateDiff();
    }

    function calculateDiff() {
        const physical = parseInt(document.getElementById('physicalStockInput').value) || 0;
        const diff = physical - currentSystemStock;
        const diffEl = document.getElementById('diffDisplay');
        const statusEl = document.getElementById('diffStatus');

        if (diff > 0) {
            diffEl.innerText = `+${diff}`;
            diffEl.className = 'fw-bold m-0 text-success';
            statusEl.innerText = 'Stok Fisik Lebih Banyak';
        } else if (diff < 0) {
            diffEl.innerText = `${diff}`;
            diffEl.className = 'fw-bold m-0 text-danger';
            statusEl.innerText = 'Stok Fisik Kurang / Hilang';
        } else {
            diffEl.innerText = '0';
            diffEl.className = 'fw-bold m-0 text-secondary';
            statusEl.innerText = 'Cocok (Tidak Ada Selisih)';
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        handleProductSelect();
    });
</script>
@endpush
