<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium uppercase tracking-[0.2em] text-amber-700">Nuevo evento</p>
            <h2 class="mt-1 text-2xl font-semibold text-slate-950">Configura tu evento</h2>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-5xl sm:px-6 lg:px-8">
            <div class="grid gap-6 lg:grid-cols-[1.05fr_0.95fr]">
                <section class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm">
                    <h3 class="text-xl font-semibold text-slate-950">Datos del evento</h3>
                    <p class="mt-2 text-sm text-slate-500">Registra la informacion principal para empezar a cargar invitados.</p>

                    <form method="POST" action="{{ route('events.store') }}" class="mt-8 space-y-6">
                        @csrf
                        @include('partials.event-form')

                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <a href="{{ route('events.index') }}" class="text-sm font-medium text-slate-500 transition hover:text-slate-950">
                                Volver a eventos
                            </a>
                            <button type="submit" class="inline-flex rounded-full bg-slate-950 px-6 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                                Guardar evento
                            </button>
                        </div>
                    </form>
                </section>

                <aside class="rounded-[2rem] border border-slate-200 bg-slate-950 p-8 text-white shadow-[0_24px_60px_-30px_rgba(15,23,42,0.75)]">
                    <p class="text-sm font-semibold uppercase tracking-[0.22em] text-amber-300">Siguiente paso</p>
                    <h3 class="mt-3 text-2xl font-semibold">Despues podras cargar invitados de dos formas</h3>
                    <div class="mt-8 space-y-4">
                        <div class="rounded-[1.5rem] bg-white/8 p-5 ring-1 ring-white/10">
                            <p class="text-sm font-semibold">Carga desde Excel</p>
                            <p class="mt-2 text-sm leading-6 text-white/65">Descarga la plantilla oficial, completa nombre, apellidos, telefono y cantidad de invitaciones, y sube el archivo.</p>
                        </div>
                        <div class="rounded-[1.5rem] bg-white/8 p-5 ring-1 ring-white/10">
                            <p class="text-sm font-semibold">Registro manual</p>
                            <p class="mt-2 text-sm leading-6 text-white/65">Agrega invitados uno por uno desde una modal dentro del detalle del evento.</p>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</x-app-layout>
