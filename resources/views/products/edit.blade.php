@extends('layouts.app')

@section('title', 'Edit Barang: ' . $product->name)

@section('content')
<div class="mb-4">
    <a href="{{ route('products.show', $product) }}" class="text-decoration-none small text-muted d-inline-flex align-items-center gap-1 mb-2">
        <i class="bi bi-arrow-left"></i> Kembali ke Detail Barang
    </a>
    <h3 class="fw-bold m-0 text-dark">Edit Barang: {{ $product->name }}</h3>
    <p class="text-muted small m-0">Perbarui informasi harga, spesifikasi, atau lokasi penempatan barang.</p>
</div>

<div class="row">
    <div class="col-12 col-lg-10 col-xl-9">
        <div class="card-custom p-4">
            <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <!-- Kategori & SKU -->
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold small">Kategori Barang <span class="text-danger">*</span></label>
                        <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>
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
                            <input type="text" name="sku" class="form-control font-monospace @error('sku') is-invalid @enderror" value="{{ old('sku', $product->sku) }}" required>
                        </div>
                        @error('sku') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Nama Barang -->
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Nama Barang Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $product->name) }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Barcode & Satuan -->
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold small">Nomor Barcode / EAN (Opsional)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i class="bi bi-upc-scan"></i></span>
                            <input type="text" name="barcode" class="form-control @error('barcode') is-invalid @enderror" value="{{ old('barcode', $product->barcode) }}">
                        </div>
                        @error('barcode') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold small">Satuan Unit <span class="text-danger">*</span></label>
                        <input type="text" name="unit" class="form-control @error('unit') is-invalid @enderror" value="{{ old('unit', $product->unit) }}" required>
                        @error('unit') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Harga Beli & Harga Jual/Estimasi -->
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold small">Harga Beli Satuan (Rp) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">Rp</span>
                            <input type="number" name="purchase_price" class="form-control @error('purchase_price') is-invalid @enderror" min="0" step="100" value="{{ old('purchase_price', $product->purchase_price) }}" required>
                        </div>
                        @error('purchase_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold small">Harga Jual / Nilai Keluar (Rp)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">Rp</span>
                            <input type="number" name="selling_price" class="form-control @error('selling_price') is-invalid @enderror" min="0" step="100" value="{{ old('selling_price', $product->selling_price) }}">
                        </div>
                        @error('selling_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Batas Stok Minimum -->
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold small">Batas Stok Minimum (Low Stock Alert) <span class="text-danger">*</span></label>
                        <input type="number" name="min_stock" class="form-control @error('min_stock') is-invalid @enderror" min="0" value="{{ old('min_stock', $product->min_stock) }}" required>
                        <span class="form-text small text-muted">Ambang batas saat ini: ≤ {{ $product->min_stock }} {{ $product->unit }}.</span>
                        @error('min_stock') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Status Stok Saat Ini (Info saja, mutasi via Transaksi/Opname) -->
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold small">Stok Fisik Gudang Terkini</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-stack"></i></span>
                            <input type="text" class="form-control bg-light fw-bold" value="{{ $product->stock }} {{ $product->unit }}" readonly>
                        </div>
                        <span class="form-text small text-muted">Untuk mengubah stok, gunakan fitur <a href="{{ route('transactions.create-in') }}">Barang Masuk</a> atau <a href="{{ route('adjustments.create') }}">Stock Opname</a>.</span>
                    </div>

                    <!-- Lokasi Rak Gudang -->
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Lokasi Rak / Penempatan Gudang</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i class="bi bi-geo-alt"></i></span>
                            <input type="text" name="location" class="form-control @error('location') is-invalid @enderror" value="{{ old('location', $product->location) }}">
                        </div>
                        @error('location') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Deskripsi Barang -->
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Deskripsi & Spesifikasi Singkat</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $product->description) }}</textarea>
                    </div>

                    <!-- Foto Barang -->
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Ganti Foto Barang (Opsional)</label>
                        @if($product->image)
                            <div class="mb-2 d-flex align-items-center gap-3 p-2 border rounded bg-light">
                                <img src="{{ asset('storage/' . $product->image) }}" alt="Foto Sekarang" class="rounded border" style="width: 60px; height: 60px; object-fit: cover;">
                                <span class="small text-muted">Foto saat ini digunakan. Pilih file baru di bawah untuk mengganti.</span>
                            </div>
                        @endif
                        <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                        @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Status Aktif Switch -->
                    <div class="col-12">
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" name="is_active" id="isActiveSwitch" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold small" for="isActiveSwitch">Produk Aktif untuk Transaksi</label>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-top d-flex gap-2 justify-content-end">
                    <a href="{{ route('products.show', $product) }}" class="btn btn-light border px-4">Batal</a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-check-lg me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
