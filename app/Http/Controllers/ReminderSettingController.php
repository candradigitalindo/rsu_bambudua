<?php

namespace App\Http\Controllers;

use App\Models\ReminderSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReminderSettingController extends Controller
{
    public function index()
    {
        $reminders = ReminderSetting::orderBy('type')->orderBy('name')->get();
        return view('pages.master-data.reminder-settings.index', compact('reminders'));
    }

    public function create()
    {
        return view('pages.master-data.reminder-settings.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:obat,checkup',
            'days_before' => 'required|integer|min:1|max:365',
            'message_template' => 'nullable|string',
            'is_active' => 'required|in:0,1'
        ]);

        // Convert string to boolean
        $validated['is_active'] = (bool) $validated['is_active'];

        ReminderSetting::create($validated);

        return redirect()->route('reminder-settings.index')
            ->with('success', 'Reminder berhasil ditambahkan.');
    }

    public function edit(ReminderSetting $reminder_setting)
    {
        return view('pages.master-data.reminder-settings.edit', [
            'reminderSetting' => $reminder_setting
        ]);
    }

    public function update(Request $request, ReminderSetting $reminder_setting)
    {
        // Debug log
        Log::info('Update Reminder - ID: ' . $reminder_setting->id);
        Log::info('Update Reminder - Request Data: ', $request->all());

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:obat,checkup',
            'days_before' => 'required|integer|min:1|max:365',
            'message_template' => 'nullable|string',
            'is_active' => 'required|in:0,1'
        ]);

        // Convert string to boolean
        $validated['is_active'] = (bool) $validated['is_active'];

        Log::info('Update Reminder - Validated Data: ', $validated);

        $reminder_setting->update($validated);

        Log::info('Update Reminder - After Update: ', $reminder_setting->toArray());

        return redirect()->route('reminder-settings.index')
            ->with('success', 'Reminder berhasil diperbarui.');
    }

    public function destroy(ReminderSetting $reminder_setting)
    {
        try {
            $reminder_setting->delete();
            return redirect()->route('reminder-settings.index')
                ->with('success', 'Reminder berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Error deleting reminder setting: ' . $e->getMessage());
            return redirect()->route('reminder-settings.index')
                ->with('error', 'Gagal menghapus reminder.');
        }
    }

    public function toggleStatus(ReminderSetting $reminder_setting)
    {
        $reminder_setting->update([
            'is_active' => !$reminder_setting->is_active
        ]);

        return response()->json([
            'success' => true,
            'is_active' => $reminder_setting->is_active,
            'message' => 'Status reminder berhasil diubah.'
        ]);
    }
}
