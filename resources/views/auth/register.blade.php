<x-guest-layout>
    <div class="mx-auto max-w-3xl rounded-[2rem] border border-slate-200 bg-white p-8 shadow-[0_24px_60px_-30px_rgba(15,23,42,0.35)] sm:p-10">
        <div class="mb-8">
            <h1 class="text-3xl font-semibold text-slate-950">Crear cuenta</h1>
            <p class="mt-2 text-sm text-slate-500">Registra tu usuario para empezar a administrar eventos e invitados.</p>
        </div>

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('register') }}" class="grid gap-5 md:grid-cols-2">
            @csrf

            <div class="md:col-span-2">
                <label for="name" class="block text-sm font-medium text-slate-700">Nombre completo</label>
                <input id="name" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-100" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" />
            </div>

            <div>
                <label for="username" class="block text-sm font-medium text-slate-700">Nombre de usuario</label>
                <input id="username" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-100" type="text" name="username" value="{{ old('username') }}" required autocomplete="username" />
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-slate-700">Correo electronico</label>
                <input id="email" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-100" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" />
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-slate-700">Contrasena</label>
                <input id="password" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-100" type="password" name="password" required autocomplete="new-password" />
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-slate-700">Confirmar contrasena</label>
                <input id="password_confirmation" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-100" type="password" name="password_confirmation" required autocomplete="new-password" />
            </div>

            <div class="md:col-span-2 flex flex-wrap items-center justify-between gap-4 pt-4">
                <a class="text-sm text-slate-500 transition hover:text-slate-950" href="{{ route('login') }}">
                    Ya tienes cuenta
                </a>

                <button class="inline-flex rounded-full bg-slate-950 px-6 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                    Crear mi cuenta
                </button>
            </div>
        </form>
    </div>
</x-guest-layout>
