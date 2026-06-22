@php
    $positionClasses = [
        'left-top' => 'top-8 left-8',
        'center-top' => 'top-8 left-1/2 -translate-x-1/2',
        'right-top' => 'top-8 right-8',
        'left-middle' => 'top-1/2 left-8 -translate-y-1/2',
        'center-middle' => 'top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2',
        'right-middle' => 'top-1/2 right-8 -translate-y-1/2',
        'left-bottom' => 'bottom-8 left-8',
        'center-bottom' => 'bottom-8 left-1/2 -translate-x-1/2',
        'right-bottom' => 'bottom-8 right-8',
    ];

    $qrPositionClass = $positionClasses[$event->invitationQrPosition()] ?? $positionClasses['right-bottom'];
    $qrSize = $event->invitationQrSize();
@endphp

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $event->name }} | Invitacion</title>
        <meta name="description" content="Invitacion digital para {{ $guest->full_name }} en {{ $event->name }}.">
        <meta property="og:title" content="{{ $event->name }} | Invitacion digital">
        <meta property="og:description" content="Invitacion digital para {{ $guest->full_name }}. Abre el enlace para ver y descargar la invitacion.">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ $invitation->publicUrl() }}">
        <meta property="og:image" content="{{ $invitation->imageUrl() }}">
        <meta property="og:image:secure_url" content="{{ $invitation->imageUrl() }}">
        <meta property="og:image:type" content="image/png">
        <meta property="og:image:alt" content="Invitacion digital con codigo QR para {{ $guest->full_name }}">
        <meta property="og:site_name" content="Plataforma de invitaciones">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $event->name }} | Invitacion digital">
        <meta name="twitter:description" content="Invitacion digital para {{ $guest->full_name }}.">
        <meta name="twitter:image" content="{{ $invitation->imageUrl() }}">
        @vite(['resources/css/app.css'])
    </head>
    <body class="min-h-screen bg-[radial-gradient(circle_at_top,_rgba(251,191,36,0.16),_transparent_28%),linear-gradient(180deg,_#fffdf7_0%,_#f8fafc_100%)] text-slate-900">
        <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-amber-700">Invitacion digital</p>
                    <h1 class="mt-2 text-2xl font-semibold text-slate-950 sm:text-3xl">{{ $event->name }}</h1>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ $invitation->imageUrl() }}" target="_blank" rel="noopener noreferrer" class="inline-flex rounded-full border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-950 hover:text-slate-950">
                        Abrir PNG
                    </a>
                    <button type="button" onclick="window.print()" class="inline-flex rounded-full bg-slate-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                        Descargar o imprimir
                    </button>
                </div>
            </div>

            <div class="overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-[0_24px_60px_-30px_rgba(15,23,42,0.35)]">
                <div class="relative aspect-[4/5] min-h-[760px]">
                    @if ($event->invitationBackgroundUrl())
                        <img src="{{ $event->invitationBackgroundUrl() }}" alt="Fondo de invitacion" class="absolute inset-0 h-full w-full object-cover">
                    @else
                        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(251,191,36,0.18),_transparent_30%),linear-gradient(180deg,_#fffdf7_0%,_#f8fafc_100%)]"></div>
                    @endif

                    <div class="absolute {{ $qrPositionClass }}" style="width: {{ $qrSize }}px; height: {{ $qrSize }}px;">
                        <div class="h-full w-full [&_svg]:h-full [&_svg]:w-full">
                            {!! $qrSvg !!}
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-5 rounded-[1.5rem] border border-slate-200 bg-white px-5 py-4 text-sm text-slate-600">
                Esta invitacion es valida para un invitado. Presenta esta imagen al ingreso del evento.
            </div>

            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <div class="rounded-[1.5rem] border border-slate-200 bg-white px-5 py-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Invitado</p>
                    <p class="mt-2 text-lg font-semibold text-slate-950">{{ $guest->full_name }}</p>
                </div>
                <div class="rounded-[1.5rem] border border-slate-200 bg-white px-5 py-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Codigo unico</p>
                    <p class="mt-2 font-mono text-lg font-semibold tracking-[0.12em] text-slate-950">{{ $invitation->code }}</p>
                </div>
            </div>
        </div>
    </body>
</html>
