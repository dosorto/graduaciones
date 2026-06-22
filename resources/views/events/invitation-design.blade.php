@php
    $selectedPosition = old('invitation_qr_position', $event->invitationQrPosition());
    $selectedSize = (int) old('invitation_qr_size', $event->invitationQrSize());
    $positionLabels = [
        'left-top' => 'Izquierda superior',
        'center-top' => 'Centro superior',
        'right-top' => 'Derecha superior',
        'left-middle' => 'Izquierda medio',
        'center-middle' => 'Centro medio',
        'right-middle' => 'Derecha medio',
        'left-bottom' => 'Izquierda inferior',
        'center-bottom' => 'Centro inferior',
        'right-bottom' => 'Derecha inferior',
    ];
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.2em] text-amber-700">Diseno de invitacion</p>
                <h2 class="mt-1 text-2xl font-semibold text-slate-950">{{ $event->name }}</h2>
            </div>
            <a href="{{ route('events.show', $event) }}" class="inline-flex rounded-full border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-950 hover:text-slate-950">
                Volver al evento
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div
            x-data="{
                previewUrl: @js($event->invitationBackgroundUrl()),
                qrPosition: @js($selectedPosition),
                qrSize: {{ $selectedSize }},
                sourceWidth: 1080,
                sourceHeight: 1350,
                positionClasses: {
                    'left-top': 'top-8 left-8',
                    'center-top': 'top-8 left-1/2 -translate-x-1/2',
                    'right-top': 'top-8 right-8',
                    'left-middle': 'top-1/2 left-8 -translate-y-1/2',
                    'center-middle': 'top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2',
                    'right-middle': 'top-1/2 right-8 -translate-y-1/2',
                    'left-bottom': 'bottom-8 left-8',
                    'center-bottom': 'bottom-8 left-1/2 -translate-x-1/2',
                    'right-bottom': 'bottom-8 right-8',
                },
                setImageDimensions(event) {
                    if (! event.target.naturalWidth || ! event.target.naturalHeight) {
                        return;
                    }

                    this.sourceWidth = event.target.naturalWidth;
                    this.sourceHeight = event.target.naturalHeight;
                },
                previewFrameStyle() {
                    return `aspect-ratio:${this.sourceWidth}/${this.sourceHeight};`;
                },
                qrBoxStyle() {
                    const widthPercent = (this.qrSize / this.sourceWidth) * 100;
                    const marginXPercent = (32 / this.sourceWidth) * 100;
                    const marginYPercent = (32 / this.sourceHeight) * 100;
                    const [horizontal, vertical] = this.qrPosition.split('-');
                    const transforms = [];
                    const styles = [
                        `width:${widthPercent}%`,
                        'aspect-ratio:1/1',
                    ];

                    if (horizontal === 'left') {
                        styles.push(`left:${marginXPercent}%`);
                    } else if (horizontal === 'center') {
                        styles.push('left:50%');
                        transforms.push('translateX(-50%)');
                    } else {
                        styles.push(`right:${marginXPercent}%`);
                    }

                    if (vertical === 'top') {
                        styles.push(`top:${marginYPercent}%`);
                    } else if (vertical === 'middle') {
                        styles.push('top:50%');
                        transforms.push('translateY(-50%)');
                    } else {
                        styles.push(`bottom:${marginYPercent}%`);
                    }

                    if (transforms.length) {
                        styles.push(`transform:${transforms.join(' ')}`);
                    }

                    return styles.join(';');
                }
            }"
            class="mx-auto grid max-w-7xl gap-6 lg:grid-cols-[0.92fr_1.08fr] sm:px-6 lg:px-8"
        >
            <section class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm">
                @if (session('status'))
                    <div class="mb-6 rounded-[1.5rem] border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm text-emerald-700">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 rounded-[1.5rem] border border-rose-200 bg-rose-50 px-5 py-4 text-sm text-rose-700">
                        <ul class="list-disc ps-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="mb-6">
                    <p class="text-sm font-semibold uppercase tracking-[0.2em] text-amber-700">Configuracion</p>
                    <h3 class="mt-2 text-2xl font-semibold text-slate-950">Fondo, posicion y tamano del QR</h3>
                    <p class="mt-2 text-sm leading-7 text-slate-500">
                        La invitacion final solo mostrara la imagen de fondo y el codigo QR. Selecciona la ubicacion exacta y el tamano antes de guardar.
                    </p>
                </div>

                <form method="POST" action="{{ route('events.invitation-design.update', $event) }}" enctype="multipart/form-data" class="space-y-8">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="invitation_background" class="block text-sm font-medium text-slate-700">Imagen de fondo</label>
                        <input
                            id="invitation_background"
                            name="invitation_background"
                            type="file"
                            accept=".jpg,.jpeg,.png,.webp"
                            @change="
                                const [file] = $event.target.files;
                                if (file) {
                                    previewUrl = URL.createObjectURL(file);
                                }
                            "
                            class="mt-2 block w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 file:me-4 file:rounded-full file:border-0 file:bg-slate-950 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white"
                        >
                        <p class="mt-2 text-xs text-slate-500">Formatos permitidos: JPG, PNG, WEBP. Tamano maximo: 5 MB.</p>
                    </div>

                    @if ($event->invitation_background_path)
                        <label class="inline-flex items-center gap-3 text-sm text-slate-600">
                            <input type="checkbox" name="remove_invitation_background" value="1" @change="if ($event.target.checked) { previewUrl = null; sourceWidth = 1080; sourceHeight = 1350; }" class="size-4 rounded border-slate-300 text-amber-600 focus:ring-amber-500">
                            Quitar fondo actual
                        </label>
                    @endif

                    <div>
                        <div class="flex items-center justify-between gap-4">
                            <label class="block text-sm font-medium text-slate-700">Posicion del QR</label>
                            <span class="text-sm font-semibold text-slate-500" x-text="{
                                'left-top': 'Izquierda superior',
                                'center-top': 'Centro superior',
                                'right-top': 'Derecha superior',
                                'left-middle': 'Izquierda medio',
                                'center-middle': 'Centro medio',
                                'right-middle': 'Derecha medio',
                                'left-bottom': 'Izquierda inferior',
                                'center-bottom': 'Centro inferior',
                                'right-bottom': 'Derecha inferior',
                            }[qrPosition]"></span>
                        </div>
                        <input type="hidden" name="invitation_qr_position" :value="qrPosition">

                        <div class="mt-3 grid grid-cols-3 gap-3 rounded-[1.75rem] border border-slate-200 bg-slate-50 p-4">
                            @foreach ($qrPositions as $position)
                                <button
                                    type="button"
                                    @click="qrPosition = '{{ $position }}'"
                                    :class="qrPosition === '{{ $position }}' ? 'border-slate-950 bg-slate-950 text-white' : 'border-slate-200 bg-white text-slate-600 hover:border-slate-300'"
                                    class="flex h-20 flex-col items-center justify-center rounded-[1.25rem] border text-center text-xs font-semibold uppercase tracking-[0.14em] transition"
                                >
                                    <span>{{ $positionLabels[$position] }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between gap-4">
                            <label for="invitation_qr_size" class="block text-sm font-medium text-slate-700">Tamano del QR</label>
                            <span class="text-sm font-semibold text-slate-500"><span x-text="qrSize"></span> px</span>
                        </div>
                        <input type="hidden" name="invitation_qr_size" :value="qrSize">
                        <input
                            id="invitation_qr_size"
                            type="range"
                            min="80"
                            max="320"
                            step="10"
                            x-model="qrSize"
                            class="mt-4 w-full accent-slate-950"
                        >
                        <div class="mt-2 flex justify-between text-xs text-slate-400">
                            <span>80 px</span>
                            <span>320 px</span>
                        </div>
                    </div>

                    <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 p-5">
                        <p class="text-sm font-semibold uppercase tracking-[0.18em] text-amber-700">Miniatura del fondo</p>
                        <div class="mt-4 overflow-hidden rounded-[1.5rem] border border-slate-200 bg-white">
                            <template x-if="previewUrl">
                                <img :src="previewUrl" alt="Vista previa del fondo" class="h-64 w-full object-cover">
                            </template>
                            <template x-if="! previewUrl">
                                <div class="grid h-64 place-items-center bg-[radial-gradient(circle_at_top,_rgba(251,191,36,0.18),_transparent_30%),linear-gradient(180deg,_#fffdf7_0%,_#f8fafc_100%)] px-6 text-center text-sm text-slate-500">
                                    Sin fondo cargado. Sube una imagen para ubicar el QR sobre ella.
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <a href="{{ route('events.show', $event) }}" class="text-sm font-medium text-slate-500 transition hover:text-slate-950">
                            Cancelar
                        </a>
                        <button type="submit" class="inline-flex rounded-full bg-slate-950 px-6 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                            Guardar diseno
                        </button>
                    </div>
                </form>
            </section>

            <section class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm">
                <div class="mb-6">
                    <p class="text-sm font-semibold uppercase tracking-[0.2em] text-amber-700">Vista previa</p>
                    <h3 class="mt-2 text-2xl font-semibold text-slate-950">Solo fondo mas codigo QR</h3>
                </div>

                <div class="overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-[0_24px_60px_-30px_rgba(15,23,42,0.25)]">
                    <div class="relative min-h-[640px] w-full bg-slate-100" :style="previewFrameStyle()">
                        <template x-if="previewUrl">
                            <img :src="previewUrl" @load="setImageDimensions($event)" alt="Fondo de invitacion" class="absolute inset-0 h-full w-full object-cover">
                        </template>
                        <template x-if="! previewUrl">
                            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(251,191,36,0.18),_transparent_30%),linear-gradient(180deg,_#fffdf7_0%,_#f8fafc_100%)]"></div>
                        </template>

                        <div
                            class="absolute"
                            :style="qrBoxStyle()"
                        >
                            <div class="h-full w-full [&_svg]:h-full [&_svg]:w-full">
                                {!! $previewQrSvg !!}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-5 rounded-[1.5rem] border border-slate-200 bg-slate-50 px-5 py-4 text-sm text-slate-600">
                    Codigo de muestra: <span class="font-mono font-semibold text-slate-950">{{ $previewCode }}</span>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
