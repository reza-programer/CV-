<?php

namespace App\Listeners;

use App\Enums\ActivityEvent;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Auth\Events\Login;

class RecordSignIn
{
    public function handle(Login $event): void
    {
        // Sign-up logs the new user in straight away; that is recorded as the registration.
        if ($event->user instanceof User && ! $event->user->wasRecentlyCreated) {
            ActivityLog::record($event->user, ActivityEvent::LoggedIn);
        }
    }
}
