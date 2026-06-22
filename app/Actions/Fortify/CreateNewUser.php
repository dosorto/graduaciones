<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        $username = $this->resolveUsername(
            $input['username'] ?? null,
            $input['name'] ?? null,
            $input['email'] ?? null,
        );

        Validator::make([...$input, 'username' => $username], [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'min:3', 'max:50', 'regex:/^[a-zA-Z0-9._-]+$/', Rule::unique('users', 'username')],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ])->validate();

        return User::create([
            'name' => $input['name'],
            'username' => $username,
            'role' => 'organizer',
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);
    }

    private function resolveUsername(?string $username, ?string $name, ?string $email): string
    {
        $candidate = Str::of($username ?: $name ?: Str::before((string) $email, '@'))
            ->lower()
            ->slug('.')
            ->value();

        $candidate = trim($candidate, '.');

        if ($candidate === '') {
            $candidate = 'usuario';
        }

        if (strlen($candidate) < 3) {
            $candidate = str_pad($candidate, 3, '0');
        }

        $base = Str::limit($candidate, 50, '');
        $resolved = $base;
        $suffix = 1;

        while (User::where('username', $resolved)->exists()) {
            $suffixText = '.'.$suffix;
            $resolved = Str::limit($base, 50 - strlen($suffixText), '').$suffixText;
            $suffix++;
        }

        return $resolved;
    }
}
