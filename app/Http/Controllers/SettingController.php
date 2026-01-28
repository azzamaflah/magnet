<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        // Mencari setting atau membuat default jika database kosong
        $setting = Setting::firstOrCreate(
            ['key' => 'min_durasi_magang'],
            ['value' => '3', 'type' => 'bulan']
        );

        return view('settings.index', compact('setting'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'value' => 'required|integer|min:1',
            'type' => 'required|in:bulan,hari'
        ]);

        Setting::updateOrCreate(
            ['key' => 'min_durasi_magang'],
            ['value' => $request->value, 'type' => $request->type]
        );

        return back()->with('success', 'Pengaturan berhasil diperbarui!');
    }
}
