<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.2em] text-amber-700">Invitaciones del invitado</p>
                <h2 class="mt-1 text-2xl font-semibold text-slate-950">{{ $guest->full_name }}</h2>
            </div>
            <a href="{{ route('events.show', $event) }}" class="inline-flex rounded-full border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-950 hover:text-slate-950">
                Volver al evento
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

            @if ($errors->any())
                <div class="rounded-[1.5rem] border border-rose-200 bg-rose-50 px-5 py-4 text-sm text-rose-700">
                    <ul class="list-disc ps-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <section class="rounded-[2rem] border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div class="min-w-0">
                        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-amber-700">Resumen</p>
                        <h3 class="mt-2 text-2xl font-semibold text-slate-950">{{ $event->name }}</h3>
                        <p class="mt-2 text-sm text-slate-500">
                            {{ $event->event_date->format('d M Y') }} · {{ $event->venue }}
                        </p>
                        <p class="mt-1 text-sm text-slate-500">
                            @if ($normalizedPhone)
                                WhatsApp: {{ $normalizedPhone }}
                            @else
                                Telefono: {{ $guest->phone }}
                            @endif
                        </p>
                    </div>

                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                        <div class="rounded-[1.25rem] bg-slate-50 px-4 py-3">
                            <p class="text-[11px] uppercase tracking-[0.18em] text-slate-400">Cupos</p>
                            <p class="mt-2 text-lg font-semibold text-slate-950">{{ $guest->invitation_count }}</p>
                        </div>
                        <div class="rounded-[1.25rem] bg-slate-50 px-4 py-3">
                            <p class="text-[11px] uppercase tracking-[0.18em] text-slate-400">Generadas</p>
                            <p class="mt-2 text-lg font-semibold text-slate-950">{{ $guest->invitations->count() }}</p>
                        </div>
                        <div class="rounded-[1.25rem] bg-slate-50 px-4 py-3">
                            <p class="text-[11px] uppercase tracking-[0.18em] text-slate-400">Telefono</p>
                            <p class="mt-2 text-sm font-semibold text-slate-950">{{ $guest->phone }}</p>
                        </div>
                        <div class="rounded-[1.25rem] bg-slate-50 px-4 py-3">
                            <p class="text-[11px] uppercase tracking-[0.18em] text-slate-400">Invitado</p>
                            <p class="mt-2 text-sm font-semibold text-slate-950">{{ $guest->full_name }}</p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                <div class="mb-4 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-xl font-semibold text-slate-950">Invitaciones individuales</h3>
                        <p class="mt-1 text-sm text-slate-500">Cada envio comparte una URL publica con vista previa de la invitacion. Desde esa pagina se puede visualizar y descargar la imagen PNG.</p>
                    </div>
                    <form method="POST" action="{{ route('events.guests.invitations.add', [$event, $guest]) }}">
                        @csrf
                        <button type="submit" class="inline-flex rounded-full bg-slate-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                            Agregar invitacion
                        </button>
                    </form>
                </div>

                <div class="overflow-hidden rounded-[1.5rem] border border-slate-200">
                    <div class="hidden grid-cols-[0.45fr_0.85fr_1.45fr_0.75fr_0.85fr_0.95fr_1.35fr] gap-4 bg-slate-50 px-5 py-3 text-xs font-semibold uppercase tracking-[0.18em] text-slate-500 lg:grid">
                        <span>#</span>
                        <span>Codigo</span>
                        <span>URL publica</span>
                        <span>Estado</span>
                        <span>Compartida</span>
                        <span>Validada por</span>
                        <span>Acciones</span>
                    </div>

                    <div class="divide-y divide-slate-200">
                        @foreach ($guest->invitations as $invitation)
                            <article class="grid gap-4 px-5 py-4 lg:grid-cols-[0.45fr_0.85fr_1.45fr_0.75fr_0.85fr_0.95fr_1.35fr] lg:items-center">
                                <div>
                                    <p class="text-xs uppercase tracking-[0.18em] text-slate-400 lg:hidden">#</p>
                                    <p class="text-sm font-semibold text-slate-950">{{ $invitation->sequence_number }}</p>
                                </div>
                                <div>
                                    <p class="text-xs uppercase tracking-[0.18em] text-slate-400 lg:hidden">Codigo</p>
                                    <p class="font-mono text-sm text-slate-700">{{ $invitation->code }}</p>
                                </div>
                                <div>
                                    <p class="text-xs uppercase tracking-[0.18em] text-slate-400 lg:hidden">URL publica</p>
                                    <a href="{{ $invitation->publicUrl() }}" target="_blank" rel="noopener noreferrer" class="break-all text-sm font-medium text-slate-600 underline decoration-slate-300 underline-offset-4 transition hover:text-slate-950">
                                        {{ $invitation->publicUrl() }}
                                    </a>
                                    <a href="{{ $invitation->imageUrl() }}" target="_blank" rel="noopener noreferrer" class="mt-2 inline-flex text-xs font-semibold text-slate-500 underline decoration-slate-300 underline-offset-4 transition hover:text-slate-900">
                                        Ver PNG directa
                                    </a>
                                </div>
                                <div>
                                    <p class="text-xs uppercase tracking-[0.18em] text-slate-400 lg:hidden">Estado</p>
                                    <p class="text-sm font-semibold {{ $invitation->isUsed() ? 'text-rose-700' : 'text-emerald-700' }}">
                                        {{ $invitation->isUsed() ? 'Usada' : 'Disponible' }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-xs uppercase tracking-[0.18em] text-slate-400 lg:hidden">Compartida</p>
                                    <p class="text-sm font-semibold text-slate-900">{{ $invitation->sent_at ? $invitation->sent_at->format('d/m H:i') : 'No' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs uppercase tracking-[0.18em] text-slate-400 lg:hidden">Validada por</p>
                                    <p class="text-sm font-semibold text-slate-900">{{ $invitation->validator?->name ?? 'Pendiente' }}</p>
                                </div>
                                <div>
                                    <div class="flex flex-wrap gap-2">
                                        @if ($normalizedPhone)
                                            <form method="POST" action="{{ route('events.guests.invitations.share', [$event, $guest, $invitation]) }}" target="_blank">
                                                @csrf
                                                <button type="submit" class="inline-flex rounded-full bg-slate-950 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
                                                    Enviar
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-sm text-slate-400">Sin telefono valido</span>
                                        @endif

                                        @if (! $invitation->isUsed())
                                            <form method="POST" action="{{ route('events.guests.invitations.destroy', [$event, $guest, $invitation]) }}" onsubmit="return confirm('Eliminar esta invitacion?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex rounded-full border border-rose-200 px-4 py-2 text-sm font-semibold text-rose-700 transition hover:bg-rose-50">
                                                    Eliminar
                                                </button>
                                            </form>
                                        @else
                                            <span class="inline-flex rounded-full bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-400">
                                                Bloqueada
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
