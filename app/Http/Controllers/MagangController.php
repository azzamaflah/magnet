<?php

namespace App\Http\Controllers;

use App\Models\Magang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\Pendaftaran;
use Carbon\Carbon;

class MagangController extends Controller
{
    /**
     * Helper: Aturan Validasi Terpusat (DRY)
     */
    private function getValidationRules($isUpdate = false)
    {
        $rules = [
            // Nama: hanya huruf & spasi
            'nama' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/'],

            // Kampus: hanya huruf & spasi
            'asal_kampus' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/'],

            // Prodi: bebas string (karena bisa ada singkatan/titik)
            'prodi' => 'required|string|max:255',

            // Tanggal Mulai (ADMIN):
            // boleh mundur sampai 1 tahun ke belakang, maksimal 7 bulan ke depan
            'tanggal_mulai' => [
                'required',
                'date',
                'after_or_equal:' . Carbon::today()->subYear()->toDateString(),
                'before_or_equal:' . Carbon::today()->addMonths(7)->toDateString(),
            ],
            
            // Tanggal Selesai:
            // setelah tanggal mulai, durasi min 1 bulan max 7 bulan
            'tanggal_selesai' => [
                'required',
                'date',
                'after_or_equal:tanggal_mulai',
                function ($attribute, $value, $fail) {
                    $mulaiInput = request('tanggal_mulai');
                    if (!$mulaiInput) return;
            
                    $mulai   = Carbon::parse($mulaiInput);
                    $selesai = Carbon::parse($value);
            
                    $durasiBulan = $mulai->diffInMonths($selesai);
            
                    if ($durasiBulan < 1) {
                        $fail('Durasi magang minimal 1 bulan.');
                    } elseif ($durasiBulan > 7) {
                        $fail('Durasi magang maksimal 7 bulan.');
                    }
                },
            ],


            // Link pekerjaan opsional, tapi kalau diisi harus URL
            'link_pekerjaan' => 'nullable|url|max:500',

            // Kontak & sosial media (opsional)
            // Whatsapp: Wajib diawali +62, diikuti 8-15 digit angka.
            // Total panjang string minimal sekitar 11-12 karakter, maksimal 18.
            'whatsapp' => ['nullable', 'string', 'max:20', 'regex:/^\+62[0-9]{8,15}$/'],

            // IG/Tiktok: username (pakai @), 1-30 char, alfanumerik . _
            'instagram' => ['nullable', 'string', 'max:100', 'regex:/^@[a-zA-Z0-9._]{1,30}$/'],
            'tiktok'    => ['nullable', 'string', 'max:100', 'regex:/^@[a-zA-Z0-9._]{1,30}$/'],

            // Text area
            'kesan' => 'nullable|string|max:2000',
            'pesan' => 'nullable|string|max:2000',
        ];

        // File foto: wajib saat create, nullable saat update
        if ($isUpdate) {
            $rules['foto'] = 'nullable|image|mimes:jpeg,png,jpg|max:2048';
        } else {
            $rules['foto'] = 'required|image|mimes:jpeg,png,jpg|max:2048';
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
            'string'   => ':attribute harus berupa teks.',
            'max'      => ':attribute maksimal :max karakter.',
            'date'     => 'Format :attribute tidak valid.',

            // Regex khusus
            'nama.regex'        => 'Nama hanya boleh berisi huruf dan spasi (tidak boleh angka atau simbol).',
            'asal_kampus.regex' => 'Nama kampus hanya boleh berisi huruf dan spasi (tidak boleh angka atau simbol).',

            // Tanggal khusus
            'tanggal_mulai.after_or_equal'  => 'Tanggal mulai maksimal boleh 1 tahun ke belakang.',
            'tanggal_mulai.before_or_equal' => 'Tanggal mulai maksimal 7 bulan ke depan.',
            'tanggal_selesai.after_or_equal'=> 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.',


            // Foto
            'foto.image' => 'File foto harus berupa gambar.',
            'foto.mimes' => 'Foto harus berformat JPEG, PNG, atau JPG.',
            'foto.max'   => 'Ukuran foto maksimal 2MB.',

            // URL & kontak
            'link_pekerjaan.url' => 'Link pekerjaan harus berupa URL yang valid.',
            'whatsapp.regex' => 'Nomor WhatsApp harus diawali kode negara +62 (bukan 0). Contoh: +6281234567890.',
            'instagram.regex' => 'Username Instagram harus diawali @, tanpa spasi (contoh: @bpsbantul).',
            'tiktok.regex'    => 'Username TikTok harus diawali @, tanpa spasi (contoh: @bpsbantul).',

        ];
    }

    /**
     * (Opsional tapi bagus) Nama atribut biar pesan "nama_pendaftar" jadi "Nama", dll.
     */
    private function getValidationAttributes()
    {
        return [
            'nama' => 'Nama',
            'foto' => 'Foto',
            'asal_kampus' => 'Asal kampus',
            'prodi' => 'Prodi',
            'tanggal_mulai' => 'Tanggal mulai',
            'tanggal_selesai' => 'Tanggal selesai',
            'link_pekerjaan' => 'Link pekerjaan',
            'whatsapp' => 'WhatsApp',
            'instagram' => 'Instagram',
            'tiktok' => 'TikTok',
            'kesan' => 'Kesan',
            'pesan' => 'Pesan',
        ];
    }

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

    public function create()
    {
        return view('magang.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate(
            $this->getValidationRules(false),
            $this->getValidationMessages(),
            $this->getValidationAttributes()
        );

        // Sanitasi (best practice)
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

    public function show(Magang $magang)
    {
        return view('magang.show', compact('magang'));
    }

    public function edit(Magang $magang)
    {
        if (!auth()->user()->isAdmin() && $magang->user_id != auth()->id()) {
            abort(403, 'AKSES DITOLAK. Anda hanya dapat mengedit data Anda sendiri.');
        }

        return view('magang.edit', compact('magang'));
    }

    public function update(Request $request, Magang $magang)
    {
        if (!auth()->user()->isAdmin() && $magang->user_id != auth()->id()) {
            abort(403, 'AKSES DITOLAK.');
        }

        $validated = $request->validate(
            $this->getValidationRules(true),
            $this->getValidationMessages(),
            $this->getValidationAttributes()
        );

        // Sanitasi
        $validated['nama']        = strip_tags($validated['nama']);
        $validated['asal_kampus'] = strip_tags($validated['asal_kampus']);
        $validated['prodi']       = strip_tags($validated['prodi']);

        if ($request->hasFile('foto')) {
            $oldMagangPhoto = $magang->foto;

            $newPhotoPath = $request->file('foto')->store('foto_magang', 'public');
            $validated['foto'] = $newPhotoPath;

            $pendaftaran = \App\Models\Pendaftaran::where('user_id', $magang->user_id)->first();

            if ($pendaftaran) {
                $oldPendaftaranPhoto = $pendaftaran->pas_foto;

                $pendaftaran->update([
                    'pas_foto' => $newPhotoPath
                ]);

                if ($oldPendaftaranPhoto && $oldPendaftaranPhoto !== $oldMagangPhoto && Storage::disk('public')->exists($oldPendaftaranPhoto)) {
                    Storage::disk('public')->delete($oldPendaftaranPhoto);
                }
            }

            if ($oldMagangPhoto && Storage::disk('public')->exists($oldMagangPhoto)) {
                Storage::disk('public')->delete($oldMagangPhoto);
            }
        }

        $magang->update($validated);

        return redirect()->route('magang.show', $magang)->with('success', 'Data dan Foto berhasil diperbarui di semua data!');
    }

    public function destroy(Magang $magang)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        if ($magang->foto && Storage::disk('public')->exists($magang->foto)) {
            Storage::disk('public')->delete($magang->foto);
        }

        $pendaftaran = Pendaftaran::where('user_id', $magang->user_id)->first();

        if ($pendaftaran) {
            $pendaftaran->update([
                'status' => 'rejected',
                'remarks' => 'Data dihapus dari daftar peserta Magang aktif oleh Admin.'
            ]);
        }

        $magang->delete();

        return redirect()->route('magang.index')->with('success', 'Peserta dihapus dari daftar aktif. Data pendaftaran telah diarsipkan (Ditolak).');
    }
}
