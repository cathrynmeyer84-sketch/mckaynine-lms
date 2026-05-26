<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password',
        'is_admin', 'is_super_admin', 'is_instructor', 'is_handler',
        'profile_photo_path', 'access_expires_at', 'is_active',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'access_expires_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'is_super_admin' => 'boolean',
            'is_instructor' => 'boolean',
            'is_handler' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function handler()
    {
        return $this->hasOne(Handler::class);
    }

    public function instructor()
    {
        return $this->hasOne(Instructor::class);
    }

    public function appNotifications()
    {
        return $this->hasMany(AppNotification::class)->latest();
    }

    public function unreadNotificationsCount()
    {
        return $this->appNotifications()->where('is_read', false)->count();
    }

    public function newAchievementsCount()
    {
        return $this->appNotifications()
            ->where('type', 'achievement')
            ->where('is_read', false)
            ->count();
    }

    public function isAdmin(): bool
    {
        return $this->is_admin;
    }

    public function isSuperAdmin(): bool
    {
        return $this->is_super_admin;
    }

    public function adminLevel(): string
    {
        if ($this->is_super_admin) return 'Super Admin';
        if ($this->is_admin) return 'Admin';
        return '';
    }

    public function isInstructor(): bool
    {
        return $this->is_instructor;
    }

    public function isHandler(): bool
    {
        return $this->is_handler;
    }

    public function getDisplayNameAttribute(): string
    {
        if ($this->handler) {
            return $this->handler->first_name . ' ' . $this->handler->last_name;
        }
        if ($this->instructor) {
            return $this->instructor->first_name . ' ' . $this->instructor->last_name;
        }
        return $this->name;
    }

    /**
     * Override reset notification to append ?setup=1 when this is a
     * first-time account activation (prefill_email session flag is set).
     */
    public function sendPasswordResetNotification($token): void
    {
        if (session('prefill_email') === $this->email) {
            // Append ?setup=1 so the reset page shows "Create Password" copy
            \Illuminate\Auth\Notifications\ResetPassword::createUrlUsing(
                fn($user, $token) => url(route('password.reset', [
                    'token' => $token,
                    'email' => $user->email,
                    'setup' => '1',
                ], false))
            );
            parent::sendPasswordResetNotification($token);
            \Illuminate\Auth\Notifications\ResetPassword::createUrlUsing(null); // restore default
        } else {
            parent::sendPasswordResetNotification($token);
        }
    }
}
