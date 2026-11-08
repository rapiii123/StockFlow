@extends('layouts.app')

@section('title', 'Kategori Barang')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold m-0 text-dark">Kategori Barang</h3>
        <p class="text-muted small m-0">Pengelompokan barang untuk mempermudah pencarian dan manajemen stok.</p>
    </div>
    <div>
        <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
            <i class="bi bi-plus-lg"></i>
            <span>Tambah Kategori</span>
        </button>
    </div>
</div>

<div class="card-custom">
    <div class="p-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
        <form action="{{ route('categories.index') }}" method="GET" class="d-flex gap-2">
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" name="search" class="form-control border-start-0" placeholder="Cari nama atau kode..." value="{{ request('search') }}">
            </div>
            <button type="submit" class="btn btn-secondary btn-sm px-3">Cari</button>
            @if(request('search'))
                <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
            @endif
        </form>
    </div>

    @if($categories->count() > 0)
    <div class="table-responsive">
        <table class="table table-custom align-middle mb-0 table-hover">
            <thead>
                <tr>
                    <th style="width: 150px;">Kode Kategori</th>
                    <th>Nama Kategori</th>
                    <th>Deskripsi</th>
                    <th class="text-center" style="width: 150px;">Jumlah Barang</th>
                    <th class="text-end" style="width: 130px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categories as $cat)
                <tr>
                    <td>
                        <span class="badge bg-light text-dark border font-monospace">{{ $cat->code }}</span>
                    </td>
                    <td>
                        <span class="fw-semibold text-dark">{{ $cat->name }}</span>
                    </td>
                    <td>
                        <span class="text-muted small">{{ $cat->description ?? '-' }}</span>
                    </td>
                    <td class="text-center">
                        <a href="{{ route('products.index', ['category_id' => $cat->id]) }}" class="badge bg-primary-subtle text-primary border border-primary-subtle text-decoration-none">
                            {{ $cat->products_count }} Barang
                        </a>
                    </td>
                    <td class="text-end">
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editCategoryModal{{ $cat->id }}" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <form action="{{ route('categories.destroy', $cat) }}" method="POST" onsubmit="return confirm('Hapus kategori ini?');" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus" {{ $cat->products_count > 0 ? 'disabled' : '' }}>
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>

                <!-- Modal Edit Category -->
                <div class="modal fade" id="editCategoryModal{{ $cat->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow">
                            <form action="{{ route('categories.update', $cat) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-header">
                                    <h5 class="modal-title fw-bold">Edit Kategori</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold">Kode Kategori</label>
                                        <input type="text" name="code" class="form-control text-uppercase font-monospace" value="{{ $cat->code }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold">Nama Kategori</label>
                                        <input type="text" name="name" class="form-control" value="{{ $cat->name }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold">Deskripsi</label>
                                        <textarea name="description" class="form-control" rows="2">{{ $cat->description }}</textarea>
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
        {{ $categories->links('pagination::bootstrap-5') }}
    </div>
    @else
    <div class="text-center py-5">
        <i class="bi bi-tags text-muted fs-1"></i>
        <h6 class="fw-bold mt-2">Belum Ada Kategori Barang</h6>
    </div>
    @endif
</div>

<!-- Modal Create Category -->
<div class="modal fade" id="createCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('categories.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Tambah Kategori Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Kode Kategori <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control text-uppercase font-monospace" placeholder="Misal: CAT-ELEK" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="Misal: Elektronik & Kelistrikan" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="Keterangan singkat kategori..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Kategori</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
