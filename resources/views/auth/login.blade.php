<x-guest-layout>
    <div class="grid gap-10 lg:grid-cols-[1.1fr_0.9fr] lg:items-center">
        <section class="space-y-6">
            <span class="inline-flex rounded-full border border-amber-300 bg-amber-100 px-4 py-1 text-xs font-semibold uppercase tracking-[0.24em] text-amber-800">
                Plataforma de invitaciones
            </span>
            <h1 class="max-w-xl text-4xl font-semibold tracking-tight text-slate-950 sm:text-5xl">
                Ingresa con tu correo o nombre de usuario y administra tus eventos.
            </h1>
            <p class="max-w-lg text-base leading-7 text-slate-600">
                Crea eventos, carga invitados desde Excel y organiza el envio de invitaciones desde un panel simple.
            </p>
            <div class="grid gap-4 sm:grid-cols-3">
                <div class="rounded-3xl border border-slate-200 bg-white px-5 py-4 shadow-sm">
                    <p class="text-sm font-semibold text-slate-950">Eventos</p>
                    <p class="mt-1 text-sm text-slate-500">Configura nombre, fecha, hora y lugar.</p>
                </div>
                <div class="rounded-3xl border border-slate-200 bg-white px-5 py-4 shadow-sm">
                    <p class="text-sm font-semibold text-slate-950">Invitados</p>
                    <p class="mt-1 text-sm text-slate-500">Registro manual o importacion masiva.</p>
                </div>
                <div class="rounded-3xl border border-slate-200 bg-white px-5 py-4 shadow-sm">
                    <p class="text-sm font-semibold text-slate-950">Control</p>
                    <p class="mt-1 text-sm text-slate-500">Visualiza totales y actividad por evento.</p>
                </div>
            </div>
        </section>

        <section class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-[0_24px_60px_-30px_rgba(15,23,42,0.35)]">
            <div class="mb-8">
                <h2 class="text-2xl font-semibold text-slate-950">Iniciar sesion</h2>
                <p class="mt-2 text-sm text-slate-500">Usa tu correo electronico o tu nombre de usuario.</p>
            </div>

            <x-validation-errors class="mb-4" />

            @session('status')
                <div class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    {{ $value }}
                </div>
            @endsession

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700">Correo o usuario</label>
                    <input id="email" name="email" type="text" value="{{ old('email') }}" required autofocus autocomplete="username" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-100">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700">Contrasena</label>
                    <input id="password" name="password" type="password" required autocomplete="current-password" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-100">
                </div>

                <label for="remember_me" class="flex items-center gap-3 text-sm text-slate-600">
                    <input id="remember_me" name="remember" type="checkbox" class="size-4 rounded border-slate-300 text-amber-600 focus:ring-amber-500">
                    Mantener sesion iniciada
                </label>

                <button type="submit" class="inline-flex w-full justify-center rounded-full bg-slate-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                    Entrar al panel
                </button>

                <div class="flex items-center justify-between gap-4 text-sm">
                    @if (Route::has('password.request'))
                        <a class="text-slate-500 transition hover:text-slate-950" href="{{ route('password.request') }}">
                            Olvide mi contrasena
                        </a>
                    @endif

                    <a class="font-semibold text-amber-700 transition hover:text-amber-800" href="{{ route('register') }}">
                        Crear cuenta
                    </a>
                </div>
            </form>
        </section>
    </div>
</x-guest-layout>
