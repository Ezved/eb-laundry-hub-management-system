<?php

namespace App\Support;

use App\Models\Order;
use Illuminate\Support\Carbon;

class Capacity
{
    /**
     * Daily load limit (entire day, 24/7 tracking).
     */
    public const DAILY_LIMIT = 30;

    /**
     * Sum of load_qty for all non-canceled orders created TODAY (Manila time).
     * Counts from 00:00:00 to 23:59:59 of current day.
     */
    public static function todaysLoads(): int
    {
        $tz = config('app.timezone', 'Asia/Manila');

        // Start of day (00:00:00)
        $startOfDay = Carbon::now($tz)->startOfDay();

        // End of day (23:59:59)
        $endOfDay = Carbon::now($tz)->endOfDay();

        // Sum all load_qty from non-canceled orders created today
        $totalLoads = Order::where('status', '!=', 'canceled')
            ->whereBetween('created_at', [$startOfDay, $endOfDay])
            ->sum('load_qty');

        return (int) $totalLoads;
    }

    /**
     * Check if today's load capacity has been reached.
     */
    public static function isCapped(): bool
    {
        return self::todaysLoads() >= self::DAILY_LIMIT;
    }

    /**
     * Check if adding N loads would exceed the daily limit.
     */
    public static function wouldExceedWith(int $additionalLoads): bool
    {
        $currentLoads = self::todaysLoads();
        return ($currentLoads + $additionalLoads) > self::DAILY_LIMIT;
    }

    /**
     * Get remaining load capacity for today.
     */
    public static function remaining(): int
    {
        return max(0, self::DAILY_LIMIT - self::todaysLoads());
    }
}
