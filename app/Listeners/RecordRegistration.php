<?php

namespace App\Listeners;

use App\Enums\ActivityEvent;
use App\Filament\Resources\Users\UserResource;
use App\Models\ActivityLog;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Auth\Events\Registered;

/**
 * Logs a new account and tells every admin through the bell in the admin
 * panel. Sent immediately rather than queued, so it also arrives on
 * installs that run no queue worker.
 */
class RecordRegistration
{
    public function handle(Registered $event): void
    {
        $user = $event->user;

        if (! $user instanceof User) {
            return;
        }

        ActivityLog::record($user, ActivityEvent::Registered);

        $notification = Notification::make()
            ->title("Pengguna baru: {$user->name}")
            ->body($user->email)
            ->actions([
                Action::make('view')
                    ->label('Lihat pengguna')
                    ->url(UserResource::getUrl('edit', ['record' => $user], panel: 'admin'))
                    ->markAsRead(),
            ]);

        User::query()
            ->where('is_admin', true)
            ->whereKeyNot($user->getKey())
            ->each(fn (User $admin) => $admin->notifyNow($notification->toDatabase()));
    }
}
