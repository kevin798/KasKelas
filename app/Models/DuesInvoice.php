<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['kas_class_id', 'student_id', 'period', 'amount', 'due_on'])]
class DuesInvoice extends Model
{
    protected function casts(): array
    {
        return ['due_on' => 'date'];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(KasPayment::class);
    }
}
