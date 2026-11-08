@extends('layouts.app')

@section('title', 'Pengeluaran Barang Keluar (Stock Out)')

@section('content')
<div class="mb-4">
    <a href="{{ route('transactions.index') }}" class="text-decoration-none small text-muted d-inline-flex align-items-center gap-1 mb-2">
        <i class="bi bi-arrow-left"></i> Kembali ke Riwayat Transaksi
    </a>
    <div class="d-flex align-items-center gap-2">
        <span class="rounded-circle bg-danger text-white p-2 d-inline-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
            <i class="bi bi-box-arrow-up-right"></i>
        </span>
        <div>
            <h3 class="fw-bold m-0 text-dark">Pencatatan Barang Keluar (Stock Out)</h3>
            <p class="text-muted small m-0">Input pengeluaran barang dari gudang untuk divisi internal, cabang, atau pengiriman ke pelanggan.</p>
        </div>
    </div>
</div>

<form action="{{ route('transactions.store') }}" method="POST" id="transactionForm">
    @csrf
    <input type="hidden" name="type" value="out">

    <div class="row g-4">
        <!-- Header Info Card -->
        <div class="col-12 col-lg-4">
            <div class="card-custom p-4">
                <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">Informasi Pengeluaran</h6>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">No. Dokumen / Referensi <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-receipt"></i></span>
                        <input type="text" name="reference_no" class="form-control font-monospace @error('reference_no') is-invalid @enderror" value="{{ old('reference_no', $suggestedRef) }}" required>
                    </div>
                    @error('reference_no') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Tanggal Pengeluaran <span class="text-danger">*</span></label>
                    <input type="date" name="transaction_date" class="form-control @error('transaction_date') is-invalid @enderror" value="{{ old('transaction_date', date('Y-m-d')) }}" required>
                    @error('transaction_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Tujuan Pengiriman / Penerima <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-geo"></i></span>
                        <input type="text" name="recipient" class="form-control @error('recipient') is-invalid @enderror" value="{{ old('recipient') }}" placeholder="Contoh: Cabang Surabaya / Tim Produksi Line A" required>
                    </div>
                    @error('recipient') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Catatan / Keterangan Keperluan</label>
                    <textarea name="notes" class="form-control" rows="3" placeholder="Misal: Permintaan darurat operasional atau No. DO..."></textarea>
                </div>

                <div class="p-3 bg-light rounded-3 border">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Total Kuantitas Keluar:</span>
                        <span class="fw-bold fs-6 text-dark" id="summaryTotalItems">0</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted small">Total Estimasi Nilai:</span>
                        <span class="fw-bold fs-5 text-danger" id="summaryTotalAmount">Rp 0</span>
                    </div>
                </div>

                <button type="submit" class="btn btn-danger w-100 py-2 mt-4 fw-semibold shadow-sm">
                    <i class="bi bi-check2-circle me-1"></i> Simpan Barang Keluar
                </button>
            </div>
        </div>

        <!-- Detail Items Card -->
        <div class="col-12 col-lg-8">
            <div class="card-custom p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold text-dark m-0">Daftar Barang yang Dikeluarkan</h6>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="addRow()">
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
                                <th style="min-width: 250px;">Nama Barang & Ketersediaan Stok</th>
                                <th style="width: 140px;">Jumlah Keluar</th>
                                <th style="width: 170px;">Harga Satuan (Rp)</th>
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
                    <span class="text-muted small text-danger"><i class="bi bi-shield-lock me-1"></i>Kuantitas keluar tidak boleh melebihi stok yang tersedia.</span>
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

        let optionsHtml = '<option value="">-- Pilih Barang yang Tersedia --</option>';
        productsData.forEach(p => {
            const isSelected = (preselectedId && preselectedId == p.id) ? 'selected' : '';
            optionsHtml += `<option value="${p.id}" data-price="${p.selling_price || p.purchase_price}" data-unit="${p.unit}" data-stock="${p.stock}" ${isSelected}>${p.name} (${p.sku}) [Stok: ${p.stock} ${p.unit}]</option>`;
        });

        tr.innerHTML = `
            <td>
                <select name="items[${rowIndex}][product_id]" class="form-select product-select" required onchange="handleProductChange(${rowIndex})">
                    ${optionsHtml}
                </select>
                <div class="small text-muted mt-1 product-meta" id="meta-${rowIndex}">Tersedia di rak: -</div>
            </td>
            <td>
                <div class="input-group input-group-sm">
                    <input type="number" name="items[${rowIndex}][quantity]" class="form-control text-center qty-input" min="1" max="999999" value="1" required oninput="handleQtyInput(${rowIndex})">
                    <span class="input-group-text unit-label" id="unit-${rowIndex}">Pcs</span>
                </div>
                <div class="text-danger small mt-1 d-none error-stock" id="error-stock-${rowIndex}">Melebihi stok!</div>
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
            const stock = parseInt(selectedOption.getAttribute('data-stock')) || 0;

            row.querySelector('.price-input').value = price;
            row.querySelector('.unit-label').innerText = unit;

            const qtyInput = row.querySelector('.qty-input');
            qtyInput.max = stock;

            row.querySelector(`#meta-${index}`).innerHTML = `Stok tersedia: <strong class="text-success">${stock} ${unit}</strong>`;
            handleQtyInput(index);
        } else {
            row.querySelector('.price-input').value = 0;
            row.querySelector('.unit-label').innerText = 'Pcs';
            row.querySelector(`#meta-${index}`).innerText = `Tersedia di rak: -`;
        }

        calculateSubtotal(index);
    }

    function handleQtyInput(index) {
        const row = document.getElementById(`row-${index}`);
        const select = row.querySelector('.product-select');
        const selectedOption = select.options[select.selectedIndex];
        const qtyInput = row.querySelector('.qty-input');
        const errorDiv = row.querySelector(`#error-stock-${index}`);

        if (selectedOption && selectedOption.value) {
            const availableStock = parseInt(selectedOption.getAttribute('data-stock')) || 0;
            const currentQty = parseInt(qtyInput.value) || 0;

            if (currentQty > availableStock) {
                errorDiv.classList.remove('d-none');
                qtyInput.classList.add('is-invalid');
            } else {
                errorDiv.classList.add('d-none');
                qtyInput.classList.remove('is-invalid');
            }
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
                alert('Minimal harus ada 1 baris barang dalam transaksi pengeluaran.');
            }
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        addRow(selectedProductId);
    });
</script>
@endpush
