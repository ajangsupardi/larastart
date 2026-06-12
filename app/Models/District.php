<?php

namespace App\Models;

use Database\Factories\DistrictFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class District extends Model
{
    /** @use HasFactory<DistrictFactory> */
    use HasFactory;

    protected $fillable = ['name', 'regency_id', 'created_by'];

    public function regency(): BelongsTo
    {
        return $this->belongsTo(Regency::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
