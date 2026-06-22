<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.2em] text-amber-700">Administracion</p>
                <h2 class="mt-1 text-2xl font-semibold text-slate-950">Usuarios y roles</h2>
            </div>
            <span class="inline-flex rounded-full bg-slate-950 px-4 py-2 text-sm font-semibold text-white">
                {{ $users->count() }} usuarios
            </span>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-[1.5rem] border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-[1.5rem] border border-rose-200 bg-rose-50 px-5 py-4 text-sm text-rose-700">
                    <ul class="list-disc ps-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <section class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                <div class="space-y-4">
                    @foreach ($users as $managedUser)
                        <article class="flex flex-col gap-4 rounded-[1.5rem] border border-slate-200 px-5 py-5 lg:flex-row lg:items-center lg:justify-between">
                            <div>
                                <p class="text-lg font-semibold text-slate-950">{{ $managedUser->name }}</p>
                                <p class="mt-1 text-sm text-slate-500">{{ '@'.$managedUser->username }} · {{ $managedUser->email }}</p>
                            </div>

                            <form method="POST" action="{{ route('users.update-role', $managedUser) }}" class="flex flex-col gap-3 sm:flex-row sm:items-center">
                                @csrf
                                @method('PUT')
                                <select name="role" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium text-slate-900 outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-100">
                                    @foreach (['admin' => 'Admin', 'organizer' => 'Organizer', 'validator' => 'Validator'] as $value => $label)
                                        <option value="{{ $value }}" @selected($managedUser->role === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="inline-flex rounded-full bg-slate-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                                    Guardar rol
                                </button>
                            </form>
                        </article>
                    @endforeach
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
