<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium uppercase tracking-[0.2em] text-amber-700">Panel personal</p>
            <h2 class="mt-1 text-2xl font-semibold text-slate-950">{{ __('Dashboard') }}</h2>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl space-y-8 sm:px-6 lg:px-8">
            <section class="grid gap-4 md:grid-cols-4">
                <article class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-sm text-slate-500">Eventos creados</p>
                    <p class="mt-3 text-3xl font-semibold text-slate-950">{{ $metrics['events'] }}</p>
                </article>
                <article class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-sm text-slate-500">Invitados registrados</p>
                    <p class="mt-3 text-3xl font-semibold text-slate-950">{{ $metrics['guests'] }}</p>
                </article>
                <article class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-sm text-slate-500">Invitaciones a enviar</p>
                    <p class="mt-3 text-3xl font-semibold text-slate-950">{{ $metrics['invitations'] }}</p>
                </article>
                <article class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-sm text-slate-500">Siguiente evento</p>
                    @if ($metrics['nextEvent'])
                        <p class="mt-3 text-lg font-semibold text-slate-950">{{ $metrics['nextEvent']->name }}</p>
                        <p class="mt-1 text-sm text-slate-500">{{ $metrics['nextEvent']->event_date->format('d M Y') }}</p>
                    @else
                        <p class="mt-3 text-lg font-semibold text-slate-950">Sin agenda</p>
                    @endif
                </article>
            </section>

            <section class="grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
                <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="mb-6 flex items-center justify-between gap-4">
                        <div>
                            <h3 class="text-xl font-semibold text-slate-950">Eventos recientes</h3>
                            <p class="mt-1 text-sm text-slate-500">Accesos rapidos a tus eventos e invitados.</p>
                        </div>
                        <a href="{{ route('events.create') }}" class="inline-flex rounded-full bg-slate-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                            Nuevo evento
                        </a>
                    </div>

                    <div class="space-y-4">
                        @forelse ($events as $event)
                            <a href="{{ route('events.show', $event) }}" class="flex flex-col gap-4 rounded-[1.5rem] border border-slate-200 px-5 py-4 transition hover:border-amber-400 hover:bg-amber-50/50 md:flex-row md:items-center md:justify-between">
                                <div>
                                    <p class="text-lg font-semibold text-slate-950">{{ $event->name }}</p>
                                    <p class="mt-1 text-sm text-slate-500">{{ $event->venue }}</p>
                                </div>
                                <div class="flex items-center gap-6 text-sm text-slate-500">
                                    <span>{{ $event->event_date->format('d M Y') }}</span>
                                    <span>{{ $event->event_time->format('H:i') }}</span>
                                    <span>{{ $event->guests_count }} invitados</span>
                                </div>
                            </a>
                        @empty
                            <div class="rounded-[1.5rem] border border-dashed border-slate-300 px-6 py-12 text-center">
                                <p class="text-lg font-semibold text-slate-900">Aun no has creado eventos.</p>
                                <p class="mt-2 text-sm text-slate-500">Empieza por registrar el nombre, fecha, hora y lugar del primer evento.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="rounded-[2rem] border border-slate-200 bg-slate-950 p-6 text-white shadow-[0_24px_60px_-30px_rgba(15,23,42,0.7)]">
                    <p class="text-sm font-semibold uppercase tracking-[0.22em] text-amber-300">Tu perfil</p>
                    <h3 class="mt-3 text-2xl font-semibold">{{ Auth::user()->name }}</h3>
                    <p class="mt-1 text-sm text-white/65">{{ '@'.Auth::user()->username }}</p>
                    <p class="mt-6 text-sm leading-7 text-white/70">
                        Desde aqui podras centralizar la organizacion de tus eventos, controlar invitados y preparar el envio de invitaciones con una lista limpia y exportable.
                    </p>

                    <div class="mt-8 space-y-3">
                        <div class="rounded-[1.5rem] bg-white/8 px-5 py-4 ring-1 ring-white/10">
                            <p class="text-xs uppercase tracking-[0.18em] text-white/50">Correo</p>
                            <p class="mt-2 text-sm font-medium text-white">{{ Auth::user()->email }}</p>
                        </div>
                        <div class="rounded-[1.5rem] bg-white/8 px-5 py-4 ring-1 ring-white/10">
                            <p class="text-xs uppercase tracking-[0.18em] text-white/50">Usuario</p>
                            <p class="mt-2 text-sm font-medium text-white">{{ Auth::user()->username }}</p>
                        </div>
                        <div class="rounded-[1.5rem] bg-white/8 px-5 py-4 ring-1 ring-white/10">
                            <p class="text-xs uppercase tracking-[0.18em] text-white/50">Rol</p>
                            <p class="mt-2 text-sm font-medium text-white">{{ Auth::user()->isAdmin() ? 'Admin' : (Auth::user()->isValidator() ? 'Validator' : 'Organizer') }}</p>
                        </div>
                        @if (Auth::user()->isAdmin())
                            <a href="{{ route('users.index') }}" class="inline-flex rounded-full bg-white px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-slate-100">
                                Gestionar usuarios
                            </a>
                        @endif
                        @if (Auth::user()->canValidateInvitations())
                            <a href="{{ route('validator.dashboard') }}" class="inline-flex rounded-full bg-amber-400 px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-amber-300">
                                Ir al modulo validador
                            </a>
                        @endif
                        <a href="{{ route('profile.show') }}" class="inline-flex rounded-full border border-white/20 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/10">
                            Editar perfil
                        </a>
                    </div>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
