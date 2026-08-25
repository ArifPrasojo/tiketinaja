<?php

namespace App\Console\Commands;

use App\Services\BookingService;
use Illuminate\Console\Command;

class CompleteFinishedBookingsCommand extends Command
{
    protected $signature = 'bookings:complete';

    protected $description = 'Mark confirmed bookings as completed when all their slots have passed';

    public function handle(BookingService $bookingService): int
    {
        $count = $bookingService->completeFinishedBookings();

        $this->info("Completed {$count} bookings.");

        return self::SUCCESS;
    }
}
