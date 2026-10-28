<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['kas_class_id', 'user_id', 'message', 'status', 'admin_reply'])]
class Inquiry extends Model
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
