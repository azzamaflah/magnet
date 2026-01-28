{{-- resources/views/profile/partials/update-profile-information-form.blade.php --}}
<section>
    <header>
        <h2 class="claude-title text-xl text-white">
            Informasi Profil
        </h2>

        <p class="mt-2 text-sm text-gray-400">
            Perbarui informasi profil akun Anda dan alamat email.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <label for="name" class="filter-label">Nama</label>
            <input type-="text" name="name" id="name" class="filter-input mt-1 block w-full" 
                   value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" />
            @error('name')
                <p class="text-red-400 text-xs mt-2">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="email" class="filter-label">Email</label>
            <input type="email" name="email" id="email" class="filter-input mt-1 block w-full" 
                   value="{{ old('email', $user->email) }}" required autocomplete="username" />
            @error('email')
                <p class="text-red-400 text-xs mt-2">{{ $message }}</p>
            @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class_="mt-2 text-sm text-gray-400">
                    <p>
                        Email Anda belum terverifikasi.

                        <button form="send-verification" 
                                class="underline text-sm text-gray-500 hover:text-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Klik di sini untuk mengirim ulang email verifikasi.
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-400">
                            Link verifikasi baru telah dikirim ke email Anda.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="filter-btn filter-btn-primary">Simpan</button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-green-400"
                >Tersimpan.</p>
            @endif
        </div>
    </form>
</section>