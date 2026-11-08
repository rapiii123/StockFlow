@extends('layouts.app')

@section('title', 'Data Pemasok & Vendor')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold m-0 text-dark">Data Pemasok & Vendor</h3>
        <p class="text-muted small m-0">Kelola informasi mitra pemasok untuk pasokan barang masuk gudang.</p>
    </div>
    <div>
        <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#createSupplierModal">
            <i class="bi bi-plus-lg"></i>
            <span>Tambah Pemasok</span>
        </button>
    </div>
</div>

<div class="card-custom">
    <div class="p-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
        <form action="{{ route('suppliers.index') }}" method="GET" class="d-flex gap-2">
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" name="search" class="form-control border-start-0" placeholder="Cari nama, kode, kontak, telepon..." value="{{ request('search') }}">
            </div>
            <button type="submit" class="btn btn-secondary btn-sm px-3">Cari</button>
            @if(request('search'))
                <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
            @endif
        </form>
    </div>

    @if($suppliers->count() > 0)
    <div class="table-responsive">
        <table class="table table-custom align-middle mb-0 table-hover">
            <thead>
                <tr>
                    <th>Kode & Nama Pemasok</th>
                    <th>Kontak Person</th>
                    <th>Telepon / Email</th>
                    <th>Alamat</th>
                    <th class="text-center">Riwayat Pasokan</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($suppliers as $sup)
                <tr>
                    <td>
                        <div class="fw-bold text-dark">{{ $sup->name }}</div>
                        <span class="badge bg-light text-secondary border font-monospace">{{ $sup->code }}</span>
                    </td>
                    <td>
                        <span class="small fw-medium">{{ $sup->contact_person ?? '-' }}</span>
                    </td>
                    <td>
                        <div class="small"><i class="bi bi-telephone text-muted me-1"></i>{{ $sup->phone ?? '-' }}</div>
                        @if($sup->email)
                            <div class="small text-muted"><i class="bi bi-envelope me-1"></i>{{ $sup->email }}</div>
                        @endif
                    </td>
                    <td>
                        <span class="small text-muted">{{ Str::limit($sup->address, 50) ?? '-' }}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-success-subtle text-success border border-success-subtle">
                            {{ $sup->stock_transactions_count }} Transaksi
                        </span>
                    </td>
                    <td class="text-end">
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editSupplierModal{{ $sup->id }}" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <form action="{{ route('suppliers.destroy', $sup) }}" method="POST" onsubmit="return confirm('Hapus data pemasok ini?');" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus" {{ $sup->stock_transactions_count > 0 ? 'disabled' : '' }}>
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>

                <!-- Modal Edit Supplier -->
                <div class="modal fade" id="editSupplierModal{{ $sup->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow">
                            <form action="{{ route('suppliers.update', $sup) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-header">
                                    <h5 class="modal-title fw-bold">Edit Pemasok</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold">Kode Pemasok</label>
                                        <input type="text" name="code" class="form-control text-uppercase font-monospace" value="{{ $sup->code }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold">Nama Perusahaan / Pemasok</label>
                                        <input type="text" name="name" class="form-control" value="{{ $sup->name }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold">Kontak Person (PIC)</label>
                                        <input type="text" name="contact_person" class="form-control" value="{{ $sup->contact_person }}">
                                    </div>
                                    <div class="row g-2 mb-3">
                                        <div class="col-6">
                                            <label class="form-label small fw-semibold">No. Telepon / WA</label>
                                            <input type="text" name="phone" class="form-control" value="{{ $sup->phone }}">
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label small fw-semibold">Email</label>
                                            <input type="email" name="email" class="form-control" value="{{ $sup->email }}">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold">Alamat Kantor / Gudang</label>
                                        <textarea name="address" class="form-control" rows="2">{{ $sup->address }}</textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="p-3 border-top">
        {{ $suppliers->links('pagination::bootstrap-5') }}
    </div>
    @else
    <div class="text-center py-5">
        <i class="bi bi-truck text-muted fs-1"></i>
        <h6 class="fw-bold mt-2">Belum Ada Data Pemasok</h6>
    </div>
    @endif
</div>

<!-- Modal Create Supplier -->
<div class="modal fade" id="createSupplierModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('suppliers.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Tambah Pemasok Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Kode Pemasok <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control text-uppercase font-monospace" placeholder="Misal: SUP-005" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Nama Perusahaan / Pemasok <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="PT Logistik Jaya Perkasa" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Kontak Person (PIC)</label>
                        <input type="text" name="contact_person" class="form-control" placeholder="Nama petugas sales/kontak">
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold">No. Telepon / WA</label>
                            <input type="text" name="phone" class="form-control" placeholder="021-xxxx atau 0812xxxx">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="sales@perusahaan.com">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Alamat Kantor / Gudang</label>
                        <textarea name="address" class="form-control" rows="2" placeholder="Alamat lengkap supplier..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Pemasok</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
