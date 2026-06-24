<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Invitaciones') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-[radial-gradient(circle_at_top,_rgba(251,191,36,0.16),_transparent_30%),linear-gradient(180deg,_#fffdf7_0%,_#f8fafc_100%)] text-slate-900">
        <div class="mx-auto flex min-h-screen max-w-7xl flex-col px-6 py-8 lg:px-10">
            <header class="flex items-center justify-between">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-3">
                    <span class="grid size-11 place-items-center rounded-2xl bg-slate-950 text-sm font-bold text-white">IV</span>
                    <span>
                        <span class="block text-sm font-semibold uppercase tracking-[0.3em] text-amber-700">Invitaciones</span>
                        <span class="block text-sm text-slate-500">Eventos y asistentes</span>
                    </span>
                </a>

                <nav class="flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="rounded-full border border-slate-300 px-5 py-2 text-sm font-semibold text-slate-700 transition hover:border-slate-950 hover:text-slate-950">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="rounded-full border border-slate-300 px-5 py-2 text-sm font-semibold text-slate-700 transition hover:border-slate-950 hover:text-slate-950">Ingresar</a>
                    @endauth
                </nav>
            </header>

            <main class="grid flex-1 items-center gap-16 py-16 lg:grid-cols-[1.05fr_0.95fr]">
                <section class="space-y-8">
                    <span class="inline-flex rounded-full border border-amber-300 bg-amber-100 px-4 py-1 text-xs font-semibold uppercase tracking-[0.24em] text-amber-800">
                        Organiza invitaciones sin friccion
                    </span>
                    <div class="space-y-5">
                        <h1 class="max-w-3xl text-5xl font-semibold tracking-tight text-slate-950 sm:text-6xl">
                            Crea eventos y carga invitados desde Excel o de forma manual.
                        </h1>
                        <p class="max-w-2xl text-lg leading-8 text-slate-600">
                            Administra nombre del evento, fecha, hora y lugar. Luego importa tu lista de invitados con nombre, apellidos, telefono y cantidad de invitaciones, o agrega personas una a una desde el panel.
                        </p>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-3">
                        <article class="rounded-[1.75rem] border border-slate-200 bg-white/90 p-5 shadow-sm">
                            <p class="text-sm font-semibold text-slate-950">Eventos claros</p>
                            <p class="mt-2 text-sm leading-6 text-slate-500">Cada usuario administra sus propios eventos con agenda y lugar definidos.</p>
                        </article>
                        <article class="rounded-[1.75rem] border border-slate-200 bg-white/90 p-5 shadow-sm">
                            <p class="text-sm font-semibold text-slate-950">Carga en lote</p>
                            <p class="mt-2 text-sm leading-6 text-slate-500">Descarga la plantilla, complétala en Excel y sube tus invitados en minutos.</p>
                        </article>
                        <article class="rounded-[1.75rem] border border-slate-200 bg-white/90 p-5 shadow-sm">
                            <p class="text-sm font-semibold text-slate-950">Panel personal</p>
                            <p class="mt-2 text-sm leading-6 text-slate-500">Consulta totales de invitados e invitaciones desde un dashboard inicial.</p>
                        </article>
                    </div>
                </section>

                <section class="relative">
                    <div class="absolute -left-8 top-10 h-44 w-44 rounded-full bg-amber-200/60 blur-3xl"></div>
                    <div class="absolute -bottom-12 right-0 h-44 w-44 rounded-full bg-sky-200/50 blur-3xl"></div>

                    <div class="relative overflow-hidden rounded-[2rem] border border-slate-200 bg-slate-950 p-8 text-white shadow-[0_30px_80px_-40px_rgba(15,23,42,0.75)]">
                        <div class="mb-8 flex items-center justify-between">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-amber-300">Vista previa</p>
                                <h2 class="mt-2 text-2xl font-semibold">Tu centro de operaciones</h2>
                            </div>
                            <span class="rounded-full bg-white/10 px-4 py-2 text-xs font-medium text-white/80">Beta inicial</span>
                        </div>

                        <div class="space-y-4">
                            <div class="rounded-[1.5rem] bg-white/8 p-5 ring-1 ring-white/10">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm text-white/60">Proximo evento</p>
                                        <p class="mt-1 text-xl font-semibold">Cena de gala 2026</p>
                                    </div>
                                    <div class="rounded-2xl bg-amber-400 px-4 py-3 text-right text-slate-950">
                                        <p class="text-xs font-semibold uppercase tracking-[0.18em]">25 julio</p>
                                        <p class="text-sm">7:00 PM</p>
                                    </div>
                                </div>
                            </div>

                            <div class="grid gap-4 sm:grid-cols-2">
                                <div class="rounded-[1.5rem] bg-white/8 p-5 ring-1 ring-white/10">
                                    <p class="text-sm text-white/60">Invitados cargados</p>
                                    <p class="mt-3 text-3xl font-semibold">148</p>
                                </div>
                                <div class="rounded-[1.5rem] bg-white/8 p-5 ring-1 ring-white/10">
                                    <p class="text-sm text-white/60">Invitaciones</p>
                                    <p class="mt-3 text-3xl font-semibold">312</p>
                                </div>
                            </div>

                            <div class="rounded-[1.5rem] bg-white/8 p-5 ring-1 ring-white/10">
                                <div class="mb-4 flex items-center justify-between">
                                    <p class="font-medium">Importacion masiva</p>
                                    <span class="rounded-full bg-emerald-400/20 px-3 py-1 text-xs font-semibold text-emerald-300">Lista para subir</span>
                                </div>
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between rounded-2xl bg-white/6 px-4 py-3 text-sm">
                                        <span>plantilla-invitados.xlsx</span>
                                        <span class="text-white/60">4 columnas</span>
                                    </div>
                                    <div class="h-2 overflow-hidden rounded-full bg-white/10">
                                        <div class="h-full w-3/4 rounded-full bg-amber-400"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </main>
        </div>
    </body>
</html>
