<?php

namespace App\Http\Controllers;

use App\Models\Pendaftaran;
use App\Models\Magang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\PendaftaranStatusMail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Setting;
// modifikasi start
use Illuminate\Support\Facades\Http;
//modifikasi end
class PendaftaranController extends Controller
{
    /**
     * Helper: Aturan Validasi Terpusat (Best Practice: DRY)
     */
    private function getValidationRules($isUpdate = false)
    {
        $setting = Setting::where('key', 'min_durasi_magang')->first();
        $minVal = $setting->value ?? 3;
        $tipe = $setting->type ?? 'bulan';

        $rules = [
            // 1. Regex hanya huruf dan spasi (No angka/simbol)
            'nama_pendaftar' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/'],
            'asal_kampus'    => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/'],

            // Prodi tetap string biasa (karena mungkin ada singkatan/titik)
            'prodi'          => 'required|string|max:255',


            // 2. Tanggal Mulai: hari ini s.d. 7 bulan ke depan
            'tanggal_mulai' => [
                'required',
                'date',
                'after_or_equal:today',
                'before_or_equal:' . Carbon::today()->addMonths(7)->toDateString(),
            ],

            // 3. Tanggal Selesai: setelah mulai & max 7 bulan dari hari ini
            'tanggal_selesai' => [
                'required',
                'date',
                'after_or_equal:tanggal_mulai',
                function ($attribute, $value, $fail) use ($minVal, $tipe) {
                    $mulai = Carbon::parse(request('tanggal_mulai'));
                    $selesai = Carbon::parse($value);

                    $diff = ($tipe == 'bulan') ? $mulai->diffInMonths($selesai) : $mulai->diffInDays($selesai);

                    if ($diff < $minVal) {
                        $fail("Maaf, durasi magang minimal adalah $minVal $tipe.");
                    }
                },
            ],


            // NOTE: Email dihapus dari validasi request karena diambil dari Auth
        ];

        // Validasi File (Kondisional Update vs Create)
        if ($isUpdate) {
            $rules['surat_permohonan'] = 'nullable|file|mimes:pdf|max:2048';
            $rules['surat_kampus']     = 'nullable|file|mimes:pdf|max:2048';
            $rules['pas_foto']         = 'nullable|file|image|mimes:jpeg,png,jpg|max:1024';
        } else {
            $rules['surat_permohonan'] = 'required|file|mimes:pdf|max:2048';
            $rules['surat_kampus']     = 'required|file|mimes:pdf|max:2048';
            $rules['pas_foto']         = 'required|file|image|mimes:jpeg,png,jpg|max:1024';
        }

        return $rules;
    }

    /**
     * Helper: Pesan Error Bahasa Indonesia
     */
    private function getValidationMessages()
    {
        return [
            'required' => ':attribute wajib diisi.',
            'date' => 'Format tanggal tidak valid.',


            // Custom Message untuk Tanggal
            'tanggal_mulai.after_or_equal'   => 'Tanggal mulai tidak boleh tanggal lampau (harus hari ini atau masa depan).',
            'tanggal_mulai.before_or_equal'  => 'Tanggal mulai maksimal 7 bulan dari hari ini.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.',



            // Custom Message untuk Regex Nama/Kampus
            'nama_pendaftar.regex' => 'Nama hanya boleh berisi huruf dan spasi (tidak boleh angka atau simbol).',
            'asal_kampus.regex'    => 'Nama kampus hanya boleh berisi huruf dan spasi (tidak boleh angka atau simbol).',

            // Validasi File
            'surat_permohonan.max' => 'Ukuran surat permohonan maksimal 2MB.',
            'surat_permohonan.mimes' => 'Surat permohonan harus format PDF.',
            'surat_kampus.max' => 'Ukuran surat kampus maksimal 2MB.',
            'surat_kampus.mimes' => 'Surat kampus harus format PDF.',
            'pas_foto.max' => 'Ukuran pas foto maksimal 1MB.',
            'pas_foto.mimes' => 'Pas foto harus format JPEG, PNG, atau JPG.',
            'pas_foto.image' => 'File harus berupa gambar.',
        ];
    }

    public function index(Request $request)
    {
        $selectedYear = $request->input('year', 'all');
        $selectedMonth = $request->input('month', 'all');
        $search = $request->input('search');
        $sort = $request->input('sort', 'latest');
        $user = auth()->user();

        if ($user->isAdmin()) {
            $query = Pendaftaran::query();
        } else {
            $query = Pendaftaran::where('user_id', $user->id);
        }

        $availableYears = (clone $query)
            ->select(DB::raw('YEAR(created_at) as year'))
            ->distinct()
            ->whereNotNull('created_at')
            ->orderBy('year', 'desc')
            ->pluck('year');

        if ($selectedYear != 'all') {
            $query->whereYear('created_at', $selectedYear);
        }

        if ($selectedMonth != 'all') {
            $query->whereMonth('created_at', $selectedMonth);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_pendaftar', 'like', '%' . $search . '%')
                    ->orWhere('asal_kampus', 'like', '%' . $search . '%')
                    ->orWhere('prodi', 'like', '%' . $search . '%');
            });
        }

        if ($sort == 'oldest') {
            $query->orderBy('created_at', 'ASC');
        } else {
            $query->latest();
        }

        // modifikasi start
        // $pendaftarans = $query->get();
        $pendaftarans = $query->paginate(10)->withQueryString();
        // modifikasi end

        return view('daftar.index', [
            'pendaftarans' => $pendaftarans,
            'availableYears' => $availableYears,
            'selectedYear' => $selectedYear,
            'selectedMonth' => $selectedMonth,
            'search' => $search,
            'sort' => $sort,
        ]);
    }

    public function create()
    {
        return view('daftar.create');
    }

    public function store(Request $request)
    {
        // // 0. Validasi Captcha dulu
        // $request->validate([
        //     'g-recaptcha-response' => 'required',
        // ], [
        //     'g-recaptcha-response.required' => 'Captcha wajib dicentang.',
        // ]);

        // $verify = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
        //     'secret'   => env('RECAPTCHA_SECRET_KEY'),
        //     'response' => $request->input('g-recaptcha-response'),
        //     'remoteip' => $request->ip(),
        // ]);

        // if (! $verify->json('success')) {
        //     return back()
        //         ->withInput()
        //         ->withErrors(['g-recaptcha-response' => 'Captcha tidak valid. Silakan coba lagi.']);
        // }

        // 1. Validasi Input (Memanggil fungsi helper di atas)
        $validated = $request->validate($this->getValidationRules(false), $this->getValidationMessages());

        // 2. Best Practice: Force Email dari Auth & Sanitasi String
        $validated['email'] = auth()->user()->email;
        $validated['nama_pendaftar'] = strip_tags($validated['nama_pendaftar']);
        $validated['asal_kampus']    = strip_tags($validated['asal_kampus']);
        $validated['prodi']          = strip_tags($validated['prodi']);

        // 3. Upload File
        $validated['surat_permohonan'] = $request->file('surat_permohonan')->store('surat_permohonan', 'public');
        $validated['surat_kampus']     = $request->file('surat_kampus')->store('surat_kampus', 'public');
        $validated['pas_foto']         = $request->file('pas_foto')->store('pas_foto', 'public');

        $validated['user_id'] = auth()->id();
        $validated['status'] = 'pending';

        Pendaftaran::create($validated);

        return redirect()->route('daftar.index')->with('success', 'Pendaftaran berhasil dikirim! Silakan tunggu konfirmasi dari Admin.');

        // Ambil aturan dari database
        $rule = \App\Models\Setting::where('key', 'min_durasi_magang')->first();
        $minVal = $rule->value;
        $type = $rule->type;

        $start = \Carbon\Carbon::parse($request->tanggal_mulai);
        $end = \Carbon\Carbon::parse($request->tanggal_selesai);

        // Hitung selisih
        $diff = ($type == 'bulan') ? $start->diffInMonths($end) : $start->diffInDays($end);

        if ($diff < $minVal) {
            return back()->withErrors(['tanggal_selesai' => "Maaf, durasi magang minimal adalah $minVal $type."]);
        }
    }

    public function show(Pendaftaran $pendaftaran)
    {
        if (!auth()->user()->isAdmin() && $pendaftaran->user_id != auth()->id()) {
            abort(403, 'AKSES DITOLAK');
        }

        return view('daftar.show', compact('pendaftaran'));
    }

    public function edit(Pendaftaran $pendaftaran)
    {
        if ($pendaftaran->user_id != auth()->id()) {
            abort(403, 'AKSES DITOLAK. Anda hanya dapat mengedit pendaftaran Anda sendiri.');
        }

        if ($pendaftaran->status == 'approved') {
            return redirect()->route('daftar.index')->withErrors('Pendaftaran yang sudah disetujui tidak dapat diubah.');
        }

        return view('daftar.edit', compact('pendaftaran'));
    }

    public function update(Request $request, Pendaftaran $pendaftaran)
    {
        if ($pendaftaran->user_id != auth()->id()) {
            abort(403, 'AKSES DITOLAK.');
        }

        if ($pendaftaran->status == 'approved') {
            return redirect()->route('daftar.index')->withErrors('Pendaftaran yang sudah disetujui tidak dapat diubah.');
        }

        // 1. Validasi Update (Memanggil helper dengan parameter true)
        $validated = $request->validate($this->getValidationRules(true), $this->getValidationMessages());

        // 2. Best Practice: Force Email & Sanitasi
        $validated['email'] = auth()->user()->email;
        $validated['nama_pendaftar'] = strip_tags($validated['nama_pendaftar']);
        $validated['asal_kampus']    = strip_tags($validated['asal_kampus']);
        $validated['prodi']          = strip_tags($validated['prodi']);

        // 3. Logic Update File (Hapus file lama jika ada yang baru)
        if ($request->hasFile('surat_permohonan')) {
            if ($pendaftaran->surat_permohonan) Storage::disk('public')->delete($pendaftaran->surat_permohonan);
            $validated['surat_permohonan'] = $request->file('surat_permohonan')->store('surat_permohonan', 'public');
        }

        if ($request->hasFile('surat_kampus')) {
            if ($pendaftaran->surat_kampus) Storage::disk('public')->delete($pendaftaran->surat_kampus);
            $validated['surat_kampus'] = $request->file('surat_kampus')->store('surat_kampus', 'public');
        }

        if ($request->hasFile('pas_foto')) {
            if ($pendaftaran->pas_foto) Storage::disk('public')->delete($pendaftaran->pas_foto);
            $validated['pas_foto'] = $request->file('pas_foto')->store('pas_foto', 'public');
        }

        // Reset status ke pending saat update
        $validated['status'] = 'pending';
        $validated['remarks'] = null;
        $validated['konfirmasi_at'] = null;

        $pendaftaran->update($validated);

        return redirect()->route('daftar.index')->with('success', 'Pendaftaran Anda berhasil diperbarui dan telah dikirim ulang untuk direview.');
    }

    public function destroy(Pendaftaran $pendaftaran)
    {
        $isOwner = $pendaftaran->user_id == auth()->id();
        $isAdmin = auth()->user()->isAdmin();

        if (!$isAdmin) {
            if (!$isOwner || $pendaftaran->status != 'pending') {
                abort(403, 'Data tidak dapat dihapus. Hanya Admin atau data Pending yang bisa dihapus.');
            }
        }

        // Clean up files (Hapus file fisik)
        $files = ['surat_permohonan', 'surat_kampus', 'pas_foto'];
        foreach ($files as $file) {
            if ($pendaftaran->$file && Storage::disk('public')->exists($pendaftaran->$file)) {
                Storage::disk('public')->delete($pendaftaran->$file);
            }
        }

        $pendaftaran->delete();

        return redirect()->route('daftar.index')->with('success', 'Data pendaftaran dan berkas berhasil dihapus.');
    }

    public function updateStatus(Request $request, Pendaftaran $pendaftaran)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'HANYA ADMIN YANG DIIZINKAN.');
        }

        $request->validate([
            'status' => 'required|string|in:approved,rejected,conditional',
            'remarks' => 'nullable|string|max:1000',
        ]);

        $status = $request->input('status');
        $remarks = $request->input('remarks');

        if (in_array($status, ['rejected', 'conditional']) && empty($remarks)) {
            return back()->withInput()->withErrors(['remarks' => 'Catatan wajib diisi untuk status Ditolak atau Bersyarat.']);
        }

        // Best Practice: DB Transaction untuk integritas data
        DB::beginTransaction();

        try {
            if ($status == 'approved') {
                if ($pendaftaran->status == 'approved') {
                    return redirect()->route('daftar.index')->with('success', 'Pendaftar ini sudah disetujui sebelumnya.');
                }

                // Copy Foto ke folder magang
                $sourcePath = $pendaftaran->pas_foto;
                $fileName = Str::afterLast($sourcePath, '/');
                $newPath = 'foto_magang/' . $fileName;

                if (Storage::disk('public')->exists($sourcePath)) {
                    Storage::disk('public')->copy($sourcePath, $newPath);
                } else {
                    DB::rollBack();
                    return back()->withErrors(['foto' => 'File foto pendaftar tidak ditemukan. Approval dibatalkan.']);
                }

                // Hitung durasi
                $tanggal_mulai = new \Carbon\Carbon($pendaftaran->tanggal_mulai);
                $tanggal_selesai = new \Carbon\Carbon($pendaftaran->tanggal_selesai);

                $periode_bulan = (int) round($tanggal_mulai->diffInDays($tanggal_selesai) / 30);
                if ($periode_bulan == 0) $periode_bulan = 1;

                // Buat data Magang
                Magang::create([
                    'user_id' => $pendaftaran->user_id,
                    'nama' => $pendaftaran->nama_pendaftar,
                    'asal_kampus' => $pendaftaran->asal_kampus,
                    'prodi' => $pendaftaran->prodi,
                    'tanggal_mulai' => $pendaftaran->tanggal_mulai,
                    'tanggal_selesai' => $pendaftaran->tanggal_selesai,
                    'foto' => $newPath,
                    'status' => 'belum_aktif',
                    'periode_bulan' => $periode_bulan,
                ]);

                $pendaftaran->status = 'approved';
                $pendaftaran->remarks = $remarks;
                $pendaftaran->save();

                Mail::to($pendaftaran->email)->send(new PendaftaranStatusMail($pendaftaran));

                DB::commit();
                return redirect()->route('daftar.index')->with('success', 'Pendaftaran disetujui! Data telah dipindahkan & notifikasi email terkirim.');
            } else {
                // Logic Rejected / Conditional
                $pendaftaran->status = $status;
                $pendaftaran->remarks = $remarks;
                $pendaftaran->save();

                Mail::to($pendaftaran->email)->send(new PendaftaranStatusMail($pendaftaran));

                DB::commit();
                $message = $status == 'rejected' ? 'Pendaftaran ditolak.' : 'Pendaftaran disetujui dengan syarat.';
                return redirect()->route('daftar.index')->with('success', $message . ' Notifikasi email terkirim.');
            }
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback jika ada error
            return back()->withErrors(['error' => 'Terjadi kesalahan sistem: ' . $e->getMessage()]);
        }
    }

    public function downloadFile(Pendaftaran $pendaftaran, $field)
    {
        if (!auth()->user()->isAdmin() && $pendaftaran->user_id != auth()->id()) {
            abort(403, 'AKSES DITOLAK');
        }

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

    public function konfirmasiKehadiran(Pendaftaran $pendaftaran)
    {
        $message = '';
        $isError = false;

        if ($pendaftaran->status != 'approved') {
            $message = 'Konfirmasi gagal. Status pendaftaran Anda belum disetujui.';
            $isError = true;
        } elseif ($pendaftaran->konfirmasi_at) {
            $message = 'Anda sudah melakukan konfirmasi pada tanggal ' . Carbon::parse($pendaftaran->konfirmasi_at)->format('d F Y, H:i') . ' WIB.';
            $isError = false;
        } else {
            $pendaftaran->konfirmasi_at = now();
            $pendaftaran->save();
            $message = 'Terima kasih! Kehadiran Anda telah berhasil dikonfirmasi.';
            $isError = false;
        }

        return view('daftar.konfirmasi-sukses', [
            'pendaftaran' => $pendaftaran,
            'message' => $message,
            'isError' => $isError,
        ]);
    }
}
