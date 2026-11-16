<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PickupSchedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PickupScheduleController extends Controller
{
    public function index(): View
    {
        $schedules = PickupSchedule::query()
            ->orderBy('pickup_date')
            ->paginate(20);

        return view('admin.pickup-schedules.index', compact('schedules'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'pickup_date' => [
                'required',
                'date_format:Y-m-d',
                'after_or_equal:'.now('Asia/Jakarta')->toDateString(),
                Rule::unique('pickup_schedules', 'pickup_date'),
            ],
            'is_active' => ['required', 'boolean'],
        ], [
            'pickup_date.unique' => 'Tanggal pickup tersebut sudah terdaftar.',
            'pickup_date.after_or_equal' => 'Tanggal pickup harus hari ini atau setelahnya.',
        ]);

        PickupSchedule::create($validated);

        return redirect()->route('admin.pickup-schedules.index')
            ->with('success', 'Jadwal pickup berhasil ditambahkan.');
    }

    public function update(Request $request, PickupSchedule $pickupSchedule): RedirectResponse
    {
        $validated = $request->validate([
            'pickup_date' => [
                'required',
                'date_format:Y-m-d',
                Rule::unique('pickup_schedules', 'pickup_date')->ignore($pickupSchedule->id),
            ],
            'is_active' => ['required', 'boolean'],
        ], [
            'pickup_date.unique' => 'Tanggal pickup tersebut sudah terdaftar.',
            'pickup_date.after_or_equal' => 'Tanggal pickup harus hari ini atau setelahnya.',
        ]);

        $pickupSchedule->update($validated);

        return redirect()->route('admin.pickup-schedules.index')
            ->with('success', 'Jadwal pickup berhasil diperbarui.');
    }

    public function destroy(PickupSchedule $pickupSchedule): RedirectResponse
    {
        $pickupSchedule->delete();

        return redirect()->route('admin.pickup-schedules.index')
            ->with('success', 'Jadwal pickup berhasil dihapus.');
    }
}
