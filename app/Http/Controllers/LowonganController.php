<?php

namespace App\Http\Controllers;

use App\Http\Requests\LowonganRequest;
use App\Models\Lowongan;
use Illuminate\Http\Request;

class LowonganController extends Controller
{
    /**
     * Daftar divisi resmi di BPS Kabupaten Bantul untuk opsi form.
     */
    public static array $divisiList = [
        'Seksi IPDS (Integrasi Pengolahan & Diseminasi Statistik)',
        'Seksi IPDS & Nerwilis',
        'Seksi Statistik Sosial',
        'Seksi Statistik Distribusi',
        'Seksi Statistik Produksi',
        'Seksi Neraca Wilayah & Analisis Statistik (Nerwilis)',
        'Subbagian Umum',
    ];

    /**
     * Display a listing of vacancies.
     */
    public function index(Request $request)
    {
        $query = Lowongan::withCount([
            'pendaftarans as total_pelamar',
            'pendaftarans as disetujui_count' => function ($q) {
                $q->where('status', 'approved');
            }
        ]);

        // Filter untuk user biasa: default tampilkan yang buka terlebih dahulu
        if (!auth()->user()->isAdmin()) {
            // User melihat semua, namun bisa filter status jika diinginkan
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul_posisi', 'like', '%' . $search . '%')
                  ->orWhere('divisi', 'like', '%' . $search . '%')
                  ->orWhere('kualifikasi', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('divisi') && $request->divisi !== 'all') {
            $query->where('divisi', $request->divisi);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $lowongans = $query->latest()->paginate(9)->withQueryString();
        $divisiList = self::$divisiList;

        return view('lowongan.index', compact('lowongans', 'divisiList'));
    }

    /**
     * Show the form for creating a new vacancy (Admin only).
     */
    public function create()
    {
        $this->authorizeAdmin();
        $divisiList = self::$divisiList;

        return view('lowongan.create', compact('divisiList'));
    }

    /**
     * Store a newly created vacancy in storage.
     */
    public function store(LowonganRequest $request)
    {
        $this->authorizeAdmin();

        $validated = $request->validated();
        $validated['judul_posisi'] = strip_tags($validated['judul_posisi']);
        $validated['divisi']       = strip_tags($validated['divisi']);

        Lowongan::create($validated);

        return redirect()->route('lowongan.index')->with('success', 'Lowongan magang berhasil diterbitkan!');
    }

    /**
     * Display the specified vacancy.
     */
    public function show(Lowongan $lowongan)
    {
        $lowongan->loadCount([
            'pendaftarans as total_pelamar',
            'pendaftarans as disetujui_count' => function ($q) {
                $q->where('status', 'approved');
            }
        ]);

        $otherLowongans = Lowongan::where('id', '!=', $lowongan->id)
            ->where('status', 'buka')
            ->latest()
            ->take(3)
            ->get();

        return view('lowongan.show', compact('lowongan', 'otherLowongans'));
    }

    /**
     * Show the form for editing the specified vacancy (Admin only).
     */
    public function edit(Lowongan $lowongan)
    {
        $this->authorizeAdmin();
        $divisiList = self::$divisiList;

        return view('lowongan.edit', compact('lowongan', 'divisiList'));
    }

    /**
     * Update the specified vacancy in storage.
     */
    public function update(LowonganRequest $request, Lowongan $lowongan)
    {
        $this->authorizeAdmin();

        $validated = $request->validated();
        $validated['judul_posisi'] = strip_tags($validated['judul_posisi']);
        $validated['divisi']       = strip_tags($validated['divisi']);

        $lowongan->update($validated);

        return redirect()->route('lowongan.index')->with('success', 'Data lowongan magang berhasil diperbarui!');
    }

    /**
     * Remove the specified vacancy from storage (Admin only).
     */
    public function destroy(Lowongan $lowongan)
    {
        $this->authorizeAdmin();

        $lowongan->delete();

        return redirect()->route('lowongan.index')->with('success', 'Lowongan magang berhasil dihapus!');
    }

    /**
     * Helper to verify if the authenticated user is an admin.
     */
    private function authorizeAdmin(): void
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'HANYA ADMIN YANG DIIZINKAN MENGAKSES HALAMAN INI.');
        }
    }
}
