<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    /**
     * Validate and update the given user's profile information.
     *
     * @param  array<string, mixed>  $input
     */
    public function update(User $user, array $input): void
    {
        $username = $this->resolveUsername(
            $input['username'] ?? $user->username,
            $input['name'] ?? $user->name,
            $user,
        );

        Validator::make([...$input, 'username' => $username], [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'min:3', 'max:50', 'regex:/^[a-zA-Z0-9._-]+$/', Rule::unique('users', 'username')->ignore($user->id)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'photo' => ['nullable', 'mimes:jpg,jpeg,png', 'max:1024'],
        ])->validateWithBag('updateProfileInformation');

        if (isset($input['photo'])) {
            $user->updateProfilePhoto($input['photo']);
        }

        if ($input['email'] !== $user->email &&
            $user instanceof MustVerifyEmail) {
            $this->updateVerifiedUser($user, [...$input, 'username' => $username]);
        } else {
            $user->forceFill([
                'name' => $input['name'],
                'username' => $username,
                'email' => $input['email'],
            ])->save();
        }
    }

    /**
     * Update the given verified user's profile information.
     *
     * @param  array<string, string>  $input
     */
    protected function updateVerifiedUser(User $user, array $input): void
    {
        $user->forceFill([
            'name' => $input['name'],
            'username' => $input['username'],
            'email' => $input['email'],
            'email_verified_at' => null,
        ])->save();

        $user->sendEmailVerificationNotification();
    }

    private function resolveUsername(?string $username, ?string $name, User $user): string
    {
        $candidate = Str::of($username ?: $name ?: 'usuario')
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

        while (User::where('username', $resolved)->whereKeyNot($user->id)->exists()) {
            $suffixText = '.'.$suffix;
            $resolved = Str::limit($base, 50 - strlen($suffixText), '').$suffixText;
            $suffix++;
        }

        return $resolved;
    }
}
