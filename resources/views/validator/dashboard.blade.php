<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium uppercase tracking-[0.2em] text-amber-700">Modulo validador</p>
            <h2 class="mt-1 text-2xl font-semibold text-slate-950">Lectura y consumo de invitaciones</h2>
        </div>
    </x-slot>

    <div class="py-10" x-data="validatorScanner()">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-[1.5rem] border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif

            <section class="grid gap-6 lg:grid-cols-[0.9fr_1.1fr]">
                <div class="rounded-[2rem] border border-slate-200 bg-slate-950 p-6 text-white shadow-[0_24px_60px_-30px_rgba(15,23,42,0.75)]">
                    <p class="text-sm font-semibold uppercase tracking-[0.2em] text-amber-300">Escaner</p>
                    <h3 class="mt-3 text-2xl font-semibold">Camara o codigo manual</h3>
                    <p class="mt-4 text-sm leading-7 text-white/70">
                        Si el navegador soporta lectura QR, puedes activar la camara. Si no, pega o escribe el codigo manualmente.
                    </p>

                    <form method="GET" action="{{ route('validator.dashboard') }}" class="mt-6 space-y-4">
                        <div>
                            <label for="code" class="block text-sm font-medium text-white/80">Codigo</label>
                            <input id="code" name="code" type="text" value="{{ $lookupCode }}" x-model="manualCode" class="mt-2 w-full rounded-2xl border border-white/15 bg-white/8 px-4 py-3 text-white outline-none transition focus:border-amber-400 focus:ring-4 focus:ring-amber-200/10">
                        </div>
                        <div class="flex flex-wrap gap-3">
                            <button type="submit" class="inline-flex rounded-full bg-amber-400 px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-amber-300">
                                Verificar codigo
                            </button>
                            <button type="button" @click="toggleScanner()" class="inline-flex rounded-full border border-white/20 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/10">
                                <span x-text="scannerActive ? 'Detener camara' : 'Usar camara'"></span>
                            </button>
                        </div>
                    </form>

                    <div x-show="scannerActive" x-cloak class="mt-6 overflow-hidden rounded-[1.5rem] border border-white/10 bg-black">
                        <video x-ref="video" autoplay muted playsinline class="aspect-video w-full object-cover"></video>
                    </div>

                    <p class="mt-4 text-xs leading-6 text-white/50" x-show="! barcodeSupported">
                        Este navegador no expone `BarcodeDetector`. Usa el campo manual o abre el modulo desde Chrome en Android para escaneo directo.
                    </p>
                </div>

                <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="text-xl font-semibold text-slate-950">Resultado</h3>

                    @if ($invitation)
                        <div class="mt-6 space-y-4">
                            <div class="rounded-[1.5rem] {{ $invitation->isUsed() ? 'border-rose-200 bg-rose-50' : 'border-emerald-200 bg-emerald-50' }} border p-5">
                                <p class="text-sm font-semibold {{ $invitation->isUsed() ? 'text-rose-800' : 'text-emerald-800' }}">
                                    {{ $invitation->isUsed() ? 'Invitacion ya utilizada' : 'Invitacion valida para ingreso' }}
                                </p>
                                <p class="mt-2 font-mono text-sm text-slate-800">{{ $invitation->code }}</p>
                            </div>

                            <div class="grid gap-4 sm:grid-cols-2">
                                <div class="rounded-[1.5rem] border border-slate-200 p-5">
                                    <p class="text-xs uppercase tracking-[0.18em] text-slate-400">Invitado</p>
                                    <p class="mt-2 text-lg font-semibold text-slate-950">{{ $invitation->guest->full_name }}</p>
                                </div>
                                <div class="rounded-[1.5rem] border border-slate-200 p-5">
                                    <p class="text-xs uppercase tracking-[0.18em] text-slate-400">Evento</p>
                                    <p class="mt-2 text-lg font-semibold text-slate-950">{{ $invitation->event->name }}</p>
                                </div>
                                <div class="rounded-[1.5rem] border border-slate-200 p-5">
                                    <p class="text-xs uppercase tracking-[0.18em] text-slate-400">Secuencia</p>
                                    <p class="mt-2 text-lg font-semibold text-slate-950">{{ $invitation->sequence_number }} / {{ $invitation->guest->invitation_count }}</p>
                                </div>
                                <div class="rounded-[1.5rem] border border-slate-200 p-5">
                                    <p class="text-xs uppercase tracking-[0.18em] text-slate-400">Validada por</p>
                                    <p class="mt-2 text-lg font-semibold text-slate-950">{{ $invitation->validator?->name ?? 'Pendiente' }}</p>
                                </div>
                            </div>

                            @if (! $invitation->isUsed())
                                <form method="POST" action="{{ route('validator.invitations.consume', $invitation) }}">
                                    @csrf
                                    <button type="submit" class="inline-flex rounded-full bg-slate-950 px-6 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                                        Marcar como utilizada
                                    </button>
                                </form>
                            @else
                                <p class="text-sm text-slate-500">
                                    Consumida el {{ $invitation->used_at?->format('d/m/Y H:i') }}.
                                </p>
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

            <section class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="text-xl font-semibold text-slate-950">Validaciones recientes</h3>
                <div class="mt-6 space-y-3">
                    @forelse ($recentValidations as $recent)
                        <div class="flex flex-col gap-3 rounded-[1.5rem] border border-slate-200 px-5 py-4 md:flex-row md:items-center md:justify-between">
                            <div>
                                <p class="font-semibold text-slate-950">{{ $recent->guest->full_name }}</p>
                                <p class="mt-1 text-sm text-slate-500">{{ $recent->event->name }}</p>
                            </div>
                            <div class="flex items-center gap-6 text-sm text-slate-500">
                                <span class="font-mono">{{ $recent->code }}</span>
                                <span>{{ $recent->used_at?->format('d/m H:i') }}</span>
                                <span>{{ $recent->validator?->name }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-[1.5rem] border border-dashed border-slate-300 px-6 py-12 text-center">
                            <p class="text-lg font-semibold text-slate-950">Aun no hay validaciones registradas.</p>
                        </div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>

    <script>
        function validatorScanner() {
            return {
                manualCode: @js($lookupCode),
                scannerActive: false,
                barcodeSupported: 'BarcodeDetector' in window,
                stream: null,
                scanTimer: null,
                async toggleScanner() {
                    if (this.scannerActive) {
                        this.stopScanner();
                        return;
                    }

                    if (! this.barcodeSupported) {
                        return;
                    }

                    this.stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
                    this.$refs.video.srcObject = this.stream;
                    this.scannerActive = true;
                    this.scanFrame();
                },
                stopScanner() {
                    this.scannerActive = false;
                    if (this.scanTimer) {
                        cancelAnimationFrame(this.scanTimer);
                    }
                    this.stream?.getTracks().forEach(track => track.stop());
                    this.stream = null;
                },
                async scanFrame() {
                    if (! this.scannerActive) {
                        return;
                    }

                    try {
                        const detector = new BarcodeDetector({ formats: ['qr_code'] });
                        const barcodes = await detector.detect(this.$refs.video);

                        if (barcodes.length > 0) {
                            const value = (barcodes[0].rawValue || '').trim();
                            if (value !== '') {
                                this.stopScanner();
                                const target = value.includes('/i/') ? value.split('/').pop() : value;
                                window.location.href = `{{ route('validator.dashboard') }}?code=${encodeURIComponent(target)}`;
                                return;
                            }
                        }
                    } catch (error) {
                        console.error(error);
                    }

                    this.scanTimer = requestAnimationFrame(() => this.scanFrame());
                },
            };
        }
    </script>
</x-app-layout>
