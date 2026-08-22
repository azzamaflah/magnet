<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class DivisiController extends Controller
{
    /**
     * Kunci setting untuk daftar divisi di database.
     */
    const SETTING_KEY = 'divisi_list';

    /**
     * Divisi default bawaan sistem jika belum ada di DB.
     */
    public static function getDefaults(): array
    {
        return [
            'Seksi IPDS (Integrasi Pengolahan & Diseminasi Statistik)',
            'Seksi IPDS & Nerwilis',
            'Seksi Statistik Sosial',
            'Seksi Statistik Distribusi',
            'Seksi Statistik Produksi',
            'Seksi Neraca Wilayah & Analisis Statistik (Nerwilis)',
            'Subbagian Umum',
        ];
    }

    /**
     * Ambil daftar divisi dari DB (fallback ke default jika belum ada).
     */
    public static function getList(): array
    {
        $setting = Setting::where('key', self::SETTING_KEY)->first();

        if ($setting && $setting->value) {
            $decoded = json_decode($setting->value, true);
            return is_array($decoded) && count($decoded) > 0 ? $decoded : self::getDefaults();
        }

        return self::getDefaults();
    }

    /**
     * Tampilkan halaman manajemen divisi.
     */
    public function index()
    {
        $this->authorizeAdmin();
        $divisiList = self::getList();

        return view('divisi.index', compact('divisiList'));
    }

    /**
     * Tambah divisi baru ke daftar.
     */
    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $request->validate([
            'nama_divisi' => ['required', 'string', 'max:200'],
        ], [
            'nama_divisi.required' => 'Nama divisi wajib diisi.',
            'nama_divisi.max'      => 'Nama divisi maksimal 200 karakter.',
        ]);

        $nama   = trim(strip_tags($request->nama_divisi));
        $list   = self::getList();

        // Cek duplikat (case-insensitive)
        $isDuplicate = collect($list)->contains(fn ($d) => strtolower($d) === strtolower($nama));

        if ($isDuplicate) {
            return back()->withInput()->withErrors(['nama_divisi' => 'Nama divisi sudah ada dalam daftar.']);
        }

        $list[] = $nama;

        Setting::updateOrCreate(
            ['key' => self::SETTING_KEY],
            ['value' => json_encode(array_values($list)), 'type' => 'json']
        );

        return back()->with('success', "Divisi \"{$nama}\" berhasil ditambahkan.");
    }

    /**
     * Hapus divisi dari daftar berdasarkan index.
     */
    public function destroy(Request $request)
    {
        $this->authorizeAdmin();

        $request->validate([
            'index' => ['required', 'integer', 'min:0'],
        ]);

        $list = self::getList();
        $idx  = (int) $request->index;

        if (!isset($list[$idx])) {
            return back()->withErrors(['index' => 'Divisi tidak ditemukan.']);
        }

        $deleted = $list[$idx];
        unset($list[$idx]);

        Setting::updateOrCreate(
            ['key' => self::SETTING_KEY],
            ['value' => json_encode(array_values($list)), 'type' => 'json']
        );

        return back()->with('success', "Divisi \"{$deleted}\" berhasil dihapus.");
    }

    /**
     * Update nama divisi (rename).
     */
    public function update(Request $request)
    {
        $this->authorizeAdmin();

        $request->validate([
            'index'      => ['required', 'integer', 'min:0'],
            'nama_divisi' => ['required', 'string', 'max:200'],
        ], [
            'nama_divisi.required' => 'Nama divisi wajib diisi.',
        ]);

        $list  = self::getList();
        $idx   = (int) $request->index;
        $nama  = trim(strip_tags($request->nama_divisi));

        if (!isset($list[$idx])) {
            return back()->withErrors(['index' => 'Divisi tidak ditemukan.']);
        }

        $list[$idx] = $nama;

        Setting::updateOrCreate(
            ['key' => self::SETTING_KEY],
            ['value' => json_encode(array_values($list)), 'type' => 'json']
        );

        return back()->with('success', "Divisi berhasil diperbarui menjadi \"{$nama}\".");
    }

    /**
     * Reset daftar divisi ke default bawaan sistem.
     */
    public function reset()
    {
        $this->authorizeAdmin();

        Setting::updateOrCreate(
            ['key' => self::SETTING_KEY],
            ['value' => json_encode(self::getDefaults()), 'type' => 'json']
        );

        return back()->with('success', 'Daftar divisi berhasil direset ke pengaturan awal.');
    }

    private function authorizeAdmin(): void
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'HANYA ADMIN YANG DIIZINKAN.');
        }
    }
}
