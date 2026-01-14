<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\NotificationPreference;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Collection;

class NotificationService
{
    /**
     * Send notification to a user
     */
    public function sendToUser(User $user, array $data): Notification
    {
        // Create notification
        $notification = Notification::create([
            'type' => $data['type'],
            'title' => $data['title'],
            'message' => $data['message'],
            'action_url' => $data['action_url'] ?? null,
            'relatable_type' => $data['relatable_type'] ?? null,
            'relatable_id' => $data['relatable_id'] ?? null,
            'data' => $data['data'] ?? null,
        ]);
        
        // Attach to user
        $notification->users()->attach($user->id);
        
        // Send through enabled channels
        $this->sendThroughChannels($user, $notification, $data);
        
        return $notification->fresh('users');
    }

    /**
     * Send notification to multiple users
     */
    public function sendToUsers(Collection $users, array $data): Notification
    {
        // Create notification
        $notification = Notification::create([
            'type' => $data['type'],
            'title' => $data['title'],
            'message' => $data['message'],
            'action_url' => $data['action_url'] ?? null,
            'relatable_type' => $data['relatable_type'] ?? null,
            'relatable_id' => $data['relatable_id'] ?? null,
            'data' => $data['data'] ?? null,
        ]);
        
        // Attach to users
        $notification->users()->attach($users->pluck('id'));
        
        // Send through enabled channels for each user
        foreach ($users as $user) {
            $this->sendThroughChannels($user, $notification, $data);
        }
        
        return $notification->fresh('users');
    }

    /**
     * Send notification through enabled channels
     */
    private function sendThroughChannels(User $user, Notification $notification, array $data): void
    {
        $preferences = $this->getUserPreferences($user, $notification->type);
        
        foreach ($preferences as $preference) {
            if (!$preference->isEnabled()) {
                continue;
            }
            
            match($preference->channel) {
                'email' => $this->sendEmailNotification($user, $notification, $data),
                'database' => null, // Already stored in database
                'push' => $this->sendPushNotification($user, $notification, $data),
                default => null,
            };
        }
    }

    /**
     * Send email notification
     */
    private function sendEmailNotification(User $user, Notification $notification, array $data): void
    {
        // TODO: Implement actual email sending using Mail facade
        // Mail::to($user->email)->send(new NotificationMail($notification));
    }

    /**
     * Send push notification
     */
    private function sendPushNotification(User $user, Notification $notification, array $data): void
    {
        // TODO: Implement actual push notification (Firebase, OneSignal, etc.)
    }

    /**
     * Get user notification preferences
     */
    private function getUserPreferences(User $user, string $notificationType): Collection
    {
        $preferences = NotificationPreference::where('user_id', $user->id)
            ->where('notification_type', $notificationType)
            ->get();
        
        // If no preferences set, use defaults (email and database enabled)
        if ($preferences->isEmpty()) {
            return collect([
                (object)['channel' => 'email', 'enabled' => true, 'frequency' => 'immediate'],
                (object)['channel' => 'database', 'enabled' => true, 'frequency' => 'immediate'],
            ]);
        }
        
        return $preferences;
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Notification $notification, User $user): void
    {
        $notification->users()
            ->updateExistingPivot($user->id, ['read_at' => now()]);
    }

    /**
     * Mark all notifications as read for user
     */
    public function markAllAsRead(User $user): void
    {
        Notification::whereHas('users', function ($query) use ($user) {
            $query->where('user_id', $user->id)
                  ->whereNull('notification_user.read_at');
        })->get()->each(function ($notification) use ($user) {
            $this->markAsRead($notification, $user);
        });
    }

    /**
     * Get unread count for user
     */
    public function getUnreadCount(User $user): int
    {
        return Notification::whereHas('users', function ($query) use ($user) {
            $query->where('user_id', $user->id)
                  ->whereNull('notification_user.read_at');
        })->count();
    }

    /**
     * Get recent notifications for user
     */
    public function getRecent(User $user, int $limit = 10)
    {
        return Notification::whereHas('users', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->with(['users' => function ($query) use ($user) {
            $query->where('user_id', $user->id);
        }])
        ->latest()
        ->limit($limit)
        ->get();
    }

    /**
     * Delete notification for user
     */
    public function delete(Notification $notification, User $user): void
    {
        $notification->users()->detach($user->id);
        
        // If no users left, delete the notification
        if ($notification->users()->count() === 0) {
            $notification->delete();
        }
    }

    /**
     * Update notification preferences
     */
    public function updatePreferences(User $user, string $notificationType, string $channel, array $data): NotificationPreference
    {
        return NotificationPreference::updateOrCreate(
            [
                'user_id' => $user->id,
                'notification_type' => $notificationType,
                'channel' => $channel,
            ],
            [
                'enabled' => $data['enabled'] ?? true,
                'frequency' => $data['frequency'] ?? 'immediate',
            ]
        );
    }

    /**
     * Get all preferences for user
     */
    public function getPreferences(User $user): Collection
    {
        return NotificationPreference::where('user_id', $user->id)->get();
    }
}
