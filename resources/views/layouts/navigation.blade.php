<nav x-data="{ open: false }" class="hep-topbar">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">
            <div class="flex items-center gap-3 lg:hidden">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                    <x-application-logo class="h-10 w-auto object-contain" />
                    <span class="text-sm font-black text-blue-950">Portal HEP</span>
                </a>
            </div>

            <div class="hidden items-center gap-2 text-sm font-bold text-slate-600 lg:flex">
                <span class="rounded-full px-3 py-1" style="background: var(--hep-accent-soft); color: var(--hep-accent);">
                    Workspace Pengguna
                </span>
                <span>{{ Auth::user()->uiTheme()['label'] }}</span>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6 sm:gap-3">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-bold text-slate-700 shadow-sm transition hover:border-orange-200 hover:text-orange-600">
                    <x-lucide-house class="h-4 w-4" />
                    <span>Ke Portal</span>
                </a>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center rounded-full border border-slate-200 bg-white px-3 py-2 text-sm font-bold leading-4 text-slate-600 shadow-sm transition hover:text-slate-900 focus:outline-none">
                            @if (Auth::user()->profilePhotoUrl())
                                <img src="{{ Auth::user()->profilePhotoUrl() }}" alt="Gambar profil {{ Auth::user()->name }}" class="me-2 h-8 w-8 rounded-full object-cover">
                            @else
                                <span class="me-2 grid h-8 w-8 place-items-center rounded-full text-xs font-black text-white" style="background: var(--hep-sidebar);">
                                    {{ \Illuminate\Support\Str::of(Auth::user()->name)->substr(0, 1)->upper() }}
                                </span>
                            @endif

                            <span>{{ Auth::user()->name }}</span>

                            <svg class="ms-2 h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            Profil & Tema
                        </x-dropdown-link>

                        @if (Auth::user()->hasAnyRole(config('hep.admin_panel_roles')))
                            <x-dropdown-link href="/admin">
                                Panel Admin
                            </x-dropdown-link>
                        @endif

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                Log Keluar
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center rounded-md p-2 text-slate-500 transition hover:bg-slate-100 hover:text-slate-700 focus:outline-none">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden border-t border-slate-200 bg-white sm:hidden">
        <div class="space-y-1 pb-3 pt-2">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                Papan Pemuka
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.edit')">
                Profil & Tema
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('home')" :active="request()->routeIs('home')">
                Ke Portal
            </x-responsive-nav-link>
            @if (Auth::user()->hasAnyRole(config('hep.admin_panel_roles')))
                <x-responsive-nav-link href="/admin">
                    Panel Admin
                </x-responsive-nav-link>
            @endif
        </div>

        <div class="border-t border-slate-200 pb-1 pt-4">
            <div class="px-4">
                <div class="flex items-center gap-3">
                    @if (Auth::user()->profilePhotoUrl())
                        <img src="{{ Auth::user()->profilePhotoUrl() }}" alt="Gambar profil {{ Auth::user()->name }}" class="h-10 w-10 rounded-full object-cover">
                    @else
                        <span class="grid h-10 w-10 place-items-center rounded-full text-sm font-black text-white" style="background: var(--hep-sidebar);">
                            {{ \Illuminate\Support\Str::of(Auth::user()->name)->substr(0, 1)->upper() }}
                        </span>
                    @endif
                    <div>
                        <div class="text-base font-bold text-slate-800">{{ Auth::user()->name }}</div>
                        <div class="text-sm font-medium text-slate-500">{{ Auth::user()->email }}</div>
                    </div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        Log Keluar
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
