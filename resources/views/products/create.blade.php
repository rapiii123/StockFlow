@extends('layouts.app')

@section('title', 'Tambah Barang Baru')

@section('content')
<div class="mb-4">
    <a href="{{ route('products.index') }}" class="text-decoration-none small text-muted d-inline-flex align-items-center gap-1 mb-2">
        <i class="bi bi-arrow-left"></i> Kembali ke Katalog Barang
    </a>
    <h3 class="fw-bold m-0 text-dark">Tambah Barang Baru</h3>
    <p class="text-muted small m-0">Masukkan data detail barang baru ke dalam sistem inventaris gudang.</p>
</div>

<div class="row">
    <div class="col-12 col-lg-10 col-xl-9">
        <div class="card-custom p-4">
            <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row g-3">
                    <!-- Kategori & SKU -->
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold small">Kategori Barang <span class="text-danger">*</span></label>
                        <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }} ({{ $cat->code }})
                                </option>
                            @endforeach
                        </select>
                        @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold small">Kode SKU Barang <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i class="bi bi-upc"></i></span>
                            <input type="text" name="sku" class="form-control font-monospace @error('sku') is-invalid @enderror" value="{{ old('sku', $suggestedSku) }}" required placeholder="Contoh: SF-0001">
                        </div>
                        @error('sku') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <!-- Nama Barang -->
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Nama Barang Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="Contoh: Scanner Barcode Wireless 2D Handheld">
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Barcode & Satuan -->
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold small">Nomor Barcode / EAN (Opsional)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i class="bi bi-upc-scan"></i></span>
                            <input type="text" name="barcode" class="form-control @error('barcode') is-invalid @enderror" value="{{ old('barcode') }}" placeholder="899123456789">
                        </div>
                        @error('barcode') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold small">Satuan Unit <span class="text-danger">*</span></label>
                        <input type="text" list="unitsList" name="unit" class="form-control @error('unit') is-invalid @enderror" value="{{ old('unit', 'Pcs') }}" required placeholder="Pcs, Box, Roll, Set, Kg, dll.">
                        <datalist id="unitsList">
                            <option value="Pcs">
                            <option value="Box">
                            <option value="Pack">
                            <option value="Roll">
                            <option value="Set">
                            <option value="Unit">
                            <option value="Kg">
                            <option value="Meter">
                            <option value="Pallet">
                            <option value="Can">
                        </datalist>
                        @error('unit') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Harga Beli & Harga Jual/Estimasi -->
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold small">Harga Beli Satuan (Rp) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">Rp</span>
                            <input type="number" name="purchase_price" class="form-control @error('purchase_price') is-invalid @enderror" min="0" step="100" value="{{ old('purchase_price', 0) }}" required>
                        </div>
                        @error('purchase_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold small">Harga Jual / Nilai Keluar (Rp)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">Rp</span>
                            <input type="number" name="selling_price" class="form-control @error('selling_price') is-invalid @enderror" min="0" step="100" value="{{ old('selling_price', 0) }}">
                        </div>
                        <span class="form-text small text-muted">Bisa dikosongkan jika sama dengan harga beli.</span>
                        @error('selling_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Stok Awal & Stok Minimum (Low Stock Threshold) -->
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold small">Jumlah Stok Awal <span class="text-danger">*</span></label>
                        <input type="number" name="stock" class="form-control @error('stock') is-invalid @enderror" min="0" value="{{ old('stock', 0) }}" required>
                        @error('stock') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold small">Batas Stok Minimum (Low Stock Alert) <span class="text-danger">*</span></label>
                        <input type="number" name="min_stock" class="form-control @error('min_stock') is-invalid @enderror" min="0" value="{{ old('min_stock', 5) }}" required>
                        <span class="form-text small text-muted">Jika stok ≤ batas ini, sistem akan memunculkan status 'Menipis'.</span>
                        @error('min_stock') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Lokasi Rak Gudang -->
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Lokasi Rak / Penempatan Gudang</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i class="bi bi-geo-alt"></i></span>
                            <input type="text" name="location" class="form-control @error('location') is-invalid @enderror" value="{{ old('location') }}" placeholder="Contoh: Rak A-02 Lantai 1, Bay 3">
                        </div>
                        @error('location') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Deskripsi Barang -->
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Deskripsi & Spesifikasi Singkat</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Informasi ukuran, tipe garansi, kegunaan, atau catatan khusus barang...">{{ old('description') }}</textarea>
                    </div>

                    <!-- Foto Barang -->
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Foto Barang (Opsional)</label>
                        <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*" onchange="previewImage(event)">
                        <span class="form-text small text-muted">Format: JPG, PNG, WEBP (Maksimal 2MB).</span>
                        @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror

                        <div id="imagePreviewContainer" class="mt-2 d-none">
                            <img id="imagePreview" src="#" alt="Preview Foto" class="rounded border" style="max-height: 120px; object-fit: contain;">
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-top d-flex gap-2 justify-content-end">
                    <a href="{{ route('products.index') }}" class="btn btn-light border px-4">Batal</a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-check-lg me-1"></i> Simpan Barang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function previewImage(event) {
        const input = event.target;
        const container = document.getElementById('imagePreviewContainer');
        const preview = document.getElementById('imagePreview');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                container.classList.remove('d-none');
            };
            reader.readAsDataURL(input.files[0]);
        } else {
            container.classList.add('d-none');
        }
    }
</script>
@endpush
