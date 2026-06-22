<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    public function index(Request $request): View
    {
        return view('users.index', [
            'users' => User::query()->orderBy('name')->get(),
        ]);
    }

    public function updateRole(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'role' => ['required', Rule::in(['admin', 'organizer', 'validator'])],
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

        $user->update(['role' => $validated['role']]);

        return back()->with('status', 'Rol actualizado correctamente.');
    }
}
