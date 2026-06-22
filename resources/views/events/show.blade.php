<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.2em] text-amber-700">Detalle del evento</p>
                <h2 class="mt-1 text-2xl font-semibold text-slate-950">{{ $event->name }}</h2>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('events.edit', $event) }}" class="inline-flex rounded-full border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-950 hover:text-slate-950">
                    Editar evento
                </a>
                <a href="{{ route('events.invitation-design.edit', $event) }}" class="inline-flex rounded-full border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-950 hover:text-slate-950">
                    Disenar invitacion
                </a>
                <a href="{{ route('events.guests.template') }}" class="inline-flex rounded-full bg-amber-400 px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-amber-300">
                    Descargar plantilla
                </a>
                <a href="{{ route('events.guests.import.create', $event) }}" class="inline-flex rounded-full bg-slate-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                    Carga masiva
                </a>
            </div>
        </div>
    </x-slot>

    <div
        class="py-10"
        x-data="{
            guestModalOpen: @js($errors->has('first_name') || $errors->has('last_name') || $errors->has('phone') || $errors->has('invitation_count')),
            importModalOpen: false
        }"
    >
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-[1.5rem] border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-[1.5rem] border border-rose-200 bg-rose-50 px-5 py-4 text-sm text-rose-700">
                    <p class="font-semibold">Revisa la informacion enviada.</p>
                    <ul class="mt-2 list-disc ps-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <section class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                    <div class="max-w-3xl">
                        <p class="text-sm font-semibold uppercase tracking-[0.18em] text-amber-700">Detalle del evento</p>
                        <p class="mt-2 text-sm text-slate-600">
                            {{ $event->event_date->format('d M Y') }} · {{ optional($event->event_time)->format('H:i') }} · {{ $event->venue }}
                        </p>
                        <p class="mt-3 text-sm leading-7 text-slate-500">
                            {{ $event->description ?: 'Este evento aun no tiene descripcion.' }}
                        </p>
                    </div>

                    <div class="grid grid-cols-2 gap-3 lg:min-w-[520px] lg:grid-cols-4">
                        <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 px-4 py-4">
                            <p class="text-xs uppercase tracking-[0.18em] text-slate-400">Invitados</p>
                            <p class="mt-2 text-2xl font-semibold text-slate-950">{{ $guestMetrics['guests'] }}</p>
                        </div>
                        <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 px-4 py-4">
                            <p class="text-xs uppercase tracking-[0.18em] text-slate-400">Invitaciones</p>
                            <p class="mt-2 text-2xl font-semibold text-slate-950">{{ $guestMetrics['invitations'] }}</p>
                        </div>
                        <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 px-4 py-4">
                            <p class="text-xs uppercase tracking-[0.18em] text-slate-400">Enviadas</p>
                            <p class="mt-2 text-2xl font-semibold text-slate-950">{{ $guestMetrics['sent'] }}</p>
                        </div>
                        <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 px-4 py-4">
                            <p class="text-xs uppercase tracking-[0.18em] text-slate-400">Asistencia</p>
                            <p class="mt-2 text-2xl font-semibold text-slate-950">{{ $guestMetrics['attendance'] }}</p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-xl font-semibold text-slate-950">Lista de invitados</h3>
                        <p class="mt-1 text-sm text-slate-500">Busca invitados, define cuantos ver por pagina y gestiona cada registro.</p>
                    </div>
                    <button type="button" @click="guestModalOpen = true" class="inline-flex rounded-full bg-slate-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                        Agregar invitado
                    </button>
                </div>

                <form
                    method="GET"
                    action="{{ route('events.show', $event) }}"
                    x-data="{
                        timer: null,
                        submitSoon() {
                            clearTimeout(this.timer);
                            this.timer = setTimeout(() => this.$refs.submit.click(), 350);
                        }
                    }"
                    class="mb-6 rounded-[1.5rem] border border-slate-200 bg-slate-50 p-4"
                >
                    <div class="grid gap-4 lg:grid-cols-[1fr_180px_auto]">
                    <div>
                        <label for="search" class="block text-sm font-medium text-slate-700">Buscar invitado</label>
                        <input id="search" name="search" type="text" value="{{ $filters['search'] }}" placeholder="Nombre, apellido o telefono" @input="submitSoon()" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-100">
                    </div>
                    <div>
                        <label for="per_page" class="block text-sm font-medium text-slate-700">Registros por pagina</label>
                        <select id="per_page" name="per_page" @change="submitSoon()" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-100">
                            @foreach ([10, 25, 50, 100] as $option)
                                <option value="{{ $option }}" @selected($filters['per_page'] === $option)>{{ $option }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex flex-wrap items-end gap-3">
                        @if ($filters['search'] !== '' || $filters['per_page'] !== 10)
                            <a href="{{ route('events.show', $event) }}" class="inline-flex rounded-full border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-950 hover:text-slate-950">
                                Limpiar
                            </a>
                        @endif
                        <span class="text-sm text-slate-500">Actualizacion automatica</span>
                    </div>
                    </div>
                    <button x-ref="submit" type="submit" class="hidden">Aplicar</button>
                </form>

                <div class="overflow-hidden rounded-[1.5rem] border border-slate-200">
                    <div class="hidden grid-cols-[1.1fr_1.1fr_0.9fr_0.8fr_1fr] gap-4 bg-slate-50 px-5 py-4 text-xs font-semibold uppercase tracking-[0.18em] text-slate-500 md:grid">
                        <span>Nombre</span>
                        <span>Apellidos</span>
                        <span>Telefono</span>
                        <span>Invitaciones</span>
                        <span>Acciones</span>
                    </div>

                    <div class="divide-y divide-slate-200">
                        @forelse ($guests as $guest)
                            <article
                                onclick="window.location='{{ route('events.guests.invitations.show', [$event, $guest]) }}'"
                                class="grid cursor-pointer gap-4 px-5 py-5 transition hover:bg-amber-50/50 md:grid-cols-[1.1fr_1.1fr_0.9fr_0.8fr_1fr] md:items-center"
                            >
                                <div>
                                    <p class="text-xs uppercase tracking-[0.18em] text-slate-400 md:hidden">Nombre</p>
                                    <p class="text-sm font-semibold text-slate-950">{{ $guest->first_name }}</p>
                                </div>
                                <div>
                                    <p class="text-xs uppercase tracking-[0.18em] text-slate-400 md:hidden">Apellidos</p>
                                    <p class="text-sm text-slate-700">{{ $guest->last_name }}</p>
                                </div>
                                <div>
                                    <p class="text-xs uppercase tracking-[0.18em] text-slate-400 md:hidden">Telefono</p>
                                    <p class="text-sm text-slate-700">{{ $guest->phone }}</p>
                                </div>
                                <div>
                                    <p class="text-xs uppercase tracking-[0.18em] text-slate-400 md:hidden">Invitaciones</p>
                                    <div class="space-y-2">
                                        <p class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-sm font-semibold text-amber-800">{{ $guest->invitation_count }}</p>
                                        <p class="text-xs text-slate-500">{{ $guest->invitations_count }} generadas</p>
                                    </div>
                                </div>
                                <div class="flex flex-wrap items-center gap-3">
                                    <a href="{{ route('events.guests.invitations.show', [$event, $guest]) }}" onclick="event.stopPropagation()" class="text-sm font-semibold text-slate-700 transition hover:text-slate-950">
                                        Gestionar
                                    </a>
                                    <form method="POST" action="{{ route('events.guests.destroy', [$event, $guest]) }}" onclick="event.stopPropagation()" onsubmit="return confirm('Eliminar este invitado?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm font-semibold text-rose-600 transition hover:text-rose-700">
                                            Eliminar
                                        </button>
                                    </form>
                                </div>
                            </article>
                        @empty
                            <div class="px-6 py-14 text-center">
                                <p class="text-lg font-semibold text-slate-950">No hay invitados registrados con ese criterio.</p>
                                <p class="mt-2 text-sm text-slate-500">Ajusta la busqueda, cambia la paginacion o agrega un nuevo invitado.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                @if ($guests->hasPages())
                    <div class="mt-6">
                        {{ $guests->links() }}
                    </div>
                @endif
            </section>
        </div>

        <div
            x-cloak
            x-show="guestModalOpen"
            x-transition.opacity
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 px-4 py-8"
        >
            <div @click.outside="guestModalOpen = false" class="w-full max-w-2xl rounded-[2rem] bg-white p-8 shadow-2xl">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-amber-700">Registro manual</p>
                        <h3 class="mt-2 text-2xl font-semibold text-slate-950">Agregar invitado</h3>
                    </div>
                    <button type="button" @click="guestModalOpen = false" class="rounded-full bg-slate-100 p-2 text-slate-500 transition hover:bg-slate-200 hover:text-slate-800">
                        <span class="sr-only">Cerrar</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('events.guests.store', $event) }}" class="mt-8 space-y-5">
                    @csrf
                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-slate-700">Nombre</label>
                            <input id="first_name" name="first_name" type="text" value="{{ old('first_name') }}" required class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-100">
                        </div>
                        <div>
                            <label for="last_name" class="block text-sm font-medium text-slate-700">Apellidos</label>
                            <input id="last_name" name="last_name" type="text" value="{{ old('last_name') }}" required class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-100">
                        </div>
                        <div>
                            <label for="phone" class="block text-sm font-medium text-slate-700">Numero de telefono</label>
                            <input id="phone" name="phone" type="text" value="{{ old('phone') }}" required class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-100">
                        </div>
                        <div>
                            <label for="invitation_count" class="block text-sm font-medium text-slate-700">Cantidad de invitaciones</label>
                            <input id="invitation_count" name="invitation_count" type="number" min="1" max="20" value="{{ old('invitation_count', 1) }}" required class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-100">
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <button type="button" @click="guestModalOpen = false" class="text-sm font-medium text-slate-500 transition hover:text-slate-950">
                            Cancelar
                        </button>
                        <button type="submit" class="inline-flex rounded-full bg-slate-950 px-6 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                            Guardar invitado
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
