<?php

namespace App\Http\Controllers;

use App\Http\Requests\PendaftaranRequest;
use App\Models\Lowongan;
use App\Models\Pendaftaran;
use App\Models\Setting;
use App\Services\PendaftaranService;
use Illuminate\Http\Request;

class PendaftaranController extends Controller
{
    public function __construct(
        protected PendaftaranService $pendaftaranService
    ) {}

    /**
     * Display a listing of registrations (Admin: all, User: own).
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $filters = $request->only([
            'status', 'lowongan_id', 'kampus', 'konfirmasi',
            'year', 'month', 'search', 'sort'
        ]);

        $data = $this->pendaftaranService->getFilteredPendaftarans($user, $filters);
        $lowongans = Lowongan::select('id', 'judul_posisi', 'divisi')->orderBy('divisi')->get();

        return view('daftar.index', [
            'pendaftarans'       => $data['pendaftarans'],
            'availableYears'     => $data['yearsList'],
            'lowongans'          => $lowongans,
            'kampusList'         => $data['kampusList'],
            'selectedStatus'     => $data['selectedStatus'],
            'selectedLowongan'   => $data['selectedLowongan'],
            'selectedKampus'     => $data['selectedKampus'],
            'selectedKonfirmasi' => $data['selectedKonfirmasi'],
            'selectedYear'       => $data['selectedYear'],
            'selectedMonth'      => $data['selectedMonth'],
            'search'             => $data['search'],
            'sort'               => $data['sort'],
            'countAll'           => $data['counts']['all'],
            'countPending'       => $data['counts']['pending'],
            'countApproved'      => $data['counts']['approved'],
            'countConditional'   => $data['counts']['conditional'],
            'countRejected'      => $data['counts']['rejected'],
        ]);
    }

    /**
     * Show the form for creating a new registration.
     */
    public function create(Request $request)
    {
        $lowongans = Lowongan::where('status', 'buka')->get();
        $selectedLowonganId = $request->query('lowongan_id');

        $setting    = Setting::where('key', 'min_durasi_magang')->first();
        $minDurasi  = (int) ($setting->value ?? 3);
        $tipeDurasi = $setting->type ?? 'bulan';

        return view('daftar.create', compact('lowongans', 'selectedLowonganId', 'minDurasi', 'tipeDurasi'));
    }

    /**
     * Store a newly created registration in storage.
     */
    public function store(PendaftaranRequest $request)
    {
        $validated = $request->validated();
        $validated['email']          = auth()->user()->email;
        $validated['nama_pendaftar'] = strip_tags($validated['nama_pendaftar']);
        $validated['asal_kampus']    = strip_tags($validated['asal_kampus']);
        $validated['prodi']          = strip_tags($validated['prodi']);

        $files = [
            'surat_permohonan' => $request->file('surat_permohonan'),
            'surat_kampus'     => $request->file('surat_kampus'),
            'pas_foto'         => $request->file('pas_foto'),
        ];

        $this->pendaftaranService->createRegistration(auth()->user(), $validated, $files);

        return redirect()->route('daftar.index')->with('success', 'Pendaftaran berhasil dikirim! Silakan tunggu konfirmasi dari Admin.');
    }

    /**
     * Display the specified registration.
     */
    public function show(Pendaftaran $pendaftaran)
    {
        $this->authorizeOwnerOrAdmin($pendaftaran);
        $pendaftaran->load('lowongan');

        return view('daftar.show', compact('pendaftaran'));
    }

    /**
     * Show the form for editing the specified registration.
     */
    public function edit(Pendaftaran $pendaftaran)
    {
        if ($pendaftaran->user_id !== auth()->id()) {
            abort(403, 'AKSES DITOLAK. Anda hanya dapat mengedit pendaftaran Anda sendiri.');
        }

        if ($pendaftaran->status === 'approved') {
            return redirect()->route('daftar.index')->withErrors('Pendaftaran yang sudah disetujui tidak dapat diubah.');
        }

        $lowongans  = Lowongan::where('status', 'buka')->get();
        $setting    = Setting::where('key', 'min_durasi_magang')->first();
        $minDurasi  = (int) ($setting->value ?? 3);
        $tipeDurasi = $setting->type ?? 'bulan';

        return view('daftar.edit', compact('pendaftaran', 'lowongans', 'minDurasi', 'tipeDurasi'));
    }

    /**
     * Update the specified registration in storage.
     */
    public function update(PendaftaranRequest $request, Pendaftaran $pendaftaran)
    {
        if ($pendaftaran->user_id !== auth()->id()) {
            abort(403, 'AKSES DITOLAK.');
        }

        if ($pendaftaran->status === 'approved') {
            return redirect()->route('daftar.index')->withErrors('Pendaftaran yang sudah disetujui tidak dapat diubah.');
        }

        $validated = $request->validated();
        $validated['email']          = auth()->user()->email;
        $validated['nama_pendaftar'] = strip_tags($validated['nama_pendaftar']);
        $validated['asal_kampus']    = strip_tags($validated['asal_kampus']);
        $validated['prodi']          = strip_tags($validated['prodi']);

        $files = [
            'surat_permohonan' => $request->file('surat_permohonan'),
            'surat_kampus'     => $request->file('surat_kampus'),
            'pas_foto'         => $request->file('pas_foto'),
        ];

        $this->pendaftaranService->updateRegistration($pendaftaran, $validated, $files);

        return redirect()->route('daftar.index')->with('success', 'Pendaftaran Anda berhasil diperbarui dan telah dikirim ulang untuk direview.');
    }

    /**
     * Remove the specified registration from storage.
     */
    public function destroy(Pendaftaran $pendaftaran)
    {
        $isOwner = $pendaftaran->user_id === auth()->id();
        $isAdmin = auth()->user()->isAdmin();

        if (!$isAdmin && (!$isOwner || $pendaftaran->status !== 'pending')) {
            abort(403, 'Data tidak dapat dihapus. Hanya Admin atau data Pending yang bisa dihapus.');
        }

        $this->pendaftaranService->deleteRegistration($pendaftaran);

        return redirect()->route('daftar.index')->with('success', 'Data pendaftaran dan berkas berhasil dihapus.');
    }

    /**
     * Update the status of registration by Admin (Approved / Rejected / Conditional).
     */
    public function updateStatus(Request $request, Pendaftaran $pendaftaran)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'HANYA ADMIN YANG DIIZINKAN.');
        }

        $request->validate([
            'status'  => 'required|string|in:approved,rejected,conditional',
            'remarks' => 'nullable|string|max:1000',
        ]);

        $status  = $request->input('status');
        $remarks = $request->input('remarks');

        if (in_array($status, ['rejected', 'conditional']) && empty($remarks)) {
            return back()->withInput()->withErrors(['remarks' => 'Catatan wajib diisi untuk status Ditolak atau Bersyarat.']);
        }

        try {
            $result = $this->pendaftaranService->updateApprovalStatus($pendaftaran, $status, $remarks);
            return redirect()->route('daftar.index')->with('success', $result['message']);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    /**
     * Download attached document safely.
     */
    public function downloadFile(Pendaftaran $pendaftaran, string $field)
    {
        $this->authorizeOwnerOrAdmin($pendaftaran);
        return $this->pendaftaranService->downloadDocument($pendaftaran, $field);
    }

    /**
     * Public endpoint to confirm attendance from email link.
     */
    public function konfirmasiKehadiran(Pendaftaran $pendaftaran)
    {
        $result = $this->pendaftaranService->confirmAttendance($pendaftaran);

        return view('daftar.konfirmasi-sukses', [
            'pendaftaran' => $result['pendaftaran'],
            'message'     => $result['message'],
            'isError'     => $result['isError'],
        ]);
    }

    /**
     * Helper to verify if the authenticated user is the owner or an admin.
     */
    private function authorizeOwnerOrAdmin(Pendaftaran $pendaftaran): void
    {
        if (!auth()->user()->isAdmin() && $pendaftaran->user_id !== auth()->id()) {
            abort(403, 'AKSES DITOLAK');
        }
    }
}
