<?php

namespace App\Support;

use App\Models\Order;
use Illuminate\Support\Carbon;

class Capacity
{
    /**
     * Daily load limit (entire day).
     */
    public const DAILY_LIMIT = 30;

    /**
     * Sum of load_qty for all non-canceled orders created TODAY (Manila time).
     * Counts from 00:00 to 23:59 of current day.
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

        // Debug logging
        \Log::info('Capacity todaysLoads() called', [
            'start' => $startOfDay->toDateTimeString(),
            'end' => $endOfDay->toDateTimeString(),
            'total_loads' => $totalLoads,
            'time_now' => Carbon::now($tz)->toDateTimeString(),
        ]);

        return (int) $totalLoads;
    }

    /**
     * Check if today's load capacity has been reached.
     */
    public static function isCapped(): bool
    {
        $currentLoads = self::todaysLoads();
        $isFull = $currentLoads >= self::DAILY_LIMIT;

        // Debug logging
        \Log::info('Capacity isCapped() called', [
            'current_loads' => $currentLoads,
            'limit' => self::DAILY_LIMIT,
            'is_capped' => $isFull,
        ]);

        return $isFull;
    }

    /**
     * Check if adding N loads would exceed the daily limit.
     */
    public static function wouldExceedWith(int $additionalLoads): bool
    {
        $currentLoads = self::todaysLoads();
        $wouldExceed = ($currentLoads + $additionalLoads) > self::DAILY_LIMIT;

        // Debug logging
        \Log::info('Capacity wouldExceedWith() called', [
            'current_loads' => $currentLoads,
            'additional_loads' => $additionalLoads,
            'total_would_be' => $currentLoads + $additionalLoads,
            'limit' => self::DAILY_LIMIT,
            'would_exceed' => $wouldExceed,
        ]);

        return $wouldExceed;
    }

    /**
     * Get remaining load capacity for today.
     */
    public static function remaining(): int
    {
        return max(0, self::DAILY_LIMIT - self::todaysLoads());
    }
}
