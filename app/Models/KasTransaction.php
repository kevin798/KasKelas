<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['kas_class_id', 'created_by', 'type', 'category', 'description', 'amount', 'occurred_on', 'receipt_path', 'is_cancelled', 'cancellation_reason'])]
class KasTransaction extends Model
{
    protected function casts(): array
    {
        return ['occurred_on' => 'date', 'is_cancelled' => 'boolean'];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
