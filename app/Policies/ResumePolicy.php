<?php

namespace App\Policies;

use App\Models\Resume;
use App\Models\User;

/**
 * A CV is private to its owner. Admins manage accounts and templates in the
 * admin panel but deliberately get no access to CV contents here.
 */
class ResumePolicy
{
    public function view(User $user, Resume $resume): bool
    {
        return $resume->user_id === $user->id;
    }

    public function update(User $user, Resume $resume): bool
    {
        return $resume->user_id === $user->id;
    }

    public function delete(User $user, Resume $resume): bool
    {
        return $resume->user_id === $user->id;
    }
}
