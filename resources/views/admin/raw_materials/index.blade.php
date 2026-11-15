@extends('layouts.admin')

@section('title', 'Pembelian Bahan Baku (Impor CSV)')
@section('page_title', 'Stok & Pembelian Bahan Baku (Impor CSV)')

@section('content')
<div class="space-y-8" x-data="{ manualModal: false }">

    <!-- Top Action Row -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-black text-slate-900 tracking-tight">Pengeluaran Stok Bahan Baku</h2>
            <p class="text-xs text-slate-500">
                Impor data pembelian bahan baku via file CSV untuk mencatat beban biaya operasional toko.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <!-- Download Template CSV Button -->
            <a href="{{ route('admin.raw-materials.template') }}"
               class="px-4 py-2.5 bg-orange-50 hover:bg-orange-100 text-orange-700 border border-orange-200 font-bold text-xs rounded-xl shadow-sm transition active:scale-95 flex items-center gap-2">
                <i data-lucide="download" class="w-4 h-4 text-orange-600"></i>
                <span>Download Template CSV</span>
            </a>

            <!-- Manual Add Entry Button -->
            <button type="button" @click="manualModal = true"
                    class="px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white font-bold text-xs rounded-xl shadow-md shadow-red-600/20 transition active:scale-95 flex items-center gap-2">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Catat Manual</span>
            </button>
        </div>
    </div>

    <!-- Spending Summary & CSV Import Section -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Left 7 Cols: Upload CSV Form Card (FEATURE ADMIN 5) -->
        <div class="lg:col-span-7 bg-white p-6 sm:p-7 rounded-3xl border-2 border-dashed border-red-200 shadow-sm space-y-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-red-100 text-red-600 flex items-center justify-center font-bold">
                    <i data-lucide="upload-cloud" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="text-sm font-black uppercase tracking-wider text-slate-900">
                        Unggah / Impor File CSV Bahan Baku
                    </h3>
                    <p class="text-xs text-slate-500">
                        Format kolom: <code class="font-bold text-red-600">tanggal, nama_bahan, jumlah, satuan, harga_total, keterangan</code>
                    </p>
                </div>
            </div>

            <form action="{{ route('admin.raw-materials.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 text-center space-y-2">
                    <input type="file"
                           name="csv_file"
                           accept=".csv,text/csv,text/plain"
                           required
                           class="block w-full text-xs text-slate-500
                                  file:mr-4 file:py-2.5 file:px-5
                                  file:rounded-xl file:border-0
                                  file:text-xs file:font-black
                                  file:bg-red-600 file:text-white
                                  hover:file:bg-red-700 cursor-pointer">
                    <p class="text-[11px] text-slate-400">
                        File .CSV diproses otomatis ke database dan dimasukkan ke dalam perhitungan laba bersih.
                    </p>
                </div>

                <div class="flex items-center justify-between pt-1">
                    <a href="{{ route('admin.raw-materials.template') }}" class="text-xs font-bold text-orange-600 hover:underline flex items-center gap-1">
                        <i data-lucide="file-text" class="w-3.5 h-3.5"></i>
                        <span>Lihat Contoh Format (.csv)</span>
                    </a>

                    <button type="submit"
                            class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white font-black text-xs rounded-xl shadow-md shadow-red-600/20 transition active:scale-95 flex items-center gap-2">
                        <i data-lucide="upload" class="w-4 h-4"></i>
                        <span>Proses Impor CSV</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Right 5 Cols: Total Spending Card -->
        <div class="lg:col-span-5 bg-gradient-to-br from-amber-500 via-orange-500 to-red-600 text-white p-6 sm:p-7 rounded-3xl shadow-xl flex flex-col justify-between space-y-4">
            <div class="space-y-2">
                <span class="inline-block px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-xs font-black uppercase tracking-wider">
                    Total Pengeluaran Stok
                </span>
                <p class="text-3xl sm:text-4xl font-black tracking-tight">
                    Rp {{ number_format($totalSpending, 0, ',', '.') }}
                </p>
                <p class="text-xs text-orange-100 leading-relaxed">
                    Pengeluaran ini otomatis menjadi pengurang omset dalam rekap laba bersih toko Dapur KaTanya.
                </p>
            </div>

            <div class="pt-4 border-t border-white/20 flex items-center justify-between text-xs font-bold">
                <span>Total Data Tercatat: {{ $purchases->total() }} Entri</span>
                <a href="{{ route('admin.sales.index') }}" class="hover:underline flex items-center gap-1">
                    <span>Lihat Rekap Laba</span>
                    <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                </a>
            </div>
        </div>

    </div>

    <!-- Purchases Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden space-y-4 p-6 sm:p-8">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
            <div>
                <h3 class="text-base font-black text-slate-900">Riwayat Pembelian Bahan Baku</h3>
                <p class="text-xs text-slate-500">Daftar transaksi pembelian bahan baku hasil impor CSV & pencatatan manual</p>
            </div>

            <!-- Search Form -->
            <form action="{{ route('admin.raw-materials.index') }}" method="GET" class="flex items-center gap-2">
                <input type="text"
                       name="search"
                       value="{{ $search }}"
                       placeholder="Cari nama bahan baku..."
                       class="text-xs px-3.5 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-red-500 w-52">
                <button type="submit" class="px-3.5 py-2 bg-slate-800 text-white rounded-xl text-xs font-bold hover:bg-slate-700">
                    Cari
                </button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-400 uppercase tracking-wider font-bold border-b border-slate-200">
                        <th class="py-3 px-3">Tanggal Beli</th>
                        <th class="py-3 px-3">Nama Bahan Baku</th>
                        <th class="py-3 px-3">Jumlah / Satuan</th>
                        <th class="py-3 px-3">Biaya Total (Rp)</th>
                        <th class="py-3 px-3">Keterangan / Supplier</th>
                        <th class="py-3 px-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($purchases as $item)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="py-3.5 px-3 font-bold text-slate-800">
                                {{ $item->purchase_date->format('d/m/Y') }}
                            </td>
                            <td class="py-3.5 px-3 font-extrabold text-slate-900">
                                {{ $item->material_name }}
                            </td>
                            <td class="py-3.5 px-3 text-slate-600 font-semibold">
                                {{ $item->quantity }} {{ $item->unit }}
                            </td>
                            <td class="py-3.5 px-3 font-black text-red-600 text-sm">
                                Rp {{ number_format($item->total_cost, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-3 text-slate-500">
                                {{ $item->notes ?: '-' }}
                            </td>
                            <td class="py-3.5 px-3 text-right">
                                <form action="{{ route('admin.raw-materials.destroy', $item) }}"
                                      method="POST"
                                      onsubmit="return confirm('Hapus catatan pembelian bahan baku ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 rounded-lg hover:bg-red-50 transition">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-slate-400">
                                Belum ada catatan pembelian bahan baku. Silakan impor file CSV atau gunakan pencatatan manual.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($purchases->hasPages())
            <div class="pt-4 border-t border-slate-100">
                {{ $purchases->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Catat Manual -->
    <div x-cloak
         x-show="manualModal"
         @keydown.escape.window="manualModal = false"
         class="fixed inset-0 z-50 overflow-y-auto"
         role="dialog"
         aria-modal="true">

        <div class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm" @click="manualModal = false"></div>

        <div class="flex items-center justify-center min-h-screen p-4 text-center">
            <div class="relative bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 text-left shadow-2xl overflow-hidden border border-slate-100 space-y-5">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-base font-black text-slate-900">Catat Pembelian Bahan Baku</h3>
                    <button @click="manualModal = false" class="text-slate-400 hover:text-slate-600">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <form action="{{ route('admin.raw-materials.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Pembelian <span class="text-red-600">*</span></label>
                        <input type="date" name="purchase_date" value="{{ date('Y-m-d') }}" required
                               class="w-full text-xs px-3.5 py-2 rounded-xl border border-slate-200">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nama Bahan Baku <span class="text-red-600">*</span></label>
                        <input type="text" name="material_name" placeholder="Contoh: Keju Quick Melt Mozzarella" required
                               class="w-full text-xs px-3.5 py-2 rounded-xl border border-slate-200">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Jumlah (Qty) <span class="text-red-600">*</span></label>
                            <input type="number" name="quantity" step="0.1" min="0.1" placeholder="Contoh: 5" required
                                   class="w-full text-xs px-3.5 py-2 rounded-xl border border-slate-200">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Satuan <span class="text-red-600">*</span></label>
                            <input type="text" name="unit" placeholder="kg / box / pack / liter" required
                                   class="w-full text-xs px-3.5 py-2 rounded-xl border border-slate-200">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Total Biaya Pembelian (Rp) <span class="text-red-600">*</span></label>
                        <input type="number" name="total_cost" step="500" min="0" placeholder="Contoh: 175000" required
                               class="w-full text-xs px-3.5 py-2 rounded-xl border border-slate-200 font-bold text-slate-800">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Keterangan / Supplier (Opsional)</label>
                        <input type="text" name="notes" placeholder="Contoh: Toko Berkah / Pasar Baru"
                               class="w-full text-xs px-3.5 py-2 rounded-xl border border-slate-200">
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" @click="manualModal = false" class="px-4 py-2 border rounded-xl text-xs font-bold text-slate-600">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl text-xs font-black shadow-md">
                            Simpan Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
