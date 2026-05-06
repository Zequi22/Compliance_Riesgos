<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, HasName
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'last_name',
        'area',
        'team',
        'department',
        'job_title',
        'email',
        'organizational_unit_id',
        'is_active',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn () => "{$this->name} {$this->last_name}",
        );
    }

    public function getFilamentName(): string
    {
        return $this->fullName;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true; // Ajusta esta lógica según tus necesidades
    }

    public function organizationalUnit(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(OrganizationalUnit::class);
    }
}
