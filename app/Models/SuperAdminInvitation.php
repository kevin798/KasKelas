<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['kas_class_id', 'invited_by', 'email', 'token_hash', 'expires_at', 'accepted_at'])]
class SuperAdminInvitation extends Model
{
    protected function casts(): array
    {
        return ['expires_at' => 'datetime', 'accepted_at' => 'datetime'];
    }

    public function kasClass(): BelongsTo
    {
        return $this->belongsTo(KasClass::class);
    }
}
