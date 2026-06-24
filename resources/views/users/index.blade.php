<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.2em] text-amber-700">Administracion</p>
                <h2 class="mt-1 text-2xl font-semibold text-slate-950">Usuarios y roles</h2>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <span class="inline-flex rounded-full bg-slate-950 px-4 py-2 text-sm font-semibold text-white">
                    {{ $users->count() }} usuarios
                </span>
                <button type="button" onclick="window.dispatchEvent(new CustomEvent('open-create-user'))" class="inline-flex rounded-full bg-amber-400 px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-amber-300">
                    Nuevo usuario
                </button>
            </div>
        </div>
    </x-slot>

    <div
        class="py-10"
        x-data="userAdmin()"
        x-init="init()"
        @open-create-user.window="openCreateModal()"
        @keydown.escape.window="closeModals()"
    >
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
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
                    <h3 class="text-xl font-semibold text-slate-950">Usuarios registrados</h3>
                    <p class="mt-1 text-sm text-slate-500">Crea cuentas nuevas y edita nombre, usuario, correo, rol y contrasena desde este modulo.</p>
                </div>

                <div class="overflow-hidden rounded-[1.5rem] border border-slate-200">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr class="text-left text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">
                                    <th class="px-4 py-4 sm:px-5">Nombre</th>
                                    <th class="px-4 py-4 sm:px-5">Usuario</th>
                                    <th class="px-4 py-4 sm:px-5">Correo</th>
                                    <th class="px-4 py-4 sm:px-5">Rol</th>
                                    <th class="px-4 py-4 text-right sm:px-5">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white">
                                @foreach ($users as $managedUser)
                                    <tr class="align-top text-sm text-slate-600">
                                        <td class="px-4 py-4 sm:px-5">
                                            <p class="font-semibold text-slate-950">{{ $managedUser->name }}</p>
                                        </td>
                                        <td class="px-4 py-4 sm:px-5">
                                            <span class="font-medium text-slate-700">{{ '@'.$managedUser->username }}</span>
                                        </td>
                                        <td class="px-4 py-4 sm:px-5">
                                            <span class="break-all">{{ $managedUser->email }}</span>
                                        </td>
                                        <td class="px-4 py-4 sm:px-5">
                                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $managedUser->role === 'admin' ? 'bg-slate-950 text-white' : ($managedUser->role === 'validator' ? 'bg-amber-100 text-amber-800' : 'bg-sky-100 text-sky-800') }}">
                                                {{ $managedUser->role === 'admin' ? 'Admin' : ($managedUser->role === 'validator' ? 'Validator' : 'Organizer') }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 text-right sm:px-5">
                                            <div class="flex justify-end gap-2">
                                                <button
                                                    type="button"
                                                    @click="openEditModal({{ $managedUser->id }})"
                                                    class="inline-flex rounded-full border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-slate-950 hover:text-slate-950"
                                                >
                                                    Editar
                                                </button>
                                                <button
                                                    type="button"
                                                    @click="openDeleteModal({{ $managedUser->id }})"
                                                    class="inline-flex rounded-full border border-rose-300 px-4 py-2 text-sm font-semibold text-rose-700 transition hover:border-rose-500 hover:bg-rose-50"
                                                >
                                                    Eliminar
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>

        <div x-cloak x-show="createModalOpen" x-transition.opacity class="fixed inset-0 z-50 flex items-end bg-slate-950/70 p-3 sm:items-center sm:justify-center sm:p-6">
            <div x-show="createModalOpen" x-transition class="w-full max-w-3xl overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-[0_24px_80px_-20px_rgba(15,23,42,0.45)]">
                <div class="flex items-start justify-between gap-4 border-b border-slate-200 px-5 py-4 sm:px-6">
                    <div>
                        <p class="text-sm font-medium uppercase tracking-[0.2em] text-amber-700">Usuarios</p>
                        <h3 class="mt-1 text-xl font-semibold text-slate-950">Registrar usuario</h3>
                    </div>
                    <button type="button" @click="closeModals()" class="inline-flex size-10 items-center justify-center rounded-full border border-slate-200 text-lg text-slate-500 transition hover:bg-slate-100 hover:text-slate-950">×</button>
                </div>

                <form method="POST" action="{{ route('users.store') }}" class="space-y-5 px-5 py-5 sm:px-6">
                    @csrf
                    <input type="hidden" name="_user_form" value="create">

                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label for="create_name" class="block text-sm font-medium text-slate-700">Nombre <span class="text-rose-600">*</span></label>
                            <input id="create_name" name="name" type="text" x-model="createForm.name" required class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-100">
                        </div>
                        <div>
                            <label for="create_username" class="block text-sm font-medium text-slate-700">Usuario <span class="text-rose-600">*</span></label>
                            <input id="create_username" name="username" type="text" x-model="createForm.username" required class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-100">
                        </div>
                        <div>
                            <label for="create_email" class="block text-sm font-medium text-slate-700">Correo</label>
                            <input id="create_email" name="email" type="email" x-model="createForm.email" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-100">
                        </div>
                        <div>
                            <label for="create_role" class="block text-sm font-medium text-slate-700">Rol <span class="text-rose-600">*</span></label>
                            <select id="create_role" name="role" x-model="createForm.role" required class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-100">
                                <option value="admin">Admin</option>
                                <option value="organizer">Organizer</option>
                                <option value="validator">Validator</option>
                            </select>
                        </div>
                        <div>
                            <label for="create_password" class="block text-sm font-medium text-slate-700">Contrasena <span class="text-rose-600">*</span></label>
                            <input id="create_password" name="password" type="password" required class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-100">
                        </div>
                        <div>
                            <label for="create_password_confirmation" class="block text-sm font-medium text-slate-700">Confirmar contrasena <span class="text-rose-600">*</span></label>
                            <input id="create_password_confirmation" name="password_confirmation" type="password" required class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-100">
                        </div>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                        <button type="button" @click="closeModals()" class="inline-flex items-center justify-center rounded-full border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-950 hover:text-slate-950">
                            Cancelar
                        </button>
                        <button type="submit" class="inline-flex items-center justify-center rounded-full bg-slate-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                            Crear usuario
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div x-cloak x-show="editModalOpen" x-transition.opacity class="fixed inset-0 z-50 flex items-end bg-slate-950/70 p-3 sm:items-center sm:justify-center sm:p-6">
            <div x-show="editModalOpen" x-transition class="w-full max-w-3xl overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-[0_24px_80px_-20px_rgba(15,23,42,0.45)]">
                <div class="flex items-start justify-between gap-4 border-b border-slate-200 px-5 py-4 sm:px-6">
                    <div>
                        <p class="text-sm font-medium uppercase tracking-[0.2em] text-amber-700">Usuarios</p>
                        <h3 class="mt-1 text-xl font-semibold text-slate-950">Editar usuario</h3>
                    </div>
                    <button type="button" @click="closeModals()" class="inline-flex size-10 items-center justify-center rounded-full border border-slate-200 text-lg text-slate-500 transition hover:bg-slate-100 hover:text-slate-950">×</button>
                </div>

                <form method="POST" :action="editAction()" class="space-y-5 px-5 py-5 sm:px-6">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="_user_form" value="edit">
                    <input type="hidden" name="edit_user_id" :value="editForm.id">

                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label for="edit_name" class="block text-sm font-medium text-slate-700">Nombre <span class="text-rose-600">*</span></label>
                            <input id="edit_name" name="name" type="text" x-model="editForm.name" required class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-100">
                        </div>
                        <div>
                            <label for="edit_username" class="block text-sm font-medium text-slate-700">Usuario <span class="text-rose-600">*</span></label>
                            <input id="edit_username" name="username" type="text" x-model="editForm.username" required class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-100">
                        </div>
                        <div>
                            <label for="edit_email" class="block text-sm font-medium text-slate-700">Correo</label>
                            <input id="edit_email" name="email" type="email" x-model="editForm.email" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-100">
                        </div>
                        <div>
                            <label for="edit_role" class="block text-sm font-medium text-slate-700">Rol <span class="text-rose-600">*</span></label>
                            <select id="edit_role" name="role" x-model="editForm.role" required class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-100">
                                <option value="admin">Admin</option>
                                <option value="organizer">Organizer</option>
                                <option value="validator">Validator</option>
                            </select>
                        </div>
                        <div>
                            <label for="edit_password" class="block text-sm font-medium text-slate-700">Nueva contrasena</label>
                            <input id="edit_password" name="password" type="password" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-100">
                            <p class="mt-2 text-xs text-slate-500">Deja este campo vacio si no deseas cambiarla.</p>
                        </div>
                        <div>
                            <label for="edit_password_confirmation" class="block text-sm font-medium text-slate-700">Confirmar contrasena</label>
                            <input id="edit_password_confirmation" name="password_confirmation" type="password" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-100">
                        </div>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <button
                            type="button"
                            @click="openDeleteModal(editForm.id)"
                            class="inline-flex items-center justify-center rounded-full border border-rose-300 px-5 py-3 text-sm font-semibold text-rose-700 transition hover:border-rose-500 hover:bg-rose-50"
                        >
                            Eliminar usuario
                        </button>
                        <div class="flex flex-col gap-3 sm:flex-row">
                            <button type="button" @click="closeModals()" class="inline-flex items-center justify-center rounded-full border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-950 hover:text-slate-950">
                                Cancelar
                            </button>
                            <button type="submit" class="inline-flex items-center justify-center rounded-full bg-slate-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                                Guardar cambios
                            </button>
                        </div>
                    </div>
                </form>

                <form x-ref="deleteForm" method="POST" :action="deleteAction()" class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>

        <div x-cloak x-show="deleteModalOpen" x-transition.opacity class="fixed inset-0 z-50 flex items-end bg-slate-950/70 p-3 sm:items-center sm:justify-center sm:p-6">
            <div x-show="deleteModalOpen" x-transition class="w-full max-w-xl overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-[0_24px_80px_-20px_rgba(15,23,42,0.45)]">
                <div class="border-b border-slate-200 px-5 py-4 sm:px-6">
                    <p class="text-sm font-medium uppercase tracking-[0.2em] text-rose-700">Confirmacion</p>
                    <h3 class="mt-1 text-xl font-semibold text-slate-950">Desactivar usuario</h3>
                </div>

                <div class="space-y-4 px-5 py-5 sm:px-6">
                    <p class="text-sm leading-7 text-slate-600">
                        Se desactivara el usuario
                        <span class="font-semibold text-slate-950" x-text="deleteTarget.name || 'seleccionado'"></span>.
                        Sus eventos e historial se conservaran mediante borrado suave.
                    </p>

                    <div class="rounded-[1.5rem] border border-amber-200 bg-amber-50 px-4 py-4 text-sm text-amber-800">
                        Esta accion ocultara el usuario del listado activo, pero no eliminara fisicamente sus datos.
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                        <button type="button" @click="closeDeleteModal()" class="inline-flex items-center justify-center rounded-full border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-950 hover:text-slate-950">
                            Cancelar
                        </button>
                        <button type="button" @click="submitDelete()" class="inline-flex items-center justify-center rounded-full bg-rose-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-rose-500">
                            Confirmar eliminacion
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function userAdmin() {
            return {
                users: @js($users->map(fn ($user) => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'email' => $user->email,
                    'role' => $user->role,
                ])->values()),
                createModalOpen: @js(old('_user_form') === 'create'),
                editModalOpen: @js(old('_user_form') === 'edit'),
                deleteModalOpen: false,
                createForm: {
                    name: @js(old('name', '')),
                    username: @js(old('username', '')),
                    email: @js(old('email', '')),
                    role: @js(old('role', 'organizer')),
                },
                editForm: {
                    id: @js(old('edit_user_id')),
                    name: @js(old('name', '')),
                    username: @js(old('username', '')),
                    email: @js(old('email', '')),
                    role: @js(old('role', 'organizer')),
                },
                deleteTarget: {
                    id: null,
                    name: '',
                },
                init() {
                    if (this.editModalOpen && this.editForm.id) {
                        this.openEditModal(Number(this.editForm.id), true);
                    }
                },
                openCreateModal() {
                    this.closeModals();
                    this.createModalOpen = true;
                },
                openEditModal(userId, preserveOldValues = false) {
                    const user = this.users.find((item) => Number(item.id) === Number(userId));

                    if (! user) {
                        return;
                    }

                    this.closeModals();
                    this.editModalOpen = true;
                    this.editForm.id = user.id;
                    this.editForm.name = preserveOldValues ? this.editForm.name : user.name;
                    this.editForm.username = preserveOldValues ? this.editForm.username : user.username;
                    this.editForm.email = preserveOldValues ? this.editForm.email : user.email;
                    this.editForm.role = preserveOldValues ? this.editForm.role : user.role;
                },
                openDeleteModal(userId) {
                    const user = this.users.find((item) => Number(item.id) === Number(userId));

                    if (! user) {
                        return;
                    }

                    this.closeModals();
                    this.deleteModalOpen = true;
                    this.deleteTarget.id = user.id;
                    this.deleteTarget.name = user.name;
                },
                closeDeleteModal() {
                    this.deleteModalOpen = false;
                    this.deleteTarget.id = null;
                    this.deleteTarget.name = '';
                },
                closeModals() {
                    this.createModalOpen = false;
                    this.editModalOpen = false;
                    this.closeDeleteModal();
                },
                editAction() {
                    return @js(route('users.update', ['user' => '__USER__'])).replace('__USER__', this.editForm.id ?? '');
                },
                deleteAction() {
                    return @js(route('users.destroy', ['user' => '__USER__'])).replace('__USER__', this.deleteTarget.id ?? '');
                },
                submitDelete() {
                    if (! this.deleteTarget.id) {
                        return;
                    }

                    this.$refs.deleteForm.submit();
                },
            };
        }
    </script>
</x-app-layout>
