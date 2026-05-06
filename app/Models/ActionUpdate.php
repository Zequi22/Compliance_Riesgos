<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActionUpdate extends Model
{
    protected $fillable = [
        'action_id',
        'user_id',
        'comment',
    ];

    public function action(): BelongsTo
    {
        return $this->belongsTo(Action::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
