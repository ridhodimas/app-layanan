<?php

namespace App\Models;

use App\Enums\NumberSequencePrefix;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class NumberSequence extends Model
{
    use HasFactory;

    protected $fillable = [
        'prefix',
        'period',
        'last_number',
    ];

    protected function casts(): array
    {
        return [
            'last_number' => 'integer',
        ];
    }

    /**
     * Generate the next sequential number atomically with row-level locking.
     * Format: {PREFIX}-{YYYYMM}-{NNNNN}
     */
    public static function nextFormattedNumber(string|NumberSequencePrefix $prefix, ?string $period = null, int $digits = 5): string
    {
        $prefixStr = $prefix instanceof NumberSequencePrefix ? $prefix->value : strtoupper($prefix);
        $periodStr = $period ?? now()->format('Ym');

        return DB::transaction(function () use ($prefixStr, $periodStr, $digits) {
            $sequence = static::where('prefix', $prefixStr)
                ->where('period', $periodStr)
                ->lockForUpdate()
                ->first();

            if (! $sequence) {
                $sequence = static::create([
                    'prefix' => $prefixStr,
                    'period' => $periodStr,
                    'last_number' => 1,
                ]);
                $nextNumber = 1;
            } else {
                $nextNumber = $sequence->last_number + 1;
                $sequence->update(['last_number' => $nextNumber]);
            }

            return sprintf('%s-%s-%s', $prefixStr, $periodStr, str_pad((string) $nextNumber, $digits, '0', STR_PAD_LEFT));
        });
    }
}
