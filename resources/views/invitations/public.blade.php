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
        <div class="mx-auto max-w-5xl px-4 py-5 sm:px-6 sm:py-8 lg:px-8">
            <div class="mb-4 flex flex-col gap-3 sm:mb-5 sm:flex-row sm:flex-wrap sm:items-center sm:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-amber-700">Invitacion digital</p>
                    <h1 class="mt-2 text-2xl font-semibold leading-tight text-slate-950 sm:text-3xl">{{ $event->name }}</h1>
                </div>
                <a href="{{ $invitation->imageUrl() }}" download="invitacion-{{ $invitation->code }}.png" class="inline-flex w-full items-center justify-center rounded-full bg-slate-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 sm:w-auto">
                    Descargar invitación
                </a>
            </div>

            <div class="overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-[0_24px_60px_-30px_rgba(15,23,42,0.35)]">
                <img src="{{ $invitation->imageUrl() }}" alt="Invitacion de {{ $guest->full_name }}" class="block h-auto w-full">
            </div>

            <div class="mt-4 rounded-[1.5rem] border border-slate-200 bg-white px-4 py-3 text-sm text-slate-600 sm:mt-5 sm:px-5 sm:py-4">
                Esta invitacion es valida para un invitado. Presenta esta imagen al ingreso del evento.
            </div>

            <div class="mt-4 grid gap-3 sm:grid-cols-2 sm:gap-4">
                <div class="rounded-[1.5rem] border border-slate-200 bg-white px-4 py-4 sm:px-5">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Invitado por: </p>
                    <p class="mt-2 text-lg font-semibold text-slate-950">{{ $guest->full_name }}</p>
                </div>
                <div class="rounded-[1.5rem] border border-slate-200 bg-white px-4 py-4 sm:px-5">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Codigo unico</p>
                    <p class="mt-2 break-all font-mono text-base font-semibold tracking-[0.12em] text-slate-950 sm:text-lg">{{ $invitation->code }}</p>
                </div>
            </div>
        </div>
    </body>
</html>
