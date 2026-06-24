<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    public function index(Request $request): View
    {
        return view('users.index', [
            'users' => User::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'min:3', 'max:50', 'regex:/^[a-zA-Z0-9._-]+$/', Rule::unique('users', 'username')],
            'email' => ['nullable', 'string', 'email', 'max:255', 'unique:users'],
            'role' => ['required', Rule::in(['admin', 'organizer', 'validator'])],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'] ?: null,
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('status', 'Usuario creado correctamente.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'min:3', 'max:50', 'regex:/^[a-zA-Z0-9._-]+$/', Rule::unique('users', 'username')->ignore($user->id)],
            'email' => ['nullable', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['required', Rule::in(['admin', 'organizer', 'validator'])],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        if ($request->user()->id === $user->id && $validated['role'] !== 'admin') {
            return back()->withErrors([
                'role' => 'No puedes retirarte a ti mismo el rol de administrador.',
            ]);
        }

        $adminCount = User::where('role', 'admin')->count();

        if ($user->role === 'admin' && $validated['role'] !== 'admin' && $adminCount <= 1) {
            return back()->withErrors([
                'role' => 'Debe existir al menos un administrador en el sistema.',
            ]);
        }

        $user->update([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'] ?: null,
            'role' => $validated['role'],
            'password' => filled($validated['password'] ?? null) ? Hash::make($validated['password']) : $user->password,
        ]);

        return back()->with('status', 'Usuario actualizado correctamente.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($request->user()->id === $user->id) {
            return back()->withErrors([
                'user' => 'No puedes eliminar tu propio usuario desde este modulo.',
            ]);
        }

        $adminCount = User::where('role', 'admin')->count();

        if ($user->role === 'admin' && $adminCount <= 1) {
            return back()->withErrors([
                'user' => 'No puedes eliminar al ultimo administrador del sistema.',
            ]);
        }

        $user->delete();

        return back()->with('status', 'Usuario desactivado correctamente. Sus eventos se conservan en el sistema.');
    }
}
