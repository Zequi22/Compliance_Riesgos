<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrganizationalUnit extends Model
{
    protected $fillable = [
        'name',
        'type',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function risks(): HasMany
    {
        return $this->hasMany(Risk::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function controls(): HasMany
    {
        return $this->hasMany(Control::class);
    }

    public function actions(): HasMany
    {
        return $this->hasMany(Action::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
