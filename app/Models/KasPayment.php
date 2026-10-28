<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['dues_invoice_id', 'student_id', 'verified_by', 'amount', 'receipt_path', 'status', 'review_note', 'paid_at'])]
class KasPayment extends Model
{
    protected function casts(): array
    {
        return ['paid_at' => 'datetime'];
    }

    public function duesInvoice(): BelongsTo
    {
        return $this->belongsTo(DuesInvoice::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
