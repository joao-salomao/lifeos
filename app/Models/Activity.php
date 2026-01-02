<?php

namespace App\Models;

use App\Enums\ActivityType;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $check_in_id
 * @property ActivityType $type
 * @property \Illuminate\Support\Carbon $started_at
 * @property \Illuminate\Support\Carbon $ended_at
 * @property float|null $distance
 * @property float|null $calories_burned
 * @property int|null $steps
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class Activity extends Model
{
    protected $fillable = [
        'check_in_id',
        'type',
        'started_at',
        'ended_at',
        'distance',
        'calories_burned',
        'steps',
    ];

    protected function casts(): array
    {
        return [
            'type' => ActivityType::class,
            'started_at' => 'datetime',
            'ended_at' => 'datetime',
            'distance' => 'decimal:2',
            'calories_burned' => 'decimal:2',
            'steps' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<CheckIn, Activity>
     */
    public function checkIn(): BelongsTo
    {
        return $this->belongsTo(CheckIn::class);
    }
}
