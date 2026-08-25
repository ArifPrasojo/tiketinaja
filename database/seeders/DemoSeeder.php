<?php

namespace Database\Seeders;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\Court;
use App\Models\OperatingHour;
use App\Models\Payment;
use App\Models\Promo;
use App\Models\PromoUsage;
use App\Models\ReservedSlot;
use App\Models\Review;
use App\Models\Sport;
use App\Models\User;
use App\Models\Venue;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * DemoSeeder — Data lengkap untuk pengujian semua fitur TiketinAja
 *
 * Akun yang dibuat:
 *   admin@tiketinaja.test      / password  (Admin)
 *   manager@tiketinaja.test    / password  (Manager Venue 1 & 2)
 *   manager2@tiketinaja.test   / password  (Manager Venue 3)
 *   customer@tiketinaja.test   / password  (Customer utama, banyak skenario)
 *   customer2@tiketinaja.test  / password  (Customer kedua, untuk review & booking lain)
 *
 * Skenario booking yang tersedia:
 *   1. Pending (belum bayar, expired 15 mnt)
 *   2. Pending (belum bayar, masih aktif)
 *   3. Confirmed (sudah bayar, belum check-in, besok)
 *   4. Confirmed (sudah bayar, belum check-in, hari ini — window check-in terbuka)
 *   5. Confirmed + checked-in (SEDANG BERMAIN — slot dimulai 5 menit lalu)
 *   6. Completed (sudah selesai, belum diulas)
 *   7. Completed + ulasan bintang 5
 *   8. Cancelled
 *   9. Expired (pembayaran kedaluwarsa)
 *  10. Booking dengan promo diskon
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. Users ────────────────────────────────────────────────────────
        $admin = User::firstOrCreate(['email' => 'admin@tiketinaja.test'], [
            'name'     => 'Admin Sistem',
            'password' => Hash::make('password'),
            'role'     => 'admin',
            'phone'    => '08100000001',
        ]);

        $manager = User::firstOrCreate(['email' => 'manager@tiketinaja.test'], [
            'name'     => 'Budi Santoso',
            'password' => Hash::make('password'),
            'role'     => 'manager',
            'phone'    => '08111222333',
        ]);

        $manager2 = User::firstOrCreate(['email' => 'manager2@tiketinaja.test'], [
            'name'     => 'Siti Rahayu',
            'password' => Hash::make('password'),
            'role'     => 'manager',
            'phone'    => '08222333444',
        ]);

        $customer = User::firstOrCreate(['email' => 'customer@tiketinaja.test'], [
            'name'     => 'Andi Wijaya',
            'password' => Hash::make('password'),
            'role'     => 'customer',
            'phone'    => '08512345678',
        ]);

        $customer2 = User::firstOrCreate(['email' => 'customer2@tiketinaja.test'], [
            'name'     => 'Rina Kusuma',
            'password' => Hash::make('password'),
            'role'     => 'customer',
            'phone'    => '08556781234',
        ]);

        // ── 2. Sports ────────────────────────────────────────────────────────
        $sports = [
            ['name' => 'Futsal',      'slug' => 'futsal',      'icon' => '⚽'],
            ['name' => 'Badminton',   'slug' => 'badminton',   'icon' => '🏸'],
            ['name' => 'Basket',      'slug' => 'basket',      'icon' => '🏀'],
            ['name' => 'Tenis',       'slug' => 'tenis',       'icon' => '🎾'],
            ['name' => 'Mini Soccer', 'slug' => 'mini-soccer', 'icon' => '⚽'],
        ];
        foreach ($sports as $s) {
            Sport::firstOrCreate(['slug' => $s['slug']], $s);
        }
        $futsal    = Sport::where('slug', 'futsal')->first();
        $badminton = Sport::where('slug', 'badminton')->first();
        $basket    = Sport::where('slug', 'basket')->first();
        $tenis     = Sport::where('slug', 'tenis')->first();

        // ── 3. Venues ────────────────────────────────────────────────────────

        // Venue 1: GBK Arena (manager)
        $venue1 = Venue::firstOrCreate(['slug' => 'gelora-bung-karno-arena'], [
            'manager_id'  => $manager->id,
            'name'        => 'Gelora Bung Karno Arena',
            'description' => 'Fasilitas olahraga premium di jantung kota Jakarta. Lapangan standar nasional dengan pencahayaan terbaik.',
            'address'     => 'Jl. Pintu Satu Senayan No. 1',
            'city'        => 'Jakarta Pusat',
            'phone'       => '02157901234',
            'is_active'   => true,
        ]);

        // Venue 2: Tangkas Sports (manager)
        $venue2 = Venue::firstOrCreate(['slug' => 'tangkas-sports-centre'], [
            'manager_id'  => $manager->id,
            'name'        => 'Tangkas Sports Centre',
            'description' => 'GOR bulutangkis & basket berkualitas tinggi di Jakarta Barat.',
            'address'     => 'Jl. Tanjung Duren Raya No. 5',
            'city'        => 'Jakarta Barat',
            'phone'       => '02156789012',
            'is_active'   => true,
        ]);

        // Venue 3: Sportify Bekasi (manager2)
        $venue3 = Venue::firstOrCreate(['slug' => 'sportify-bekasi'], [
            'manager_id'  => $manager2->id,
            'name'        => 'Sportify Bekasi',
            'description' => 'Pusat olahraga modern di Bekasi dengan fasilitas terlengkap.',
            'address'     => 'Jl. Ahmad Yani No. 88',
            'city'        => 'Bekasi',
            'phone'       => '02188991234',
            'is_active'   => true,
        ]);

        // Operating hours untuk semua venue
        foreach ([$venue1, $venue2, $venue3] as $venue) {
            for ($day = 0; $day <= 6; $day++) {
                OperatingHour::firstOrCreate(
                    ['venue_id' => $venue->id, 'day_of_week' => $day],
                    ['open_time' => '07:00', 'close_time' => '23:00', 'is_closed' => false]
                );
            }
        }

        // ── 4. Courts ────────────────────────────────────────────────────────

        // GBK courts
        $courtFutsalA = Court::firstOrCreate(['venue_id' => $venue1->id, 'name' => 'Lapangan Futsal A (Sintetis)'], [
            'sport_id'       => $futsal->id,
            'description'    => 'Lapangan futsal rumput sintetis premium, AC, scoreboard digital.',
            'price_per_hour' => 150000,
            'facilities'     => ['Bola', 'Rompi', 'AC', 'Scoreboard Digital', 'Loker'],
            'is_active'      => true,
        ]);
        $courtFutsalB = Court::firstOrCreate(['venue_id' => $venue1->id, 'name' => 'Lapangan Futsal B (Vinyl)'], [
            'sport_id'       => $futsal->id,
            'description'    => 'Lapangan futsal lantai vinyl, cocok untuk turnamen.',
            'price_per_hour' => 175000,
            'facilities'     => ['Bola', 'Rompi', 'Papan Skor'],
            'is_active'      => true,
        ]);

        // Tangkas courts
        $courtBad1 = Court::firstOrCreate(['venue_id' => $venue2->id, 'name' => 'Lapangan Badminton 1'], [
            'sport_id'       => $badminton->id,
            'description'    => 'Lapangan badminton standar nasional, lantai kayu.',
            'price_per_hour' => 60000,
            'facilities'     => ['Shuttlecock', 'Net', 'Ganti Baju'],
            'is_active'      => true,
        ]);
        $courtBad2 = Court::firstOrCreate(['venue_id' => $venue2->id, 'name' => 'Lapangan Badminton 2'], [
            'sport_id'       => $badminton->id,
            'price_per_hour' => 60000,
            'facilities'     => ['Shuttlecock', 'Net'],
            'is_active'      => true,
        ]);
        $courtBasket = Court::firstOrCreate(['venue_id' => $venue2->id, 'name' => 'Lapangan Basket Full Court'], [
            'sport_id'       => $basket->id,
            'description'    => 'Full court basket standar internasional.',
            'price_per_hour' => 200000,
            'facilities'     => ['Bola Basket', 'Ring Adjustable', 'AC', 'Kursi Penonton'],
            'is_active'      => true,
        ]);

        // Sportify courts
        $courtTenis = Court::firstOrCreate(['venue_id' => $venue3->id, 'name' => 'Lapangan Tenis Indoor'], [
            'sport_id'       => $tenis->id,
            'description'    => 'Lapangan tenis indoor dengan lantai granit dan pencahayaan LED.',
            'price_per_hour' => 120000,
            'facilities'     => ['Bola Tenis', 'Net', 'AC', 'Penonton'],
            'is_active'      => true,
        ]);
        $courtFutsalC = Court::firstOrCreate(['venue_id' => $venue3->id, 'name' => 'Lapangan Futsal VIP'], [
            'sport_id'       => $futsal->id,
            'price_per_hour' => 200000,
            'facilities'     => ['Bola', 'Rompi', 'AC', 'Minuman Gratis', 'Loker VIP'],
            'is_active'      => true,
        ]);

        // ── 5. Promos ────────────────────────────────────────────────────────
        $promoFlat = Promo::firstOrCreate(['code' => 'TIKETIN10'], [
            'venue_id'    => null,
            'description' => 'Diskon 10% untuk semua booking (maks Rp 50.000)',
            'type'        => 'percentage',
            'value'       => 10,
            'max_discount' => 50000,
            'max_uses'    => 100,
            'is_active'   => true,
        ]);

        $promoNew = Promo::firstOrCreate(['code' => 'NEWUSER25'], [
            'venue_id'           => null,
            'description'        => 'Diskon Rp 25.000 untuk pengguna baru (min. booking Rp 100.000)',
            'type'               => 'fixed',
            'value'              => 25000,
            'min_booking_amount' => 100000,
            'max_uses'           => 50,
            'is_active'          => true,
        ]);

        $promoGbk = Promo::firstOrCreate(['code' => 'GBK20'], [
            'venue_id'    => $venue1->id,
            'description' => 'Diskon 20% khusus GBK Arena (maks Rp 100.000)',
            'type'        => 'percentage',
            'value'       => 20,
            'max_discount' => 100000,
            'max_uses'    => 30,
            'expires_at'  => now()->addMonth(),
            'is_active'   => true,
        ]);

        // ── 6. Bookings — berbagai skenario ──────────────────────────────────

        $today     = today();
        $yesterday = today()->subDay();
        $tomorrow  = today()->addDay();
        $twoDays   = today()->addDays(2);

        // Helper closure
        $makeBooking = function (
            User    $user,
            Court   $court,
            string  $date,
            string  $startTime,
            int     $hours,
            BookingStatus $status,
            PaymentStatus $payStatus,
            bool    $checkedIn      = false,
            ?Carbon $checkedInAt    = null,
            ?int    $discountAmount = 0,
            ?Promo  $promo          = null
        ) {
            $code        = 'BK-' . strtoupper(Str::random(8));
            $total       = $court->price_per_hour * $hours;
            $finalAmount = $total - ($discountAmount ?? 0);

            $booking = Booking::create([
                'booking_code'    => $code,
                'user_id'         => $user->id,
                'venue_id'        => $court->venue_id,
                'promo_id'        => $promo?->id,
                'status'          => $status,
                'total_amount'    => $total,
                'discount_amount' => $discountAmount ?? 0,
                'expires_at'      => $status === BookingStatus::Pending ? now()->addMinutes(15) : null,
                'checked_in'      => $checkedIn,
                'checked_in_at'   => $checkedInAt,
                'qr_code'         => Str::uuid()->toString(),
            ]);

            $start = Carbon::parse($date . ' ' . $startTime);
            for ($h = 0; $h < $hours; $h++) {
                $slotStart = $start->copy()->addHours($h)->format('H:i');
                $slotEnd   = $start->copy()->addHours($h + 1)->format('H:i');

                BookingItem::create([
                    'booking_id'   => $booking->id,
                    'court_id'     => $court->id,
                    'booking_date' => $date,
                    'start_time'   => $slotStart,
                    'end_time'     => $slotEnd,
                    'price'        => $court->price_per_hour,
                ]);

                // Reserved slots only for active bookings
                if (in_array($status->value, ['pending', 'confirmed'])) {
                    ReservedSlot::firstOrCreate([
                        'court_id'     => $court->id,
                        'booking_date' => $date,
                        'start_time'   => $slotStart,
                    ], [
                        'booking_id' => $booking->id,
                    ]);
                }
            }

            Payment::create([
                'booking_id'     => $booking->id,
                'payment_code'   => 'PAY-' . strtoupper(Str::random(8)),
                'amount'         => $finalAmount,
                'payment_method' => 'mock',
                'status'         => $payStatus,
                'paid_at'        => $payStatus === PaymentStatus::Paid ? now()->subHours(rand(1, 48)) : null,
            ]);

            if ($promo && $discountAmount > 0) {
                PromoUsage::firstOrCreate(['promo_id' => $promo->id, 'booking_id' => $booking->id], [
                    'user_id'         => $user->id,
                    'discount_amount' => $discountAmount,
                ]);
                $promo->increment('used_count');
            }

            return $booking;
        };

        // ── Skenario 1: PENDING (belum bayar, masih aktif) ──
        $makeBooking(
            $customer, $courtFutsalA,
            $tomorrow->toDateString(), '14:00', 2,
            BookingStatus::Pending, PaymentStatus::Pending
        );

        // ── Skenario 2: PENDING dengan promo (belum bayar) ──
        $makeBooking(
            $customer, $courtBad1,
            $twoDays->toDateString(), '09:00', 1,
            BookingStatus::Pending, PaymentStatus::Pending,
            false, null, 25000, $promoNew
        );

        // ── Skenario 3: CONFIRMED (sudah bayar, besok) ──
        $makeBooking(
            $customer, $courtFutsalA,
            $tomorrow->toDateString(), '19:00', 2,
            BookingStatus::Confirmed, PaymentStatus::Paid
        );

        // ── Skenario 4: CONFIRMED dengan promo GBK ──
        $discount = (int) round(150000 * 0.20); // 20% dari 150000 = 30000
        $makeBooking(
            $customer, $courtFutsalA,
            $twoDays->toDateString(), '10:00', 1,
            BookingStatus::Confirmed, PaymentStatus::Paid,
            false, null, $discount, $promoGbk
        );

        // ── Skenario 5: CONFIRMED (hari ini, window check-in terbuka dalam 10 menit) ──
        // Jam mulai = sekarang + 8 menit (sudah masuk window check-in 10 mnt)
        $checkInSlot  = now()->addMinutes(8)->format('H');
        $checkInStart = str_pad($checkInSlot, 2, '0', STR_PAD_LEFT) . ':00';
        // Pastikan tidak melebihi jam tutup (23:00)
        if ((int)$checkInSlot < 22) {
            $makeBooking(
                $customer, $courtFutsalB,
                $today->toDateString(), $checkInStart, 1,
                BookingStatus::Confirmed, PaymentStatus::Paid
            );
        }

        // ── Skenario 6: SEDANG BERMAIN (check-in, slot dimulai 20 menit lalu) ──
        $playingHour  = now()->subMinutes(20)->format('H');
        $playingStart = str_pad($playingHour, 2, '0', STR_PAD_LEFT) . ':00';
        // Pastikan valid (08:00-22:00)
        if ((int)$playingHour >= 8 && (int)$playingHour < 22) {
            $playingBooking = $makeBooking(
                $customer, $courtBad1,
                $today->toDateString(), $playingStart, 1,
                BookingStatus::Confirmed, PaymentStatus::Paid,
                true, now()->subMinutes(8)
            );
        }

        // ── Skenario 7: COMPLETED (selesai kemarin, belum ada ulasan) ──
        $makeBooking(
            $customer, $courtFutsalA,
            $yesterday->toDateString(), '09:00', 2,
            BookingStatus::Completed, PaymentStatus::Paid,
            true, Carbon::parse($yesterday->toDateString() . ' 08:52')
        );

        // ── Skenario 8: COMPLETED + ulasan bintang 5 ──
        $completedWithReview = $makeBooking(
            $customer, $courtBasket,
            $yesterday->toDateString(), '15:00', 2,
            BookingStatus::Completed, PaymentStatus::Paid,
            true, Carbon::parse($yesterday->toDateString() . ' 14:55')
        );
        Review::firstOrCreate(['booking_id' => $completedWithReview->id], [
            'user_id'  => $customer->id,
            'venue_id' => $venue2->id,
            'rating'   => 5,
            'comment'  => 'Lapangan bersih, staff ramah, fasilitas lengkap. Pasti balik lagi!',
        ]);

        // ── Skenario 9: COMPLETED + ulasan bintang 4 (customer2 di venue3) ──
        $completedReview2 = $makeBooking(
            $customer2, $courtTenis,
            $yesterday->toDateString(), '11:00', 1,
            BookingStatus::Completed, PaymentStatus::Paid,
            true, Carbon::parse($yesterday->toDateString() . ' 10:52')
        );
        Review::firstOrCreate(['booking_id' => $completedReview2->id], [
            'user_id'  => $customer2->id,
            'venue_id' => $venue3->id,
            'rating'   => 4,
            'comment'  => 'Lapangan bagus, tapi parkir agak penuh. Overall oke!',
        ]);

        // ── Skenario 10: CANCELLED ──
        $makeBooking(
            $customer, $courtFutsalC,
            $tomorrow->toDateString(), '16:00', 2,
            BookingStatus::Cancelled, PaymentStatus::Cancelled
        );

        // ── Skenario 11: EXPIRED (pembayaran kedaluwarsa) ──
        $expiredBooking = Booking::create([
            'booking_code'    => 'BK-EXPIRED1',
            'user_id'         => $customer->id,
            'venue_id'        => $courtFutsalA->venue_id,
            'status'          => BookingStatus::Expired,
            'total_amount'    => 150000,
            'discount_amount' => 0,
            'expires_at'      => now()->subHours(2),
            'qr_code'         => Str::uuid()->toString(),
        ]);
        BookingItem::create([
            'booking_id'   => $expiredBooking->id,
            'court_id'     => $courtFutsalA->id,
            'booking_date' => $today->toDateString(),
            'start_time'   => '08:00',
            'end_time'     => '09:00',
            'price'        => 150000,
        ]);
        Payment::create([
            'booking_id'     => $expiredBooking->id,
            'payment_code'   => 'PAY-EXPIRED1',
            'amount'         => 150000,
            'payment_method' => 'mock',
            'status'         => PaymentStatus::Expired,
        ]);

        // ── Skenario 12: Booking customer2 (confirmed, besok) ──
        $makeBooking(
            $customer2, $courtFutsalA,
            $tomorrow->toDateString(), '08:00', 1,
            BookingStatus::Confirmed, PaymentStatus::Paid
        );

        // ── Skenario 13: Booking customer2 (completed, dapat diulas) ──
        $makeBooking(
            $customer2, $courtBad2,
            $yesterday->toDateString(), '13:00', 1,
            BookingStatus::Completed, PaymentStatus::Paid,
            true, Carbon::parse($yesterday->toDateString() . ' 12:52')
        );

        $this->command->info('');
        $this->command->info('  ✅  Demo seeder selesai!');
        $this->command->info('  ─────────────────────────────────────────────────────────');
        $this->command->info('  Akun untuk login:');
        $this->command->info('  Admin    → admin@tiketinaja.test      / password');
        $this->command->info('  Manager  → manager@tiketinaja.test    / password');
        $this->command->info('  Manager2 → manager2@tiketinaja.test   / password');
        $this->command->info('  Customer → customer@tiketinaja.test   / password');
        $this->command->info('  Customer2→ customer2@tiketinaja.test  / password');
        $this->command->info('  ─────────────────────────────────────────────────────────');
        $this->command->info('  Venue & Courts: 3 venue, 7 lapangan');
        $this->command->info('  Promo aktif: TIKETIN10 · NEWUSER25 · GBK20');
        $this->command->info('  Skenario booking: 13 booking (pending/confirmed/');
        $this->command->info('    sedang bermain/completed/cancelled/expired)');
        $this->command->info('');
    }
}
