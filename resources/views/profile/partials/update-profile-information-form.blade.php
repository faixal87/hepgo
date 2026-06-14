<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            Maklumat Profil
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            Kemaskini maklumat akaun, gambar profil dan tema paparan workspace anda.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div class="rounded-2xl border border-dashed border-orange-200 bg-orange-50/70 p-4">
            <p class="mb-4 text-sm font-extrabold text-orange-900">Letak gambar profil di sini</p>
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
            @if ($user->profilePhotoUrl())
                <img src="{{ $user->profilePhotoUrl() }}" alt="Gambar profil {{ $user->name }}" class="h-20 w-20 rounded-full object-cover ring-4 ring-orange-100">
            @else
                <div class="grid h-20 w-20 place-items-center rounded-full bg-blue-900 text-2xl font-black text-white ring-4 ring-orange-100">
                    {{ \Illuminate\Support\Str::of($user->name)->substr(0, 1)->upper() }}
                </div>
            @endif

            <div class="flex-1">
                <x-input-label for="profile_photo" value="Gambar Profil" />
                <input id="profile_photo" name="profile_photo" type="file" accept="image/png,image/jpeg,image/webp" class="mt-1 block w-full rounded-xl border border-orange-200 bg-white text-sm text-gray-700 file:me-4 file:border-0 file:bg-orange-600 file:px-4 file:py-2.5 file:text-sm file:font-bold file:text-white hover:file:bg-orange-700">
                <p class="mt-1 text-xs text-gray-500">Format dibenarkan: JPG, PNG atau WEBP. Saiz maksimum 2MB.</p>
                <x-input-error class="mt-2" :messages="$errors->get('profile_photo')" />
            </div>
            </div>
        </div>

        <div>
            <x-input-label for="name" value="Nama" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" value="Emel" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        Emel anda belum disahkan.

                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Klik di sini untuk hantar semula emel pengesahan.
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            Pautan pengesahan baharu telah dihantar ke emel anda.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div>
            <x-input-label for="phone" value="No. Telefon" />
            <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone', $user->phone)" autocomplete="tel" />
            <x-input-error class="mt-2" :messages="$errors->get('phone')" />
        </div>

        <div>
            <x-input-label for="ui_theme" value="Tema Workspace" />
            <select id="ui_theme" name="ui_theme" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                @foreach (config('hep.ui_themes') as $key => $theme)
                    <option value="{{ $key }}" @selected(old('ui_theme', $user->uiThemeKey()) === $key)>
                        {{ $theme['label'] }}
                    </option>
                @endforeach
            </select>
            <p class="mt-1 text-xs text-gray-500">Tema ini digunakan untuk workspace pengguna dan panel admin anda.</p>
            <x-input-error class="mt-2" :messages="$errors->get('ui_theme')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>Simpan</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >Maklumat berjaya disimpan.</p>
            @endif
        </div>
    </form>
</section>
