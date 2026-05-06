<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ControlEvidence extends Model
{
    protected $table = 'control_evidences';

    protected $fillable = [
        'control_id',
        'file_path',
        'title',
        'description',
        'uploaded_at',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
    ];

    public function control(): BelongsTo
    {
        return $this->belongsTo(Control::class);
    }
}
