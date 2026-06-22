<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium uppercase tracking-[0.2em] text-amber-700">Perfil</p>
            <h2 class="mt-1 text-2xl font-semibold text-slate-950">Configuracion de cuenta</h2>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            @if (Laravel\Fortify\Features::canUpdateProfileInformation())
                <div class="rounded-[2rem] border border-slate-200 bg-white p-2 shadow-sm">
                    @livewire('profile.update-profile-information-form')
                </div>
            @endif

            @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
                <div class="rounded-[2rem] border border-slate-200 bg-white p-2 shadow-sm">
                    @livewire('profile.update-password-form')
                </div>
            @endif

            @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
                <div class="rounded-[2rem] border border-slate-200 bg-white p-2 shadow-sm">
                    @livewire('profile.two-factor-authentication-form')
                </div>
            @endif

            <div class="rounded-[2rem] border border-slate-200 bg-white p-2 shadow-sm">
                @livewire('profile.logout-other-browser-sessions-form')
            </div>

            @if (Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
                <div class="rounded-[2rem] border border-rose-200 bg-white p-2 shadow-sm">
                    @livewire('profile.delete-user-form')
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
