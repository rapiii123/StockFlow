@extends('layouts.app')

@section('title', 'Penerimaan Barang Masuk (Stock In)')

@section('content')
<div class="mb-4">
    <a href="{{ route('transactions.index') }}" class="text-decoration-none small text-muted d-inline-flex align-items-center gap-1 mb-2">
        <i class="bi bi-arrow-left"></i> Kembali ke Riwayat Transaksi
    </a>
    <div class="d-flex align-items-center gap-2">
        <span class="rounded-circle bg-success text-white p-2 d-inline-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
            <i class="bi bi-box-arrow-in-down"></i>
        </span>
        <div>
            <h3 class="fw-bold m-0 text-dark">Pencatatan Barang Masuk (Stock In)</h3>
            <p class="text-muted small m-0">Input penerimaan pasokan stok barang dari pemasok ke dalam gudang.</p>
        </div>
    </div>
</div>

<form action="{{ route('transactions.store') }}" method="POST" id="transactionForm">
    @csrf
    <input type="hidden" name="type" value="in">

    <div class="row g-4">
        <!-- Header Info Card -->
        <div class="col-12 col-lg-4">
            <div class="card-custom p-4">
                <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">Informasi Penerimaan</h6>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">No. Dokumen / Referensi <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-receipt"></i></span>
                        <input type="text" name="reference_no" class="form-control font-monospace @error('reference_no') is-invalid @enderror" value="{{ old('reference_no', $suggestedRef) }}" required>
                    </div>
                    @error('reference_no') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Tanggal Penerimaan <span class="text-danger">*</span></label>
                    <input type="date" name="transaction_date" class="form-control @error('transaction_date') is-invalid @enderror" value="{{ old('transaction_date', date('Y-m-d')) }}" required>
                    @error('transaction_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Pemasok / Vendor</label>
                    <select name="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror">
                        <option value="">-- Pilih Pemasok / Umum --</option>
                        @foreach($suppliers as $sup)
                            <option value="{{ $sup->id }}" {{ old('supplier_id') == $sup->id ? 'selected' : '' }}>
                                {{ $sup->name }} ({{ $sup->code }})
                            </option>
                        @endforeach
                    </select>
                    @error('supplier_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Catatan / No. PO Vendor (Opsional)</label>
                    <textarea name="notes" class="form-control" rows="3" placeholder="Misal: Penerimaan PO-2026-11 via Ekspedisi Dakota...">{{ old('notes') }}</textarea>
                </div>

                <div class="p-3 bg-light rounded-3 border">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Total Kuantitas:</span>
                        <span class="fw-bold fs-6 text-dark" id="summaryTotalItems">0</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted small">Total Estimasi Nilai:</span>
                        <span class="fw-bold fs-5 text-success" id="summaryTotalAmount">Rp 0</span>
                    </div>
                </div>

                <button type="submit" class="btn btn-success w-100 py-2 mt-4 fw-semibold shadow-sm">
                    <i class="bi bi-check2-circle me-1"></i> Simpan Barang Masuk
                </button>
            </div>
        </div>

        <!-- Detail Items Card -->
        <div class="col-12 col-lg-8">
            <div class="card-custom p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold text-dark m-0">Daftar Barang yang Diterima</h6>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="addRow()">
                        <i class="bi bi-plus-lg me-1"></i> Tambah Baris Barang
                    </button>
                </div>

                @error('items')
                    <div class="alert alert-danger py-2 small mb-3">{{ $message }}</div>
                @enderror

                <div class="table-responsive">
                    <table class="table table-bordered align-middle" id="itemsTable">
                        <thead class="bg-light">
                            <tr>
                                <th style="min-width: 250px;">Nama Barang</th>
                                <th style="width: 130px;">Jumlah</th>
                                <th style="width: 180px;">Harga Beli (Rp)</th>
                                <th style="width: 170px;">Subtotal (Rp)</th>
                                <th style="width: 50px;"></th>
                            </tr>
                        </thead>
                        <tbody id="itemsBody">
                            <!-- Rows injected via JS -->
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3 pt-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="addRow()">
                        <i class="bi bi-plus-circle me-1"></i> Tambah Produk Lain
                    </button>
                    <span class="text-muted small">* Stok produk terkait akan otomatis bertambah setelah disimpan.</span>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    const productsData = @json($products);
    const selectedProductId = @json($selectedProductId);
    let rowIndex = 0;

    function addRow(preselectedId = null) {
        const tbody = document.getElementById('itemsBody');
        const tr = document.createElement('tr');
        tr.id = `row-${rowIndex}`;

        let optionsHtml = '<option value="">-- Pilih Barang --</option>';
        productsData.forEach(p => {
            const isSelected = (preselectedId && preselectedId == p.id) ? 'selected' : '';
            optionsHtml += `<option value="${p.id}" data-price="${p.purchase_price}" data-unit="${p.unit}" data-stock="${p.stock}" ${isSelected}>${p.name} (${p.sku}) - Stok: ${p.stock} ${p.unit}</option>`;
        });

        tr.innerHTML = `
            <td>
                <select name="items[${rowIndex}][product_id]" class="form-select product-select" required onchange="handleProductChange(${rowIndex})">
                    ${optionsHtml}
                </select>
                <div class="small text-muted mt-1 product-meta" id="meta-${rowIndex}">Satuan: - | Stok saat ini: -</div>
            </td>
            <td>
                <div class="input-group input-group-sm">
                    <input type="number" name="items[${rowIndex}][quantity]" class="form-control text-center qty-input" min="1" value="1" required oninput="calculateSubtotal(${rowIndex})">
                    <span class="input-group-text unit-label" id="unit-${rowIndex}">Pcs</span>
                </div>
            </td>
            <td>
                <div class="input-group input-group-sm">
                    <span class="input-group-text">Rp</span>
                    <input type="number" name="items[${rowIndex}][unit_price]" class="form-control price-input text-end" min="0" step="100" value="0" required oninput="calculateSubtotal(${rowIndex})">
                </div>
            </td>
            <td>
                <div class="fw-bold text-end text-dark subtotal-text" id="subtotal-${rowIndex}">Rp 0</div>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger border-0" onclick="removeRow(${rowIndex})" title="Hapus Baris">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        `;

        tbody.appendChild(tr);

        if (preselectedId) {
            handleProductChange(rowIndex);
        }

        rowIndex++;
        calculateGrandTotal();
    }

    function handleProductChange(index) {
        const row = document.getElementById(`row-${index}`);
        const select = row.querySelector('.product-select');
        const selectedOption = select.options[select.selectedIndex];

        if (selectedOption && selectedOption.value) {
            const price = parseFloat(selectedOption.getAttribute('data-price')) || 0;
            const unit = selectedOption.getAttribute('data-unit') || 'Pcs';
            const stock = selectedOption.getAttribute('data-stock') || '0';

            row.querySelector('.price-input').value = price;
            row.querySelector('.unit-label').innerText = unit;
            row.querySelector(`#meta-${index}`).innerText = `Satuan: ${unit} | Stok saat ini di gudang: ${stock} ${unit}`;
        } else {
            row.querySelector('.price-input').value = 0;
            row.querySelector('.unit-label').innerText = 'Pcs';
            row.querySelector(`#meta-${index}`).innerText = `Satuan: - | Stok saat ini: -`;
        }

        calculateSubtotal(index);
    }

    function calculateSubtotal(index) {
        const row = document.getElementById(`row-${index}`);
        if (!row) return;

        const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
        const price = parseFloat(row.querySelector('.price-input').value) || 0;
        const subtotal = qty * price;

        row.querySelector(`#subtotal-${index}`).innerText = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(subtotal);

        calculateGrandTotal();
    }

    function calculateGrandTotal() {
        let totalItems = 0;
        let totalAmount = 0;

        document.querySelectorAll('#itemsBody tr').forEach(row => {
            const qty = parseFloat(row.querySelector('.qty-input')?.value) || 0;
            const price = parseFloat(row.querySelector('.price-input')?.value) || 0;
            totalItems += qty;
            totalAmount += (qty * price);
        });

        document.getElementById('summaryTotalItems').innerText = `${totalItems.toLocaleString('id-ID')} unit`;
        document.getElementById('summaryTotalAmount').innerText = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(totalAmount);
    }

    function removeRow(index) {
        const row = document.getElementById(`row-${index}`);
        if (row) {
            if (document.querySelectorAll('#itemsBody tr').length > 1) {
                row.remove();
                calculateGrandTotal();
            } else {
                alert('Minimal harus ada 1 baris barang dalam transaksi penerimaan.');
            }
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        addRow(selectedProductId);
    });
</script>
@endpush
