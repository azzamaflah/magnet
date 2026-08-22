<?php

namespace App\Http\Controllers;

use App\Http\Requests\PendaftaranRequest;
use App\Mail\PendaftaranStatusMail;
use App\Models\Lowongan;
use App\Models\Magang;
use App\Models\Pendaftaran;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PendaftaranController extends Controller
{
    /**
     * Display a listing of registrations (Admin: all, User: own).
     */
    public function index(Request $request)
    {
        $user               = auth()->user();
        $selectedStatus     = $request->input('status', 'all');
        $selectedLowongan   = $request->input('lowongan_id', 'all');
        $selectedKampus     = $request->input('kampus', 'all');
        $selectedKonfirmasi = $request->input('konfirmasi', 'all');
        $selectedYear       = $request->input('year', 'all');
        $selectedMonth      = $request->input('month', 'all');
        $search             = $request->input('search');
        $sort               = $request->input('sort', 'latest');

        $baseQuery = $user->isAdmin()
            ? Pendaftaran::with('lowongan')
            : Pendaftaran::with('lowongan')->where('user_id', $user->id);

        // Hitung Tab Status Counters
        $statusCounts = (clone $baseQuery)
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        $countAll         = (clone $baseQuery)->count();
        $countPending     = $statusCounts->get('pending', 0);
        $countApproved    = $statusCounts->get('approved', 0);
        $countConditional = $statusCounts->get('conditional', 0);
        $countRejected    = $statusCounts->get('rejected', 0);

        // Query Utama dengan Filter
        $query = clone $baseQuery;

        if ($selectedStatus !== 'all') {
            $query->where('status', $selectedStatus);
        }

        if ($selectedLowongan !== 'all') {
            if ($selectedLowongan === 'umum') {
                $query->whereNull('lowongan_id');
            } else {
                $query->where('lowongan_id', $selectedLowongan);
            }
        }

        if ($selectedKampus !== 'all') {
            $query->where('asal_kampus', $selectedKampus);
        }

        if ($selectedKonfirmasi === 'confirmed') {
            $query->whereNotNull('konfirmasi_at');
        } elseif ($selectedKonfirmasi === 'unconfirmed') {
            $query->where('status', 'approved')->whereNull('konfirmasi_at');
        }

        if ($selectedYear !== 'all') {
            $query->whereYear('created_at', $selectedYear);
        }

        if ($selectedMonth !== 'all') {
            $query->whereMonth('created_at', $selectedMonth);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_pendaftar', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('asal_kampus', 'like', '%' . $search . '%')
                    ->orWhere('prodi', 'like', '%' . $search . '%');
            });
        }

        if ($sort === 'oldest') {
            $query->oldest('created_at');
        } elseif ($sort === 'name_asc') {
            $query->orderBy('nama_pendaftar', 'asc');
        } elseif ($sort === 'name_desc') {
            $query->orderBy('nama_pendaftar', 'desc');
        } else {
            $query->latest('created_at');
        }

        $pendaftarans = $query->paginate(10)->withQueryString();

        // Data untuk Dropdown Filter
        $lowongans = Lowongan::select('id', 'judul_posisi', 'divisi')->orderBy('divisi')->get();
        
        $kampusList = (clone $baseQuery)
            ->select('asal_kampus')
            ->distinct()
            ->whereNotNull('asal_kampus')
            ->where('asal_kampus', '!=', '')
            ->orderBy('asal_kampus', 'asc')
            ->pluck('asal_kampus');

        $availableYears = (clone $baseQuery)
            ->select(DB::raw('YEAR(created_at) as year'))
            ->distinct()
            ->whereNotNull('created_at')
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('daftar.index', [
            'pendaftarans'       => $pendaftarans,
            'availableYears'     => $availableYears,
            'lowongans'          => $lowongans,
            'kampusList'         => $kampusList,
            'selectedStatus'     => $selectedStatus,
            'selectedLowongan'   => $selectedLowongan,
            'selectedKampus'     => $selectedKampus,
            'selectedKonfirmasi' => $selectedKonfirmasi,
            'selectedYear'       => $selectedYear,
            'selectedMonth'      => $selectedMonth,
            'search'             => $search,
            'sort'               => $sort,
            'countAll'           => $countAll,
            'countPending'       => $countPending,
            'countApproved'      => $countApproved,
            'countConditional'   => $countConditional,
            'countRejected'      => $countRejected,
        ]);
    }

    /**
     * Show the form for creating a new registration.
     */
    public function create(Request $request)
    {
        $lowongans = Lowongan::where('status', 'buka')->get();
        $selectedLowonganId = $request->query('lowongan_id');

        $setting = Setting::where('key', 'min_durasi_magang')->first();
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

        // Bind data dari user auth & sanitasi input
        $validated['email']          = auth()->user()->email;
        $validated['user_id']        = auth()->id();
        $validated['status']         = 'pending';
        $validated['nama_pendaftar'] = strip_tags($validated['nama_pendaftar']);
        $validated['asal_kampus']    = strip_tags($validated['asal_kampus']);
        $validated['prodi']          = strip_tags($validated['prodi']);

        // Upload berkas fisik
        $validated['surat_permohonan'] = $request->file('surat_permohonan')->store('surat_permohonan', 'public');
        $validated['surat_kampus']     = $request->file('surat_kampus')->store('surat_kampus', 'public');
        $validated['pas_foto']         = $request->file('pas_foto')->store('pas_foto', 'public');

        Pendaftaran::create($validated);

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

        $lowongans = Lowongan::where('status', 'buka')->get();

        $setting = Setting::where('key', 'min_durasi_magang')->first();
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

        // Upload berkas baru dan bersihkan berkas lama
        if ($request->hasFile('surat_permohonan')) {
            if ($pendaftaran->surat_permohonan && Storage::disk('public')->exists($pendaftaran->surat_permohonan)) {
                Storage::disk('public')->delete($pendaftaran->surat_permohonan);
            }
            $validated['surat_permohonan'] = $request->file('surat_permohonan')->store('surat_permohonan', 'public');
        }

        if ($request->hasFile('surat_kampus')) {
            if ($pendaftaran->surat_kampus && Storage::disk('public')->exists($pendaftaran->surat_kampus)) {
                Storage::disk('public')->delete($pendaftaran->surat_kampus);
            }
            $validated['surat_kampus'] = $request->file('surat_kampus')->store('surat_kampus', 'public');
        }

        if ($request->hasFile('pas_foto')) {
            if ($pendaftaran->pas_foto && Storage::disk('public')->exists($pendaftaran->pas_foto)) {
                Storage::disk('public')->delete($pendaftaran->pas_foto);
            }
            $validated['pas_foto'] = $request->file('pas_foto')->store('pas_foto', 'public');
        }

        // Reset status ke pending saat pemohon mengedit kembali berkas
        $validated['status']        = 'pending';
        $validated['remarks']       = null;
        $validated['konfirmasi_at'] = null;

        $pendaftaran->update($validated);

        return redirect()->route('daftar.index')->with('success', 'Pendaftaran Anda berhasil diperbarui dan telah dikirim ulang untuk direview.');
    }

    /**
     * Remove the specified registration from storage.
     */
    public function destroy(Pendaftaran $pendaftaran)
    {
        $isOwner = $pendaftaran->user_id === auth()->id();
        $isAdmin = auth()->user()->isAdmin();

        if (!$isAdmin) {
            if (!$isOwner || $pendaftaran->status !== 'pending') {
                abort(403, 'Data tidak dapat dihapus. Hanya Admin atau data Pending yang bisa dihapus.');
            }
        }

        // Bersihkan berkas fisik
        $files = ['surat_permohonan', 'surat_kampus', 'pas_foto'];
        foreach ($files as $file) {
            if ($pendaftaran->$file && Storage::disk('public')->exists($pendaftaran->$file)) {
                Storage::disk('public')->delete($pendaftaran->$file);
            }
        }

        $pendaftaran->delete();

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

        DB::beginTransaction();

        try {
            if ($status === 'approved') {
                if ($pendaftaran->status === 'approved') {
                    DB::rollBack();
                    return redirect()->route('daftar.index')->with('success', 'Pendaftar ini sudah disetujui sebelumnya.');
                }

                // Copy Foto ke folder foto_magang
                $sourcePath = $pendaftaran->pas_foto;
                $fileName   = Str::afterLast($sourcePath, '/');
                $newPath    = 'foto_magang/' . $fileName;

                if (Storage::disk('public')->exists($sourcePath)) {
                    Storage::disk('public')->copy($sourcePath, $newPath);
                } else {
                    DB::rollBack();
                    return back()->withErrors(['foto' => 'File foto pendaftar tidak ditemukan. Approval dibatalkan.']);
                }

                // Hitung durasi
                $tanggal_mulai   = Carbon::parse($pendaftaran->tanggal_mulai);
                $tanggal_selesai = Carbon::parse($pendaftaran->tanggal_selesai);

                $periode_bulan = (int) round($tanggal_mulai->diffInDays($tanggal_selesai) / 30);
                if ($periode_bulan === 0) {
                    $periode_bulan = 1;
                }

                // Buat data Magang baru (termasuk lowongan_id jika ada)
                Magang::create([
                    'user_id'         => $pendaftaran->user_id,
                    'lowongan_id'     => $pendaftaran->lowongan_id,
                    'nama'            => $pendaftaran->nama_pendaftar,
                    'email'           => $pendaftaran->email,
                    'asal_kampus'     => $pendaftaran->asal_kampus,
                    'prodi'           => $pendaftaran->prodi,
                    'tanggal_mulai'   => $pendaftaran->tanggal_mulai,
                    'tanggal_selesai' => $pendaftaran->tanggal_selesai,
                    'foto'            => $newPath,
                    'status'          => 'belum_aktif',
                    'periode_bulan'   => $periode_bulan,
                ]);

                $pendaftaran->status  = 'approved';
                $pendaftaran->remarks = $remarks;
                $pendaftaran->save();

                Mail::to($pendaftaran->email)->send(new PendaftaranStatusMail($pendaftaran));

                DB::commit();
                return redirect()->route('daftar.index')->with('success', 'Pendaftaran disetujui! Data telah dipindahkan & notifikasi email terkirim.');
            } else {
                // Status Rejected / Conditional
                $pendaftaran->status  = $status;
                $pendaftaran->remarks = $remarks;
                $pendaftaran->save();

                Mail::to($pendaftaran->email)->send(new PendaftaranStatusMail($pendaftaran));

                DB::commit();
                $message = $status === 'rejected' ? 'Pendaftaran ditolak.' : 'Pendaftaran disetujui dengan syarat.';
                return redirect()->route('daftar.index')->with('success', $message . ' Notifikasi email terkirim.');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Terjadi kesalahan sistem: ' . $e->getMessage()]);
        }
    }

    /**
     * Download attached document safely.
     */
    public function downloadFile(Pendaftaran $pendaftaran, $field)
    {
        $this->authorizeOwnerOrAdmin($pendaftaran);

        $allowedFields = ['surat_permohonan', 'surat_kampus', 'pas_foto'];

        if (!in_array($field, $allowedFields)) {
            abort(404, 'Jenis file tidak valid.');
        }

        $filePath = $pendaftaran->{$field};

        if (!$filePath || !Storage::disk('public')->exists($filePath)) {
            abort(404, 'File tidak ditemukan.');
        }

        return Storage::disk('public')->download($filePath);
    }

    /**
     * Public endpoint to confirm attendance from email link.
     */
    public function konfirmasiKehadiran(Pendaftaran $pendaftaran)
    {
        $isError = false;

        if ($pendaftaran->status !== 'approved') {
            $message = 'Konfirmasi gagal. Status pendaftaran Anda belum disetujui.';
            $isError = true;
        } elseif ($pendaftaran->konfirmasi_at) {
            $message = 'Anda sudah melakukan konfirmasi pada tanggal ' . Carbon::parse($pendaftaran->konfirmasi_at)->format('d F Y, H:i') . ' WIB.';
        } else {
            $pendaftaran->konfirmasi_at = now();
            $pendaftaran->save();
            $message = 'Terima kasih! Kehadiran Anda telah berhasil dikonfirmasi.';
        }

        return view('daftar.konfirmasi-sukses', [
            'pendaftaran' => $pendaftaran,
            'message'     => $message,
            'isError'     => $isError,
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
