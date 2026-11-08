@extends('layouts.app')

@section('title', 'Riwayat Transaksi Stok')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold m-0 text-dark">Riwayat Transaksi Stok</h3>
        <p class="text-muted small m-0">Log seluruh aktivitas barang masuk dan barang keluar secara kronologis.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('transactions.create-in') }}" class="btn btn-success d-inline-flex align-items-center gap-2 shadow-sm">
            <i class="bi bi-box-arrow-in-down"></i>
            <span>Catat Barang Masuk</span>
        </a>
        <a href="{{ route('transactions.create-out') }}" class="btn btn-danger d-inline-flex align-items-center gap-2 shadow-sm">
            <i class="bi bi-box-arrow-up-right"></i>
            <span>Catat Barang Keluar</span>
        </a>
    </div>
</div>

<!-- Stats mini cards -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card-custom p-3 d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small fw-semibold">Transaksi Masuk</span>
                <h4 class="fw-bold text-success m-0">{{ $countIn }}</h4>
            </div>
            <div class="stat-icon bg-light-green" style="width: 40px; height: 40px; font-size: 1.2rem;">
                <i class="bi bi-box-arrow-in-down"></i>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card-custom p-3 d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small fw-semibold">Total Item Masuk</span>
                <h4 class="fw-bold text-success m-0">+{{ number_format($sumInItems) }}</h4>
            </div>
            <div class="stat-icon bg-light-green" style="width: 40px; height: 40px; font-size: 1.2rem;">
                <i class="bi bi-stack"></i>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card-custom p-3 d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small fw-semibold">Transaksi Keluar</span>
                <h4 class="fw-bold text-danger m-0">{{ $countOut }}</h4>
            </div>
            <div class="stat-icon bg-light-red" style="width: 40px; height: 40px; font-size: 1.2rem;">
                <i class="bi bi-box-arrow-up-right"></i>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card-custom p-3 d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small fw-semibold">Total Item Keluar</span>
                <h4 class="fw-bold text-danger m-0">-{{ number_format($sumOutItems) }}</h4>
            </div>
            <div class="stat-icon bg-light-red" style="width: 40px; height: 40px; font-size: 1.2rem;">
                <i class="bi bi-cart-x"></i>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card-custom p-3 mb-4">
    <form action="{{ route('transactions.index') }}" method="GET" class="row g-2 align-items-center">
        <div class="col-12 col-md-3">
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" name="search" class="form-control border-start-0" placeholder="No. ref, pemasok, penerima..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-6 col-md-2">
            <select name="type" class="form-select">
                <option value="">Semua Jenis</option>
                <option value="in" {{ request('type') == 'in' ? 'selected' : '' }}>Barang Masuk (In)</option>
                <option value="out" {{ request('type') == 'out' ? 'selected' : '' }}>Barang Keluar (Out)</option>
            </select>
        </div>
        <div class="col-6 col-md-3">
            <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}" title="Tanggal Mulai">
        </div>
        <div class="col-6 col-md-3">
            <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}" title="Tanggal Selesai">
        </div>
        <div class="col-6 col-md-1 d-flex gap-1">
            <button type="submit" class="btn btn-primary w-100" title="Terapkan">
                <i class="bi bi-funnel-fill"></i>
            </button>
            @if(request()->anyFilled(['search', 'type', 'start_date', 'end_date']))
                <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary" title="Reset">
                    <i class="bi bi-x-lg"></i>
                </a>
            @endif
        </div>
    </form>
</div>

<!-- Table Card -->
<div class="card-custom">
    @if($transactions->count() > 0)
    <div class="table-responsive">
        <table class="table table-custom align-middle mb-0 table-hover">
            <thead>
                <tr>
                    <th>No. Referensi</th>
                    <th>Jenis Transaksi</th>
                    <th>Tanggal</th>
                    <th>Mitra / Tujuan</th>
                    <th class="text-center">Total Kuantitas</th>
                    <th class="text-end">Total Nominal</th>
                    <th>Operator</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $tx)
                <tr>
                    <td>
                        <a href="{{ route('transactions.show', $tx) }}" class="fw-bold text-dark text-decoration-none">
                            <code>{{ $tx->reference_no }}</code>
                        </a>
                    </td>
                    <td>
                        @if($tx->type === 'in')
                            <span class="badge-soft-success">
                                <i class="bi bi-box-arrow-in-down me-1"></i>Barang Masuk
                            </span>
                        @else
                            <span class="badge-soft-danger">
                                <i class="bi bi-box-arrow-up-right me-1"></i>Barang Keluar
                            </span>
                        @endif
                    </td>
                    <td>
                        <span class="small">{{ $tx->transaction_date->translatedFormat('d M Y') }}</span>
                    </td>
                    <td>
                        <div class="small fw-medium text-dark">
                            @if($tx->type === 'in')
                                <i class="bi bi-truck text-muted me-1"></i>{{ $tx->supplier->name ?? 'Pemasok Umum' }}
                            @else
                                <i class="bi bi-send text-muted me-1"></i>{{ $tx->recipient ?? 'Tujuan Internal' }}
                            @endif
                        </div>
                    </td>
                    <td class="text-center">
                        <span class="fw-bold">{{ number_format($tx->total_items) }}</span> <span class="small text-muted">item</span>
                    </td>
                    <td class="text-end">
                        <span class="fw-semibold text-dark">Rp {{ number_format($tx->total_amount, 0, ',', '.') }}</span>
                    </td>
                    <td>
                        <span class="small text-muted"><i class="bi bi-person me-1"></i>{{ $tx->user->name ?? '-' }}</span>
                    </td>
                    <td class="text-end">
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('transactions.show', $tx) }}" class="btn btn-outline-primary" title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('transactions.print', $tx) }}" target="_blank" class="btn btn-outline-secondary" title="Cetak Surat Jalan / Bukti">
                                <i class="bi bi-printer"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="p-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="text-muted small">
            Menampilkan {{ $transactions->firstItem() ?? 0 }} - {{ $transactions->lastItem() ?? 0 }} dari {{ $transactions->total() }} transaksi
        </div>
        <div>
            {{ $transactions->links('pagination::bootstrap-5') }}
        </div>
    </div>
    @else
    <div class="text-center py-5">
        <i class="bi bi-inbox text-muted fs-1"></i>
        <h6 class="fw-bold mt-2">Belum Ada Transaksi Ditemukan</h6>
        <p class="text-muted small">Silakan sesuaikan filter pencarian atau buat transaksi baru.</p>
    </div>
    @endif
</div>
@endsection
