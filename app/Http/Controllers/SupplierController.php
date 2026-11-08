<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = Supplier::withCount('stockTransactions');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
        }

        $suppliers = $query->orderBy('name')->paginate(10)->withQueryString();

        return view('suppliers.index', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:20', 'unique:suppliers,code'],
            'name' => ['required', 'string', 'max:150'],
            'contact_person' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:100'],
            'address' => ['nullable', 'string'],
        ], [
            'code.required' => 'Kode pemasok wajib diisi.',
            'code.unique' => 'Kode pemasok sudah terdaftar.',
            'name.required' => 'Nama pemasok / vendor wajib diisi.',
        ]);

        $validated['code'] = strtoupper($validated['code']);

        Supplier::create($validated);

        return redirect()->route('suppliers.index')
            ->with('success', 'Pemasok baru berhasil didaftarkan.');
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:20', Rule::unique('suppliers', 'code')->ignore($supplier->id)],
            'name' => ['required', 'string', 'max:150'],
            'contact_person' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:100'],
            'address' => ['nullable', 'string'],
        ]);

        $validated['code'] = strtoupper($validated['code']);

        $supplier->update($validated);

        return redirect()->route('suppliers.index')
            ->with('success', "Data pemasok '{$supplier->name}' berhasil diperbarui.");
    }

    public function destroy(Supplier $supplier)
    {
        if ($supplier->stockTransactions()->exists()) {
            return back()->with('error', 'Pemasok tidak dapat dihapus karena memiliki riwayat transaksi pasokan barang.');
        }

        $supplier->delete();

        return redirect()->route('suppliers.index')
            ->with('success', 'Pemasok berhasil dihapus.');
    }
}
