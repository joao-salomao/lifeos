<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $user_id
 * @property string $title
 * @property string|null $description
 * @property \Illuminate\Support\Carbon $checked_in_at
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class CheckIn extends Model
{
    /** @use HasFactory<\Database\Factories\CheckInFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'checked_in_at',
    ];

    protected function casts(): array
    {
        return [
            'checked_in_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<User, CheckIn>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<Activity>
     */
    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }
}
