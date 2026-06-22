<nav x-data="{ open: false }" class="border-b border-slate-200 bg-white/90 backdrop-blur">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-18 items-center justify-between gap-4">
            <div class="flex items-center gap-8">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-3">
                    <span class="grid size-10 place-items-center rounded-2xl bg-slate-950 text-sm font-bold text-white">IV</span>
                    <span class="hidden sm:block">
                        <span class="block text-xs font-semibold uppercase tracking-[0.24em] text-amber-700">Invitaciones</span>
                        <span class="block text-sm text-slate-500">Eventos y asistentes</span>
                    </span>
                </a>

                <div class="hidden items-center gap-2 sm:flex">
                    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'bg-slate-950 text-white' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950' }} rounded-full px-4 py-2 text-sm font-semibold transition">
                        Inicio
                    </a>
                    <a href="{{ route('events.index') }}" class="{{ request()->routeIs('events.*') ? 'bg-slate-950 text-white' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950' }} rounded-full px-4 py-2 text-sm font-semibold transition">
                        Eventos
                    </a>
                    @if (Auth::user()->isAdmin())
                        <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'bg-slate-950 text-white' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950' }} rounded-full px-4 py-2 text-sm font-semibold transition">
                            Usuarios
                        </a>
                    @endif
                    @if (Auth::user()->isValidator())
                        <a href="{{ route('validator.dashboard') }}" class="{{ request()->routeIs('validator.*') ? 'bg-slate-950 text-white' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950' }} rounded-full px-4 py-2 text-sm font-semibold transition">
                            Validador
                        </a>
                    @endif
                </div>
            </div>

            <div class="hidden items-center gap-3 sm:flex">
                <a href="{{ route('events.create') }}" class="inline-flex rounded-full bg-amber-400 px-4 py-2 text-sm font-semibold text-slate-950 transition hover:bg-amber-300">
                    Nuevo evento
                </a>

                <x-dropdown align="right" width="64">
                    <x-slot name="trigger">
                        <button type="button" class="inline-flex items-center gap-3 rounded-full border border-slate-200 bg-white px-3 py-2 text-left shadow-sm transition hover:border-slate-300">
                            @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                <img class="size-9 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                            @else
                                <span class="grid size-9 place-items-center rounded-full bg-slate-950 text-xs font-bold text-white">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </span>
                            @endif
                            <span>
                                <span class="block text-sm font-semibold text-slate-950">{{ Auth::user()->name }}</span>
                                <span class="block text-xs text-slate-500">{{ '@'.Auth::user()->username }}</span>
                            </span>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="px-4 py-3">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Cuenta</p>
                            <p class="mt-2 text-sm font-semibold text-slate-950">{{ Auth::user()->email }}</p>
                        </div>

                        <div class="border-t border-slate-200"></div>

                        <x-dropdown-link href="{{ route('profile.show') }}">
                            Perfil
                        </x-dropdown-link>

                        @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                            <x-dropdown-link href="{{ route('api-tokens.index') }}">
                                API Tokens
                            </x-dropdown-link>
                        @endif

                        <div class="border-t border-slate-200"></div>

                        <form method="POST" action="{{ route('logout') }}" x-data>
                            @csrf

                            <x-dropdown-link href="{{ route('logout') }}" @click.prevent="$root.submit();">
                                Cerrar sesion
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <button @click="open = ! open" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 p-2 text-slate-600 transition hover:bg-slate-100 sm:hidden">
                <svg class="size-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden border-t border-slate-200 bg-white sm:hidden">
        <div class="space-y-3 px-4 py-4">
            <a href="{{ route('dashboard') }}" class="block rounded-2xl px-4 py-3 text-sm font-semibold {{ request()->routeIs('dashboard') ? 'bg-slate-950 text-white' : 'bg-slate-100 text-slate-700' }}">
                Inicio
            </a>
            <a href="{{ route('events.index') }}" class="block rounded-2xl px-4 py-3 text-sm font-semibold {{ request()->routeIs('events.*') ? 'bg-slate-950 text-white' : 'bg-slate-100 text-slate-700' }}">
                Eventos
            </a>
            @if (Auth::user()->isAdmin())
                <a href="{{ route('users.index') }}" class="block rounded-2xl px-4 py-3 text-sm font-semibold {{ request()->routeIs('users.*') ? 'bg-slate-950 text-white' : 'bg-slate-100 text-slate-700' }}">
                    Usuarios
                </a>
            @endif
            @if (Auth::user()->isValidator())
                <a href="{{ route('validator.dashboard') }}" class="block rounded-2xl px-4 py-3 text-sm font-semibold {{ request()->routeIs('validator.*') ? 'bg-slate-950 text-white' : 'bg-slate-100 text-slate-700' }}">
                    Validador
                </a>
            @endif
            <a href="{{ route('events.create') }}" class="block rounded-2xl bg-amber-400 px-4 py-3 text-sm font-semibold text-slate-950">
                Nuevo evento
            </a>
        </div>

        <div class="border-t border-slate-200 px-4 py-4">
            <p class="text-sm font-semibold text-slate-950">{{ Auth::user()->name }}</p>
            <p class="mt-1 text-sm text-slate-500">{{ Auth::user()->email }}</p>
            <p class="mt-1 text-xs text-slate-400">{{ '@'.Auth::user()->username }}</p>

            <div class="mt-4 space-y-2">
                <a href="{{ route('profile.show') }}" class="block rounded-2xl bg-slate-100 px-4 py-3 text-sm font-semibold text-slate-700">
                    Perfil
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="block w-full rounded-2xl bg-slate-950 px-4 py-3 text-left text-sm font-semibold text-white">
                        Cerrar sesion
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
