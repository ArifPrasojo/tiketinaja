<?php

namespace App\Console\Commands;

use App\Services\BookingService;
use Illuminate\Console\Command;

class ExpirePendingBookingsCommand extends Command
{
    protected $signature = 'bookings:expire';

    protected $description = 'Expire pending bookings past their expiry time';

    public function handle(BookingService $bookingService): int
    {
        $count = $bookingService->expirePendingBookings();

        $this->info("Expired {$count} bookings.");

        return self::SUCCESS;
    }
}
