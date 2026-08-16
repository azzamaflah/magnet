<?php

namespace App\Http\Controllers;

use App\Http\Requests\MagangRequest;
use App\Models\Magang;
use App\Models\Pendaftaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MagangController extends Controller
{
    /**
     * Display a listing of magang participants.
     */
    public function index(Request $request)
    {
        $query = Magang::query();

        $availableYears = (clone $query)
            ->selectRaw('YEAR(tanggal_mulai) as year')
            ->distinct()
            ->whereNotNull('tanggal_mulai')
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->filter();

        $selectedYear = $request->input('year', 'all');

        if ($request->filled('search')) {
            $query->where('nama', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('kampus')) {
            $query->where('asal_kampus', $request->kampus);
        }

        if ($selectedYear !== 'all') {
            $query->whereYear('tanggal_mulai', $selectedYear);
        }

        $kampusList = Magang::select('asal_kampus')
            ->distinct()
            ->orderBy('asal_kampus', 'asc')
            ->pluck('asal_kampus')
            ->filter();

        $magangs = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('magang.index', compact('magangs', 'kampusList', 'availableYears', 'selectedYear'));
    }

    /**
     * Show the form for creating a new magang data (Admin only).
     */
    public function create()
    {
        return view('magang.create');
    }

    /**
     * Store a newly created magang data in storage.
     */
    public function store(MagangRequest $request)
    {
        $validated = $request->validated();

        // Sanitasi input teks
        $validated['nama']        = strip_tags($validated['nama']);
        $validated['asal_kampus'] = strip_tags($validated['asal_kampus']);
        $validated['prodi']       = strip_tags($validated['prodi']);

        // Handle upload foto
        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('foto_magang', 'public');
        }

        Magang::create($validated);

        return redirect()->route('magang.index')->with('success', 'Data magang berhasil ditambahkan!');
    }

    /**
     * Display the specified magang profile.
     */
    public function show(Magang $magang)
    {
        return view('magang.show', compact('magang'));
    }

    /**
     * Show the form for editing the specified magang profile.
     */
    public function edit(Magang $magang)
    {
        $this->authorizeOwnerOrAdmin($magang);

        return view('magang.edit', compact('magang'));
    }

    /**
     * Update the specified magang profile in storage.
     */
    public function update(MagangRequest $request, Magang $magang)
    {
        $this->authorizeOwnerOrAdmin($magang);

        $validated = $request->validated();

        // Sanitasi input teks
        $validated['nama']        = strip_tags($validated['nama']);
        $validated['asal_kampus'] = strip_tags($validated['asal_kampus']);
        $validated['prodi']       = strip_tags($validated['prodi']);

        DB::transaction(function () use ($request, $magang, &$validated) {
            if ($request->hasFile('foto')) {
                $oldMagangPhoto = $magang->foto;
                $newPhotoPath   = $request->file('foto')->store('foto_magang', 'public');
                $validated['foto'] = $newPhotoPath;

                // Sinkronkan ke pendaftaran terkait
                $pendaftaran = Pendaftaran::where('user_id', $magang->user_id)->first();
                if ($pendaftaran) {
                    $oldPendaftaranPhoto = $pendaftaran->pas_foto;
                    $pendaftaran->update(['pas_foto' => $newPhotoPath]);

                    if ($oldPendaftaranPhoto && $oldPendaftaranPhoto !== $oldMagangPhoto && Storage::disk('public')->exists($oldPendaftaranPhoto)) {
                        Storage::disk('public')->delete($oldPendaftaranPhoto);
                    }
                }

                if ($oldMagangPhoto && Storage::disk('public')->exists($oldMagangPhoto)) {
                    Storage::disk('public')->delete($oldMagangPhoto);
                }
            }

            $magang->update($validated);
        });

        return redirect()->route('magang.show', $magang)->with('success', 'Data dan Foto berhasil diperbarui di semua data!');
    }

    /**
     * Remove the specified magang participant (Admin only).
     */
    public function destroy(Magang $magang)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        DB::transaction(function () use ($magang) {
            if ($magang->foto && Storage::disk('public')->exists($magang->foto)) {
                Storage::disk('public')->delete($magang->foto);
            }

            $pendaftaran = Pendaftaran::where('user_id', $magang->user_id)->first();
            if ($pendaftaran) {
                $pendaftaran->update([
                    'status'  => 'rejected',
                    'remarks' => 'Data dihapus dari daftar peserta Magang aktif oleh Admin.',
                ]);
            }

            $magang->delete();
        });

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
