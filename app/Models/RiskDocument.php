<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiskDocument extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'risk_documents';

    protected $fillable = [
        'risk_id',
        'title',
        'description',
        'classification',
        'file_path',
        'file_paths',
        'uploaded_at',
        'document_type',
        'document_date',
        'control_id',
        'action_id',
        'status',
        'validated_by',
        'validated_at',
        'validation_comment',
        'uploaded_by',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
        'document_date' => 'date',
        'validated_at' => 'datetime',
        'file_paths' => 'array',
    ];

    public function risk(): BelongsTo
    {
        return $this->belongsTo(Risk::class);
    }

    public function control(): BelongsTo
    {
        return $this->belongsTo(Control::class);
    }

    public function action(): BelongsTo
    {
        return $this->belongsTo(Action::class);
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function validatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }
}
