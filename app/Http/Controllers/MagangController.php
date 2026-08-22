<?php

namespace App\Http\Controllers;

use App\Http\Requests\MagangRequest;
use App\Models\Lowongan;
use App\Models\Magang;
use App\Models\Setting;
use App\Services\MagangService;
use Illuminate\Http\Request;

class MagangController extends Controller
{
    public function __construct(
        protected MagangService $magangService
    ) {}

    /**
     * Display a listing of magang participants.
     */
    public function index(Request $request)
    {
        $user    = auth()->user();
        $filters = $request->only(['search', 'kampus', 'year', 'visibility']);
        $data    = $this->magangService->getFilteredMagangs($user, $filters);

        return view('magang.index', $data);
    }

    /**
     * Show the form for creating a new magang data (Admin only).
     */
    public function create()
    {
        $lowongans  = Lowongan::all();
        $setting    = Setting::where('key', 'min_durasi_magang')->first();
        $minDurasi  = (int) ($setting->value ?? 3);
        $tipeDurasi = $setting->type ?? 'bulan';

        return view('magang.create', compact('lowongans', 'minDurasi', 'tipeDurasi'));
    }

    /**
     * Store a newly created magang data in storage.
     */
    public function store(MagangRequest $request)
    {
        $validated = $request->validated();
        $validated['nama']        = strip_tags($validated['nama']);
        $validated['asal_kampus'] = strip_tags($validated['asal_kampus']);
        $validated['prodi']       = strip_tags($validated['prodi']);
        $validated['is_hidden']   = $request->boolean('is_hidden');

        $this->magangService->createMagang($validated, $request->file('foto'));

        return redirect()->route('magang.index')->with('success', 'Data magang berhasil ditambahkan!');
    }

    /**
     * Display the specified magang profile.
     */
    public function show(Magang $magang)
    {
        if ($magang->is_hidden && (!auth()->check() || (!auth()->user()->isAdmin() && $magang->user_id !== auth()->id()))) {
            abort(404, 'Data magang tidak ditemukan atau sedang disembunyikan oleh Administrator.');
        }

        return view('magang.show', compact('magang'));
    }

    /**
     * Show the form for editing the specified magang profile.
     */
    public function edit(Magang $magang)
    {
        $this->authorizeOwnerOrAdmin($magang);

        $lowongans  = Lowongan::all();
        $setting    = Setting::where('key', 'min_durasi_magang')->first();
        $minDurasi  = (int) ($setting->value ?? 3);
        $tipeDurasi = $setting->type ?? 'bulan';

        return view('magang.edit', compact('magang', 'lowongans', 'minDurasi', 'tipeDurasi'));
    }

    /**
     * Update the specified magang profile in storage.
     */
    public function update(MagangRequest $request, Magang $magang)
    {
        $this->authorizeOwnerOrAdmin($magang);

        $validated = $request->validated();
        $validated['nama']        = strip_tags($validated['nama']);
        $validated['asal_kampus'] = strip_tags($validated['asal_kampus']);
        $validated['prodi']       = strip_tags($validated['prodi']);

        if (auth()->user()->isAdmin()) {
            $validated['is_hidden'] = $request->boolean('is_hidden');
        }

        $this->magangService->updateMagang($magang, $validated, $request->file('foto'));

        return redirect()->route('magang.show', $magang)->with('success', 'Data dan Foto berhasil diperbarui di semua data!');
    }

    /**
     * Toggle visibility (hide/unhide) of a magang participant (Admin only).
     */
    public function toggleVisibility(Magang $magang)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        $isHidden = $this->magangService->toggleVisibility($magang);

        $statusMsg = $isHidden
            ? "Data peserta {$magang->nama} berhasil disembunyikan dari publik."
            : "Data peserta {$magang->nama} berhasil ditampilkan ke publik.";

        if (request()->wantsJson()) {
            return response()->json([
                'success'   => true,
                'is_hidden' => $isHidden,
                'message'   => $statusMsg,
            ]);
        }

        return redirect()->back()->with('success', $statusMsg);
    }

    /**
     * Remove the specified magang participant (Admin only).
     */
    public function destroy(Magang $magang)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        $this->magangService->deleteMagang($magang);

        return redirect()->route('magang.index')->with('success', 'Peserta dihapus dari daftar aktif. Data pendaftaran telah diarsipkan (Ditolak).');
    }

    /**
     * Helper to verify if the authenticated user is the owner or an admin.
     */
    private function authorizeOwnerOrAdmin(Magang $magang): void
    {
        if (!auth()->user()->isAdmin() && $magang->user_id != auth()->id()) {
            abort(403, 'AKSES DITOLAK. Anda hanya dapat mengedit data Anda sendiri.');
        }
    }
}
