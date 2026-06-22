<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.2em] text-amber-700">Gestion de eventos</p>
                <h2 class="mt-1 text-2xl font-semibold text-slate-950">Todos tus eventos</h2>
            </div>
            <a href="{{ route('events.create') }}" class="inline-flex rounded-full bg-slate-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                Crear evento
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-[1.5rem] border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif

            <div class="grid gap-5 lg:grid-cols-2">
                @forelse ($events as $event)
                    <article class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                        <div class="flex flex-col gap-5">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-xl font-semibold text-slate-950">{{ $event->name }}</p>
                                    <p class="mt-2 text-sm leading-6 text-slate-500">{{ $event->description ?: 'Sin descripcion registrada.' }}</p>
                                </div>
                                <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-amber-800">
                                    {{ $event->guests_count }} invitados
                                </span>
                            </div>

                            <div class="grid gap-3 sm:grid-cols-3">
                                <div class="rounded-2xl bg-slate-50 px-4 py-3">
                                    <p class="text-xs uppercase tracking-[0.18em] text-slate-400">Fecha</p>
                                    <p class="mt-2 text-sm font-semibold text-slate-900">{{ $event->event_date->format('d M Y') }}</p>
                                </div>
                                <div class="rounded-2xl bg-slate-50 px-4 py-3">
                                    <p class="text-xs uppercase tracking-[0.18em] text-slate-400">Hora</p>
                                    <p class="mt-2 text-sm font-semibold text-slate-900">{{ optional($event->event_time)->format('H:i') }}</p>
                                </div>
                                <div class="rounded-2xl bg-slate-50 px-4 py-3">
                                    <p class="text-xs uppercase tracking-[0.18em] text-slate-400">Lugar</p>
                                    <p class="mt-2 text-sm font-semibold text-slate-900">{{ $event->venue }}</p>
                                </div>
                            </div>

                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <a href="{{ route('events.show', $event) }}" class="inline-flex rounded-full bg-slate-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                                    Ver detalle
                                </a>
                                <a href="{{ route('events.edit', $event) }}" class="inline-flex rounded-full border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-950 hover:text-slate-950">
                                    Editar
                                </a>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="rounded-[2rem] border border-dashed border-slate-300 bg-white px-8 py-16 text-center lg:col-span-2">
                        <p class="text-2xl font-semibold text-slate-950">No has creado eventos todavia.</p>
                        <p class="mt-3 text-sm text-slate-500">Empieza registrando el nombre, fecha, hora y lugar del primer evento para luego agregar invitados.</p>
                        <a href="{{ route('events.create') }}" class="mt-8 inline-flex rounded-full bg-slate-950 px-6 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                            Crear primer evento
                        </a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
