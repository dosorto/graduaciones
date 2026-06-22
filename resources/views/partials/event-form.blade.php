@php
    $event = $event ?? null;
@endphp

<div class="grid gap-6 md:grid-cols-2">
    <div class="md:col-span-2">
        <label for="name" class="block text-sm font-medium text-slate-700">Nombre del evento</label>
        <input
            id="name"
            name="name"
            type="text"
            value="{{ old('name', $event?->name) }}"
            required
            class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-100"
        >
        @error('name')
            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="event_date" class="block text-sm font-medium text-slate-700">Fecha</label>
        <input
            id="event_date"
            name="event_date"
            type="date"
            value="{{ old('event_date', $event?->event_date?->format('Y-m-d')) }}"
            required
            class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-100"
        >
        @error('event_date')
            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="event_time" class="block text-sm font-medium text-slate-700">Hora</label>
        <input
            id="event_time"
            name="event_time"
            type="time"
            value="{{ old('event_time', $event?->event_time?->format('H:i')) }}"
            required
            class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-100"
        >
        @error('event_time')
            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="md:col-span-2">
        <label for="venue" class="block text-sm font-medium text-slate-700">Lugar</label>
        <input
            id="venue"
            name="venue"
            type="text"
            value="{{ old('venue', $event?->venue) }}"
            required
            class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-100"
        >
        @error('venue')
            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="md:col-span-2">
        <label for="description" class="block text-sm font-medium text-slate-700">Descripcion</label>
        <textarea
            id="description"
            name="description"
            rows="5"
            class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-100"
        >{{ old('description', $event?->description) }}</textarea>
        @error('description')
            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
        @enderror
    </div>
</div>
