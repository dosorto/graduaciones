<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium uppercase tracking-[0.2em] text-amber-700">Edicion de evento</p>
            <h2 class="mt-1 text-2xl font-semibold text-slate-950">{{ $event->name }}</h2>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-5xl sm:px-6 lg:px-8">
            <div class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <h3 class="text-xl font-semibold text-slate-950">Actualizar informacion del evento</h3>
                        <p class="mt-2 text-sm text-slate-500">Modifica fecha, hora, lugar o descripcion sin perder la lista de invitados.</p>
                    </div>

                    <form method="POST" action="{{ route('events.destroy', $event) }}" onsubmit="return confirm('Se eliminara el evento y su lista de invitados. Continuar?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex rounded-full border border-rose-200 px-4 py-2 text-sm font-semibold text-rose-700 transition hover:bg-rose-50">
                            Eliminar evento
                        </button>
                    </form>
                </div>

                <form method="POST" action="{{ route('events.update', $event) }}" class="mt-8 space-y-6">
                    @csrf
                    @method('PUT')
                    @include('partials.event-form', ['event' => $event])

                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <a href="{{ route('events.show', $event) }}" class="text-sm font-medium text-slate-500 transition hover:text-slate-950">
                            Volver al detalle
                        </a>
                        <button type="submit" class="inline-flex rounded-full bg-slate-950 px-6 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                            Guardar cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
