@extends('layouts.app')

@section('title', 'Stock Opname / Penyesuaian Stok')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold m-0 text-dark">Stock Opname / Penyesuaian Stok</h3>
        <p class="text-muted small m-0">Rekonsiliasi pencatatan stok sistem dengan perhitungan fisik aktual di gudang.</p>
    </div>
    <div>
        <a href="{{ route('adjustments.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-2 shadow-sm">
            <i class="bi bi-clipboard-plus"></i>
            <span>Input Opname Baru</span>
        </a>
    </div>
</div>

<div class="card-custom">
    <div class="p-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
        <form action="{{ route('adjustments.index') }}" method="GET" class="d-flex gap-2">
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" name="search" class="form-control border-start-0" placeholder="Cari ref, barang, alasan..." value="{{ request('search') }}">
            </div>
            <button type="submit" class="btn btn-secondary btn-sm px-3">Cari</button>
            @if(request('search'))
                <a href="{{ route('adjustments.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
            @endif
        </form>
    </div>

    @if($adjustments->count() > 0)
    <div class="table-responsive">
        <table class="table table-custom align-middle mb-0 table-hover">
            <thead>
                <tr>
                    <th>No. Referensi</th>
                    <th>Nama Barang & SKU</th>
                    <th>Tanggal</th>
                    <th class="text-center">Stok Sistem</th>
                    <th class="text-center">Fisik Aktual</th>
                    <th class="text-center">Selisih</th>
                    <th>Alasan Penyesuaian</th>
                    <th>Petugas</th>
                </tr>
            </thead>
            <tbody>
                @foreach($adjustments as $adj)
                <tr>
                    <td>
                        <span class="font-monospace fw-bold text-dark">{{ $adj->reference_no }}</span>
                    </td>
                    <td>
                        <a href="{{ route('products.show', $adj->product) }}" class="fw-semibold text-dark text-decoration-none">
                            {{ $adj->product->name ?? 'Barang Dihapus' }}
                        </a>
                        <div class="text-muted small"><code>{{ $adj->product->sku ?? '-' }}</code></div>
                    </td>
                    <td>
                        <span class="small">{{ $adj->adjustment_date->translatedFormat('d M Y') }}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-secondary-subtle text-secondary">{{ $adj->system_stock }}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-primary-subtle text-primary">{{ $adj->physical_stock }}</span>
                    </td>
                    <td class="text-center">
                        <span class="fw-bold {{ $adj->difference >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ $adj->difference >= 0 ? '+' : '' }}{{ $adj->difference }}
                        </span>
                    </td>
                    <td>
                        <span class="small fw-medium text-dark">{{ $adj->reason }}</span>
                        @if($adj->notes)
                            <div class="small text-muted">{{ $adj->notes }}</div>
                        @endif
                    </td>
                    <td>
                        <span class="small text-muted"><i class="bi bi-person me-1"></i>{{ $adj->user->name ?? '-' }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="p-3 border-top">
        {{ $adjustments->links('pagination::bootstrap-5') }}
    </div>
    @else
    <div class="text-center py-5">
        <i class="bi bi-clipboard-check text-muted fs-1"></i>
        <h6 class="fw-bold mt-2">Belum Ada Riwayat Stock Opname</h6>
        <p class="text-muted small">Lakukan audit fisik dan catat penyesuaian jika ditemukan selisih barang.</p>
    </div>
    @endif
</div>
@endsection
