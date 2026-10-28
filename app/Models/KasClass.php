<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'class_code', 'initial_balance', 'monthly_dues'])]
class KasClass extends Model
{
    public function transactions(): HasMany
    {
        return $this->hasMany(KasTransaction::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(User::class)->where('role', 'student');
    }

    public function administrators(): HasMany
    {
        return $this->hasMany(User::class)->where('role', 'admin');
    }

    public function members(): HasMany
    {
        return $this->hasMany(User::class)->whereIn('role', ['student', 'admin']);
    }
}
