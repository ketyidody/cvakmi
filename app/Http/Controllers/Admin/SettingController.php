<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingController extends Controller
{
    public function edit()
    {
        return Inertia::render('Admin/Settings/Edit', [
            'setting' => Setting::current(),
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'email' => 'nullable|email|max:255',
            'iban' => 'nullable|string|max:255',
            'watermark_text' => 'nullable|string|max:255',
            'orders_enabled' => 'required|boolean',
        ]);

        Setting::current()->update($validated);

        return redirect()->route('admin.settings.edit')
            ->with('success', 'Settings updated successfully.');
    }
}
