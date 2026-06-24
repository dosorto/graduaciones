<x-guest-layout>
    <div class="grid gap-6 lg:grid-cols-[0.92fr_1.08fr] lg:items-center lg:gap-10">
        <section class="order-1 rounded-[2rem] border border-slate-200 bg-white/95 p-6 shadow-[0_24px_60px_-30px_rgba(15,23,42,0.35)] sm:p-8">
            <div class="mb-6 sm:mb-8">
                <span class="inline-flex rounded-full border border-amber-300 bg-amber-100 px-4 py-1 text-[11px] font-semibold uppercase tracking-[0.24em] text-amber-800">
                    Acceso seguro
                </span>
                <h2 class="mt-4 text-2xl font-semibold text-slate-950 sm:text-3xl">Iniciar sesion</h2>
                <p class="mt-2 text-sm leading-6 text-slate-500">Usa tu correo electronico o tu nombre de usuario para entrar al panel.</p>
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
                    <label for="email" class="block text-sm font-medium text-slate-700">Correo o usuario <span class="text-rose-600">*</span></label>
                    <input id="email" name="email" type="text" value="{{ old('email') }}" required autofocus autocomplete="username" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3.5 text-base text-slate-900 outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-100">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700">Contrasena <span class="text-rose-600">*</span></label>
                    <input id="password" name="password" type="password" required autocomplete="current-password" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3.5 text-base text-slate-900 outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-100">
                </div>

                <label for="remember_me" class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600">
                    <input id="remember_me" name="remember" type="checkbox" class="size-4 rounded border-slate-300 text-amber-600 focus:ring-amber-500">
                    Mantener sesion iniciada
                </label>

                <button type="submit" class="inline-flex w-full justify-center rounded-full bg-slate-950 px-5 py-3.5 text-sm font-semibold text-white transition hover:bg-slate-800">
                    Entrar al panel
                </button>

                <div class="flex items-center justify-between gap-4 text-sm">
                    @if (Route::has('password.request'))
                        <a class="text-slate-500 transition hover:text-slate-950" href="{{ route('password.request') }}">
                            Olvide mi contrasena
                        </a>
                    @endif
                </div>
            </form>
        </section>

        <section class="order-2 space-y-6 lg:order-none lg:ps-4">
            <span class="inline-flex rounded-full border border-amber-300 bg-amber-100 px-4 py-1 text-xs font-semibold uppercase tracking-[0.24em] text-amber-800">
                Plataforma de invitaciones
            </span>
            <div class="space-y-4">
                <h1 class="max-w-2xl text-3xl font-semibold tracking-tight text-slate-950 sm:text-4xl lg:text-5xl">
                    Accede rapido y administra eventos, invitados e invitaciones desde un solo panel.
                </h1>
                <p class="max-w-xl text-base leading-7 text-slate-600">
                    Organiza eventos, carga listas desde Excel, comparte invitaciones por WhatsApp y controla accesos desde el modulo validador.
                </p>
            </div>

            <div class="grid gap-4 sm:grid-cols-3 lg:grid-cols-1 xl:grid-cols-3">
                <div class="rounded-3xl border border-slate-200 bg-white px-5 py-4 shadow-sm">
                    <p class="text-sm font-semibold text-slate-950">Eventos</p>
                    <p class="mt-1 text-sm leading-6 text-slate-500">Configura nombre, fecha, hora y lugar desde una interfaz clara.</p>
                </div>
                <div class="rounded-3xl border border-slate-200 bg-white px-5 py-4 shadow-sm">
                    <p class="text-sm font-semibold text-slate-950">Invitados</p>
                    <p class="mt-1 text-sm leading-6 text-slate-500">Carga masiva en Excel o registro individual con invitaciones automaticas.</p>
                </div>
                <div class="rounded-3xl border border-slate-200 bg-white px-5 py-4 shadow-sm">
                    <p class="text-sm font-semibold text-slate-950">Validacion</p>
                    <p class="mt-1 text-sm leading-6 text-slate-500">Escanea QR, controla ingresos y permite reingresos desde el sistema.</p>
                </div>
            </div>
        </section>
    </div>
</x-guest-layout>
