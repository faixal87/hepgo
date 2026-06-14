<?php

namespace App\Models;

use App\Enums\UserStatus;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password', 'phone', 'profile_photo_path', 'status', 'last_login_at'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, HasRoles, Notifiable, SoftDeletes;

    public function canAccessPanel(Panel $panel): bool
    {
        return $panel->getId() === 'admin'
            && $this->status === UserStatus::ACTIVE
            && $this->hasAnyRole(config('hep.admin_panel_roles'));
    }

    public function profilePhotoUrl(): ?string
    {
        if (blank($this->profile_photo_path)) {
            return null;
        }

        return Storage::disk('public')->url($this->profile_photo_path);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'status' => UserStatus::class,
            'password' => 'hashed',
        ];
    }

    public function createdOwners(): HasMany
    {
        return $this->hasMany(Owner::class, 'created_by');
    }

    public function verifiedOwners(): HasMany
    {
        return $this->hasMany(Owner::class, 'verified_by');
    }

    public function createdProperties(): HasMany
    {
        return $this->hasMany(Property::class, 'created_by');
    }

    public function verifiedProperties(): HasMany
    {
        return $this->hasMany(Property::class, 'verified_by');
    }

    public function propertyStatusLogs(): HasMany
    {
        return $this->hasMany(PropertyStatusLog::class, 'changed_by');
    }

    public function handledPropertyReports(): HasMany
    {
        return $this->hasMany(PropertyReport::class, 'handled_by');
    }

    public function propertyBookmarks(): HasMany
    {
        return $this->hasMany(PropertyBookmark::class);
    }
}
