@extends('layouts.admin')

@section('title', 'Jadwal Pickup / Open PO')
@section('page_title', 'Jadwal Pickup / Open PO')

@section('content')
<div class="max-w-5xl space-y-6">
    <section class="rounded-3xl bg-gradient-to-r from-red-600 to-orange-500 p-6 text-white shadow-xl shadow-red-600/15 sm:p-8">
        <div class="flex items-start gap-4">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-white/20">
                <i data-lucide="calendar-days" class="h-6 w-6"></i>
            </div>
            <div>
                <h2 class="text-xl font-black sm:text-2xl">Jadwal Pickup / Open PO</h2>
                <p class="mt-1 text-xs text-orange-50 sm:text-sm">Atur tanggal batch produksi. Hanya jadwal aktif yang akan tersedia saat checkout.</p>
            </div>
        </div>
    </section>

    <section class="space-y-5 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <div>
            <h3 class="text-sm font-black text-slate-900">Tambah Jadwal Pickup</h3>
            <p class="mt-1 text-xs text-slate-500">Hari akan ditampilkan otomatis bersama tanggal, bulan, dan tahun.</p>
        </div>

        <form action="{{ route('admin.pickup-schedules.store') }}" method="POST" class="grid grid-cols-1 items-end gap-4 sm:grid-cols-[minmax(0,1fr)_auto_auto]">
            @csrf
            <div class="space-y-1.5">
                <label for="pickup_date" class="block text-xs font-bold text-slate-700">Tanggal Pickup</label>
                <input id="pickup_date" type="date" name="pickup_date" value="{{ old('pickup_date') }}" min="{{ now('Asia/Jakarta')->toDateString() }}" required
                       class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm focus:border-transparent focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>
            <div class="flex items-center gap-2 pb-2">
                <input type="hidden" name="is_active" value="0">
                <input id="new_schedule_active" type="checkbox" name="is_active" value="1" @checked(old('is_active', true))
                       class="h-4 w-4 rounded border-slate-300 text-red-600 focus:ring-red-500">
                <label for="new_schedule_active" class="text-xs font-semibold text-slate-700">Aktif untuk checkout</label>
            </div>
            <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-red-600 px-5 py-2.5 text-xs font-black text-white shadow-md shadow-red-600/20 transition hover:bg-red-700">
                <i data-lucide="plus" class="h-4 w-4"></i>
                Tambah Jadwal
            </button>
        </form>
    </section>

    <section class="space-y-4">
        <div class="flex flex-wrap items-end justify-between gap-2">
            <div>
                <h3 class="text-sm font-black text-slate-900">Daftar Tanggal PO</h3>
                <p class="mt-1 text-xs text-slate-500">Jadwal nonaktif tetap tersimpan untuk memudahkan pengaturan batch.</p>
            </div>
            <span class="rounded-full bg-orange-50 px-3 py-1 text-[11px] font-bold text-orange-700">{{ $schedules->total() }} jadwal</span>
        </div>

        @forelse($schedules as $schedule)
            <article class="space-y-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <p class="text-sm font-black text-slate-900">
                        {{ $schedule->pickup_date->locale('id')->translatedFormat('l, d F Y') }}
                    </p>
                    @if($schedule->is_active)
                        <span class="rounded-full bg-emerald-50 px-3 py-1 text-[10px] font-black uppercase tracking-wide text-emerald-700">Aktif</span>
                    @else
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-[10px] font-black uppercase tracking-wide text-slate-500">Nonaktif</span>
                    @endif
                </div>

                <div class="flex flex-col gap-3 border-t border-slate-100 pt-4 sm:flex-row sm:items-end">
                    <form action="{{ route('admin.pickup-schedules.update', $schedule) }}" method="POST" class="grid flex-1 grid-cols-1 items-end gap-3 sm:grid-cols-[minmax(0,1fr)_auto_auto]">
                        @csrf
                        @method('PUT')
                        <div class="space-y-1.5">
                            <label for="pickup_date_{{ $schedule->id }}" class="block text-[11px] font-bold text-slate-600">Ubah tanggal</label>
                            <input id="pickup_date_{{ $schedule->id }}" type="date" name="pickup_date" value="{{ $schedule->pickup_date->format('Y-m-d') }}" required
                                   class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs focus:border-transparent focus:outline-none focus:ring-2 focus:ring-red-500">
                        </div>
                        <div class="flex items-center gap-2 pb-2">
                            <input type="hidden" name="is_active" value="0">
                            <input id="schedule_active_{{ $schedule->id }}" type="checkbox" name="is_active" value="1" @checked($schedule->is_active)
                                   class="h-4 w-4 rounded border-slate-300 text-red-600 focus:ring-red-500">
                            <label for="schedule_active_{{ $schedule->id }}" class="whitespace-nowrap text-xs font-semibold text-slate-700">Aktif</label>
                        </div>
                        <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl border border-orange-200 bg-orange-50 px-4 py-2 text-xs font-black text-orange-800 transition hover:bg-orange-100">
                            <i data-lucide="save" class="h-4 w-4"></i>
                            Simpan
                        </button>
                    </form>

                    <form action="{{ route('admin.pickup-schedules.destroy', $schedule) }}" method="POST" onsubmit="return confirm('Hapus jadwal pickup ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-red-200 px-4 py-2 text-xs font-bold text-red-700 transition hover:bg-red-50 sm:w-auto">
                            <i data-lucide="trash-2" class="h-4 w-4"></i>
                            Hapus
                        </button>
                    </form>
                </div>
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-orange-200 bg-orange-50/50 px-5 py-10 text-center">
                <i data-lucide="calendar-plus" class="mx-auto h-8 w-8 text-orange-500"></i>
                <p class="mt-3 text-sm font-bold text-slate-800">Belum ada jadwal pickup</p>
                <p class="mt-1 text-xs text-slate-500">Tambahkan tanggal batch PO agar pembeli dapat memilih jadwal saat checkout.</p>
            </div>
        @endforelse

        @if($schedules->hasPages())
            <div>{{ $schedules->links() }}</div>
        @endif
    </section>
</div>
@endsection
