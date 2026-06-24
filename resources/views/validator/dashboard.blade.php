<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium uppercase tracking-[0.2em] text-amber-700">Modulo validador</p>
            <h2 class="mt-1 text-2xl font-semibold text-slate-950">Lectura y consumo de invitaciones</h2>
        </div>
    </x-slot>

    <div class="py-6 sm:py-10" x-data="validatorScanner()">
        <div class="mx-auto max-w-7xl space-y-5 px-4 sm:space-y-6 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-[1.5rem] border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif

            <section class="grid gap-6 lg:grid-cols-[0.9fr_1.1fr]">
                <div class="rounded-[2rem] border border-slate-200 bg-slate-950 p-5 text-white shadow-[0_24px_60px_-30px_rgba(15,23,42,0.75)] sm:p-6">
                    <p class="text-sm font-semibold uppercase tracking-[0.2em] text-amber-300">Escaner</p>
                    <h3 class="mt-3 text-xl font-semibold sm:text-2xl">Camara o codigo manual</h3>
                    <p class="mt-3 text-sm leading-7 text-white/70">
                        Si el navegador soporta lectura QR, puedes activar la camara. Si no, pega o escribe el codigo manualmente.
                    </p>

                    <form method="GET" action="{{ route('validator.dashboard') }}" class="mt-6 space-y-4" x-ref="lookupForm">
                        <div>
                            <label for="code" class="block text-sm font-medium text-white/80">Codigo</label>
                            <input id="code" name="code" type="text" value="{{ $lookupCode }}" x-model="manualCode" autocomplete="off" autocapitalize="characters" class="mt-2 w-full rounded-2xl border border-white/15 bg-white/8 px-4 py-3 text-base text-white outline-none transition focus:border-amber-400 focus:ring-4 focus:ring-amber-200/10">
                        </div>
                        <div class="grid gap-3 sm:flex sm:flex-wrap">
                            <button type="submit" class="inline-flex w-full items-center justify-center rounded-full bg-amber-400 px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-amber-300 sm:w-auto">
                                Verificar codigo
                            </button>
                            <button type="button" @click="openScanner()" class="inline-flex w-full items-center justify-center rounded-full border border-white/20 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/10 sm:w-auto">
                                Usar camara
                            </button>
                        </div>
                    </form>

                    <p class="mt-4 text-xs leading-6 text-white/50" x-show="! barcodeSupported">
                        Este navegador no expone `BarcodeDetector`. Usa el campo manual o abre el modulo desde Chrome en Android para escaneo directo.
                    </p>
                </div>

                <div class="rounded-[2rem] border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                    <h3 class="text-xl font-semibold text-slate-950">Resultado</h3>

                    @if ($invitation)
                        @php
                            $statusClasses = match (true) {
                                ! $invitation->isUsed() => 'border-emerald-200 bg-emerald-50 text-emerald-800',
                                $invitation->hasPendingReentry() => 'border-amber-200 bg-amber-50 text-amber-900',
                                default => 'border-rose-200 bg-rose-50 text-rose-800',
                            };

                            $statusLabel = match (true) {
                                ! $invitation->isUsed() => 'Invitacion valida para ingreso',
                                $invitation->hasPendingReentry() => 'Reingreso habilitado. Puede volver a ingresar.',
                                $invitation->hasRegisteredReentry() => 'Invitacion ya registrada. Ya tiene reingresos en historial.',
                                default => 'Invitacion ya registrada. Requiere habilitar reingreso.',
                            };
                        @endphp
                        <div class="mt-6 space-y-4">
                            <div class="rounded-[1.5rem] border p-5 {{ $statusClasses }}">
                                <p class="text-sm font-semibold">
                                    {{ $statusLabel }}
                                </p>
                                <p class="mt-2 font-mono text-sm text-slate-800">{{ $invitation->code }}</p>
                            </div>

                            <div class="grid gap-4 sm:grid-cols-2">
                                <div class="rounded-[1.5rem] border border-slate-200 p-4 sm:p-5">
                                    <p class="text-xs uppercase tracking-[0.18em] text-slate-400">Invitado</p>
                                    <p class="mt-2 text-lg font-semibold text-slate-950">{{ $invitation->guest->full_name }}</p>
                                </div>
                                <div class="rounded-[1.5rem] border border-slate-200 p-4 sm:p-5">
                                    <p class="text-xs uppercase tracking-[0.18em] text-slate-400">Evento</p>
                                    <p class="mt-2 text-lg font-semibold text-slate-950">{{ $invitation->event->name }}</p>
                                </div>
                                <div class="rounded-[1.5rem] border border-slate-200 p-4 sm:p-5">
                                    <p class="text-xs uppercase tracking-[0.18em] text-slate-400">Secuencia</p>
                                    <p class="mt-2 text-lg font-semibold text-slate-950">{{ $invitation->sequence_number }} / {{ $invitation->guest->invitation_count }}</p>
                                </div>
                                <div class="rounded-[1.5rem] border border-slate-200 p-4 sm:p-5">
                                    <p class="text-xs uppercase tracking-[0.18em] text-slate-400">Validada por</p>
                                    <p class="mt-2 text-lg font-semibold text-slate-950">{{ $invitation->validator?->name ?? 'Pendiente' }}</p>
                                </div>
                                <div class="rounded-[1.5rem] border border-slate-200 p-4 sm:p-5">
                                    <p class="text-xs uppercase tracking-[0.18em] text-slate-400">Primer ingreso</p>
                                    <p class="mt-2 text-lg font-semibold text-slate-950">{{ $invitation->used_at?->format('d/m/Y H:i') ?? 'Pendiente' }}</p>
                                </div>
                                <div class="rounded-[1.5rem] border border-slate-200 p-4 sm:p-5">
                                    <p class="text-xs uppercase tracking-[0.18em] text-slate-400">Reingresos registrados</p>
                                    <p class="mt-2 text-lg font-semibold text-slate-950">{{ $invitation->reentry_count }}</p>
                                </div>
                                <div class="rounded-[1.5rem] border border-slate-200 p-4 sm:p-5">
                                    <p class="text-xs uppercase tracking-[0.18em] text-slate-400">Reingreso habilitado por</p>
                                    <p class="mt-2 text-lg font-semibold text-slate-950">{{ $invitation->reentryEnabledBy?->name ?? 'No habilitado' }}</p>
                                </div>
                                <div class="rounded-[1.5rem] border border-slate-200 p-4 sm:p-5">
                                    <p class="text-xs uppercase tracking-[0.18em] text-slate-400">Ultimo reingreso</p>
                                    <p class="mt-2 text-lg font-semibold text-slate-950">{{ $invitation->reentry_used_at?->format('d/m/Y H:i') ?? 'Sin reingreso' }}</p>
                                </div>
                            </div>

                            @if (! $invitation->isUsed())
                                <form method="POST" action="{{ route('validator.invitations.consume', $invitation) }}">
                                    @csrf
                                    <button type="submit" class="inline-flex w-full items-center justify-center rounded-full bg-slate-950 px-6 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 sm:w-auto">
                                        Marcar como utilizada
                                    </button>
                                </form>
                            @elseif ($invitation->hasPendingReentry())
                                <div class="flex flex-col gap-3 sm:flex-row">
                                    <form method="POST" action="{{ route('validator.invitations.consume', $invitation) }}">
                                        @csrf
                                        <button type="submit" class="inline-flex w-full items-center justify-center rounded-full bg-amber-500 px-6 py-3 text-sm font-semibold text-white transition hover:bg-amber-400 sm:w-auto">
                                            Registrar reingreso
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('validator.invitations.enable-reentry', $invitation) }}">
                                        @csrf
                                        <button type="submit" class="inline-flex w-full items-center justify-center rounded-full border border-slate-300 px-6 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-950 hover:text-slate-950 sm:w-auto">
                                            Renovar reingreso
                                        </button>
                                    </form>
                                </div>
                            @else
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                                    <p class="text-sm text-slate-500">
                                        Esta invitacion ya fue registrada. Si la persona salio del evento, habilita el reingreso para permitir una nueva entrada.
                                    </p>
                                    <form method="POST" action="{{ route('validator.invitations.enable-reentry', $invitation) }}">
                                        @csrf
                                        <button type="submit" class="inline-flex w-full items-center justify-center rounded-full bg-slate-950 px-6 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 sm:w-auto">
                                            Habilitar reingreso
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @elseif ($lookupCode !== '')
                        <div class="mt-6 rounded-[1.5rem] border border-rose-200 bg-rose-50 p-5 text-sm text-rose-700">
                            No se encontro una invitacion con el codigo <span class="font-mono font-semibold">{{ strtoupper($lookupCode) }}</span>.
                        </div>
                    @else
                        <div class="mt-6 rounded-[1.5rem] border border-dashed border-slate-300 p-8 text-center">
                            <p class="text-lg font-semibold text-slate-950">Sin codigo cargado.</p>
                            <p class="mt-2 text-sm text-slate-500">Escanea el QR o escribe el codigo unico para revisar el estado de la invitacion.</p>
                        </div>
                    @endif
                </div>
            </section>

            <section class="rounded-[2rem] border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                <h3 class="text-xl font-semibold text-slate-950">Validaciones recientes</h3>
                <div class="mt-6 overflow-hidden rounded-[1.5rem] border border-slate-200">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr class="text-left text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">
                                    <th class="px-4 py-4 sm:px-5">Invitado</th>
                                    <th class="px-4 py-4 sm:px-5">Evento</th>
                                    <th class="px-4 py-4 sm:px-5">Codigo</th>
                                    <th class="px-4 py-4 sm:px-5">Fecha</th>
                                    <th class="px-4 py-4 sm:px-5">Validador</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white">
                    @forelse ($recentValidations as $recent)
                                <tr class="align-top text-sm text-slate-600">
                                    <td class="px-4 py-4 sm:px-5">
                                        <p class="font-semibold text-slate-950">{{ $recent->guest->full_name }}</p>
                                    </td>
                                    <td class="px-4 py-4 sm:px-5">
                                        <p class="min-w-48 text-slate-600">{{ $recent->event->name }}</p>
                                    </td>
                                    <td class="px-4 py-4 sm:px-5">
                                        <span class="block min-w-44 break-all font-mono text-xs font-semibold text-slate-700 sm:text-sm">{{ $recent->code }}</span>
                                    </td>
                                    <td class="px-4 py-4 sm:px-5">
                                        <span>{{ $recent->used_at?->format('d/m/Y H:i') }}</span>
                                    </td>
                                    <td class="px-4 py-4 sm:px-5">
                                        <span>{{ $recent->validator?->name ?? 'Pendiente' }}</span>
                                    </td>
                                </tr>
                    @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <p class="text-lg font-semibold text-slate-950">Aun no hay validaciones registradas.</p>
                                    </td>
                                </tr>
                    @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($recentValidations->hasPages())
                    <div class="mt-4">
                        {{ $recentValidations->onEachSide(1)->links() }}
                    </div>
                @endif
            </section>
        </div>

        <div
            x-cloak
            x-show="scannerModalOpen"
            x-transition.opacity
            @keydown.escape.window="closeScanner()"
            class="fixed inset-0 z-50 flex items-end bg-slate-950/75 p-3 sm:items-center sm:justify-center sm:p-6"
        >
            <div
                x-show="scannerModalOpen"
                x-transition
                class="w-full overflow-hidden rounded-[2rem] border border-slate-200 bg-slate-950 text-white shadow-[0_24px_80px_-20px_rgba(15,23,42,0.9)] sm:max-w-2xl"
            >
                <div class="flex items-start justify-between gap-4 border-b border-white/10 px-5 py-4 sm:px-6">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-amber-300">Escaner QR</p>
                        <h3 class="mt-2 text-lg font-semibold sm:text-xl">Escanea la invitacion</h3>
                    </div>
                    <button type="button" @click="closeScanner()" class="inline-flex size-10 items-center justify-center rounded-full border border-white/15 text-lg text-white/70 transition hover:bg-white/10 hover:text-white">
                        ×
                    </button>
                </div>

                <div class="space-y-4 px-5 py-5 sm:px-6">
                    <p class="text-sm leading-6 text-white/70">
                        Apunta la camara al codigo QR. Al detectarlo, el codigo se colocara en la busqueda automaticamente.
                    </p>

                    <div class="space-y-3" x-show="scannerActive || scannerError || scanMessage" x-cloak>
                        <div x-show="scanMessage" x-text="scanMessage" class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white/80"></div>
                        <div x-show="scannerError" x-text="scannerError" class="rounded-2xl border border-rose-400/30 bg-rose-400/10 px-4 py-3 text-sm text-rose-100"></div>
                    </div>

                    <div class="overflow-hidden rounded-[1.5rem] border border-white/10 bg-black">
                        <div class="relative aspect-square w-full">
                            <video x-ref="video" autoplay muted playsinline class="h-full w-full object-cover"></video>
                            <div class="pointer-events-none absolute inset-0 flex items-center justify-center p-5">
                                <div class="h-56 w-56 max-w-full rounded-[2rem] border-2 border-white/80 shadow-[0_0_0_9999px_rgba(2,6,23,0.35)] sm:h-72 sm:w-72"></div>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                        <button type="button" @click="closeScanner()" class="inline-flex w-full items-center justify-center rounded-full border border-white/20 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/10 sm:w-auto">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function validatorScanner() {
            return {
                manualCode: @js($lookupCode),
                scannerModalOpen: false,
                scannerActive: false,
                barcodeSupported: 'BarcodeDetector' in window,
                stream: null,
                scanTimer: null,
                scanMessage: '',
                scannerError: '',
                isProcessingScan: false,
                async openScanner() {
                    if (! this.barcodeSupported) {
                        this.scannerError = 'Este navegador no soporta lectura QR directa.';
                        return;
                    }

                    this.scannerModalOpen = true;
                    this.scannerError = '';
                    this.scanMessage = 'Apunta la camara al codigo QR para validar automaticamente.';
                    this.scannerActive = true;
                    this.isProcessingScan = false;

                    try {
                        await this.$nextTick();

                        this.stream = await navigator.mediaDevices.getUserMedia({
                            video: {
                                facingMode: { ideal: 'environment' },
                            },
                            audio: false,
                        });

                        const video = this.$refs.video;
                        video.srcObject = this.stream;
                        video.setAttribute('playsinline', 'true');

                        await new Promise((resolve) => {
                            if (video.readyState >= 1) {
                                resolve();
                                return;
                            }

                            video.onloadedmetadata = () => resolve();
                        });

                        await video.play();
                        this.scanFrame();
                    } catch (error) {
                        this.scannerError = 'No fue posible abrir la camara. Revisa permisos del navegador.';
                        console.error(error);
                        this.closeScanner();
                    }
                },
                closeScanner() {
                    this.scannerModalOpen = false;
                    this.scanMessage = '';
                    this.scannerError = '';
                    this.isProcessingScan = false;
                    this.scannerActive = false;
                    if (this.scanTimer) {
                        cancelAnimationFrame(this.scanTimer);
                        this.scanTimer = null;
                    }
                    if (this.$refs.video) {
                        this.$refs.video.pause();
                        this.$refs.video.srcObject = null;
                    }
                    this.stream?.getTracks().forEach(track => track.stop());
                    this.stream = null;
                },
                submitLookup(value) {
                    this.manualCode = value;
                    this.$nextTick(() => this.$refs.lookupForm.requestSubmit());
                },
                extractLookupValue(value) {
                    const trimmedValue = (value || '').trim();

                    if (trimmedValue === '') {
                        return '';
                    }

                    try {
                        const parsedUrl = new URL(trimmedValue);
                        const segments = parsedUrl.pathname.split('/').filter(Boolean);

                        if (segments[0] === 'i' && segments[1]) {
                            return segments[1];
                        }
                    } catch (error) {
                        // Not a URL, continue with raw value.
                    }

                    return trimmedValue;
                },
                async scanFrame() {
                    if (! this.scannerActive || this.isProcessingScan) {
                        return;
                    }

                    try {
                        const detector = new BarcodeDetector({ formats: ['qr_code'] });
                        const barcodes = await detector.detect(this.$refs.video);

                        if (barcodes.length > 0) {
                            const value = this.extractLookupValue(barcodes[0].rawValue || '');
                            if (value !== '') {
                                this.isProcessingScan = true;
                                this.scanMessage = 'Codigo detectado. Consultando invitacion...';
                                this.closeScanner();
                                this.submitLookup(value);
                                return;
                            }
                        }
                    } catch (error) {
                        console.error(error);
                        this.scannerError = 'Ocurrio un error al leer el codigo QR.';
                        this.closeScanner();
                        return;
                    }

                    this.scanTimer = requestAnimationFrame(() => this.scanFrame());
                },
            };
        }
    </script>
</x-app-layout>
