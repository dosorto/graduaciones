<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.2em] text-amber-700">Carga masiva</p>
                <h2 class="mt-1 text-2xl font-semibold text-slate-950">{{ $event->name }}</h2>
                <p class="mt-2 text-sm text-slate-500">Paso 1: sube el archivo. Paso 2: revisa duplicados y errores antes de ejecutar la importacion.</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('events.guests.template') }}" class="inline-flex rounded-full bg-amber-400 px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-amber-300">
                    Descargar plantilla
                </a>
                <a href="{{ route('events.show', $event) }}" class="inline-flex rounded-full border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-950 hover:text-slate-950">
                    Volver al evento
                </a>
            </div>
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
                <div class="mb-6">
                    <p class="text-sm font-semibold uppercase tracking-[0.2em] text-amber-700">Paso 1</p>
                    <h3 class="mt-2 text-2xl font-semibold text-slate-950">Selecciona o arrastra el archivo</h3>
                </div>

                <form method="POST" action="{{ route('events.guests.import.preview', $event) }}" enctype="multipart/form-data" x-data="{ dragging: false, fileName: '' }" class="space-y-5">
                    @csrf
                    <label
                        for="guest_file"
                        @dragover.prevent="dragging = true"
                        @dragleave.prevent="dragging = false"
                        @drop.prevent="
                            dragging = false;
                            if ($event.dataTransfer.files.length) {
                                $refs.guestFile.files = $event.dataTransfer.files;
                                fileName = $event.dataTransfer.files[0].name;
                            }
                        "
                        :class="dragging ? 'border-amber-400 bg-amber-50/40' : 'border-slate-300 bg-slate-50'"
                        class="block cursor-pointer rounded-[1.75rem] border-2 border-dashed px-6 py-14 text-center transition hover:border-amber-400 hover:bg-amber-50/40"
                    >
                        <span class="mx-auto grid size-14 place-items-center rounded-full bg-white text-slate-700 shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 16V4m0 0 4 4m-4-4L8 8M4 16.5A2.5 2.5 0 0 0 6.5 19h11a2.5 2.5 0 0 0 2.5-2.5" />
                            </svg>
                        </span>
                        <span class="mt-5 block text-lg font-semibold text-slate-950">Arrastra el archivo aqui</span>
                        <span class="mt-2 block text-sm text-slate-500">o haz clic para seleccionarlo desde tu equipo</span>
                        <span x-show="fileName" x-cloak class="mt-4 block text-sm font-medium text-slate-700" x-text="fileName"></span>
                        <input
                            id="guest_file"
                            x-ref="guestFile"
                            @change="fileName = $event.target.files.length ? $event.target.files[0].name : ''"
                            name="guest_file"
                            type="file"
                            accept=".xlsx,.xls,.csv"
                            required
                            class="sr-only"
                        >
                    </label>

                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex rounded-full bg-slate-950 px-6 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                            Cargar y validar archivo
                        </button>
                    </div>
                </form>
            </section>

            <section class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-amber-700">Paso 2</p>
                        <h3 class="mt-2 text-2xl font-semibold text-slate-950">Revision de registros</h3>
                        <p class="mt-2 text-sm text-slate-500">Se valida duplicidad por nombre y telefono dentro del archivo y contra los invitados ya existentes en el evento.</p>
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 px-4 py-4">
                            <p class="text-xs uppercase tracking-[0.18em] text-slate-400">Total</p>
                            <p class="mt-2 text-2xl font-semibold text-slate-950">{{ $preview['summary']['total'] }}</p>
                        </div>
                        <div class="rounded-[1.5rem] border border-emerald-200 bg-emerald-50 px-4 py-4">
                            <p class="text-xs uppercase tracking-[0.18em] text-emerald-700">Validos</p>
                            <p class="mt-2 text-2xl font-semibold text-emerald-800">{{ $preview['summary']['valid'] }}</p>
                        </div>
                        <div class="rounded-[1.5rem] border border-rose-200 bg-rose-50 px-4 py-4">
                            <p class="text-xs uppercase tracking-[0.18em] text-rose-700">Errores</p>
                            <p class="mt-2 text-2xl font-semibold text-rose-800">{{ $preview['summary']['invalid'] }}</p>
                        </div>
                    </div>
                </div>

                <form
                    method="GET"
                    action="{{ route('events.guests.import.create', $event) }}"
                    x-data="{
                        timer: null,
                        submitSoon() {
                            clearTimeout(this.timer);
                            this.timer = setTimeout(() => this.$refs.submit.click(), 350);
                        }
                    }"
                    class="mt-6 rounded-[1.5rem] border border-slate-200 bg-slate-50 p-4"
                >
                    <div class="grid gap-4 lg:grid-cols-[1fr_180px_auto]">
                        <div>
                            <label for="search" class="block text-sm font-medium text-slate-700">Filtrar por nombre</label>
                            <input id="search" name="search" type="text" value="{{ $filters['search'] }}" placeholder="Nombre o apellido" @input="submitSoon()" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-100">
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
                                <a href="{{ route('events.guests.import.create', $event) }}" class="inline-flex rounded-full border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-950 hover:text-slate-950">
                                    Limpiar
                                </a>
                            @endif
                            <span class="text-sm text-slate-500">Actualizacion automatica</span>
                        </div>
                    </div>
                    <button x-ref="submit" type="submit" class="hidden">Aplicar</button>
                </form>

                <div class="mt-6 overflow-hidden rounded-[1.5rem] border border-slate-200">
                    <div class="hidden grid-cols-[0.5fr_1fr_1fr_1fr_0.8fr_1.3fr] gap-4 bg-slate-50 px-5 py-4 text-xs font-semibold uppercase tracking-[0.18em] text-slate-500 lg:grid">
                        <span>Fila</span>
                        <span>Nombre</span>
                        <span>Apellidos</span>
                        <span>Telefono</span>
                        <span>Invitaciones</span>
                        <span>Resultado</span>
                    </div>

                    <div class="divide-y divide-slate-200">
                        @forelse ($rows as $row)
                            <article class="grid gap-4 px-5 py-5 lg:grid-cols-[0.5fr_1fr_1fr_1fr_0.8fr_1.3fr] lg:items-start {{ $row['valid'] ? 'bg-emerald-50/40' : 'bg-rose-50/40' }}">
                                <div>
                                    <p class="text-xs uppercase tracking-[0.18em] text-slate-400 lg:hidden">Fila</p>
                                    <p class="text-sm font-semibold text-slate-950">{{ $row['row_number'] }}</p>
                                </div>
                                <div>
                                    <p class="text-xs uppercase tracking-[0.18em] text-slate-400 lg:hidden">Nombre</p>
                                    <p class="text-sm font-semibold text-slate-950">{{ $row['first_name'] }}</p>
                                </div>
                                <div>
                                    <p class="text-xs uppercase tracking-[0.18em] text-slate-400 lg:hidden">Apellidos</p>
                                    <p class="text-sm text-slate-700">{{ $row['last_name'] }}</p>
                                </div>
                                <div>
                                    <p class="text-xs uppercase tracking-[0.18em] text-slate-400 lg:hidden">Telefono</p>
                                    <p class="text-sm text-slate-700">{{ $row['phone'] }}</p>
                                </div>
                                <div>
                                    <p class="text-xs uppercase tracking-[0.18em] text-slate-400 lg:hidden">Invitaciones</p>
                                    <p class="inline-flex rounded-full {{ $row['valid'] ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }} px-3 py-1 text-sm font-semibold">{{ $row['invitation_count'] }}</p>
                                </div>
                                <div>
                                    <p class="text-xs uppercase tracking-[0.18em] text-slate-400 lg:hidden">Resultado</p>
                                    @if ($row['valid'])
                                        <p class="text-sm font-semibold text-emerald-700">Registro valido. Listo para importar.</p>
                                    @else
                                        <ul class="space-y-1 text-sm text-rose-700">
                                            @foreach ($row['errors'] as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            </article>
                        @empty
                            <div class="px-6 py-14 text-center">
                                <p class="text-lg font-semibold text-slate-950">Todavia no hay datos cargados para revisar.</p>
                                <p class="mt-2 text-sm text-slate-500">Sube un archivo en el paso 1 para iniciar la validacion.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                @if ($rows->hasPages())
                    <div class="mt-6">
                        {{ $rows->links() }}
                    </div>
                @endif

                <div class="mt-6 flex flex-wrap items-center justify-between gap-3">
                    <p class="text-sm {{ $preview['summary']['invalid'] === 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                        {{ $preview['summary']['invalid'] === 0 ? 'Todos los registros estan validados correctamente.' : 'Corrige los errores mostrados antes de ejecutar la importacion.' }}
                    </p>

                    <form method="POST" action="{{ route('events.guests.import.execute', $event) }}">
                        @csrf
                        <button type="submit" @disabled($preview['summary']['total'] === 0 || $preview['summary']['invalid'] > 0) class="inline-flex rounded-full bg-slate-950 px-6 py-3 text-sm font-semibold text-white transition enabled:hover:bg-slate-800 disabled:cursor-not-allowed disabled:bg-slate-300">
                            Ejecutar importacion
                        </button>
                    </form>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
