<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Kost;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $owner = User::updateOrCreate(
            ['email' => 'owner@sisri.test'],
            [
                'name' => 'Owner Demo',
                'password' => Hash::make('password'),
                'role' => User::ROLE_OWNER,
            ]
        );

        $student = User::updateOrCreate(
            ['email' => 'mahasiswa@sisri.test'],
            [
                'name' => 'Mahasiswa Demo',
                'password' => Hash::make('password'),
                'role' => User::ROLE_MAHASISWA,
            ]
        );

        $kostA = Kost::updateOrCreate(
            ['name' => 'Kost Harmoni'],
            [
                'owner_id' => $owner->id,
                'address' => 'Jl. Mawar No. 10, Kota Pelajar',
                'price_per_month' => 900000,
                'facilities' => ['WiFi', 'AC', 'Laundry'],
                'description' => 'Kost nyaman dekat kampus dengan akses 24 jam.',
                'photo_path' => null,
            ]
        );

        $kostB = Kost::updateOrCreate(
            ['name' => 'Kost Melati'],
            [
                'owner_id' => $owner->id,
                'address' => 'Jl. Melati No. 5, Kota Pelajar',
                'price_per_month' => 750000,
                'facilities' => ['WiFi', 'Parkir', 'Dapur Bersama'],
                'description' => 'Pilihan hemat dengan fasilitas lengkap.',
                'photo_path' => null,
            ]
        );

        $firstMoveIn = now()->addWeek()->toDateString();
        $secondMoveIn = now()->addWeeks(2)->toDateString();

        Booking::updateOrCreate(
            ['kost_id' => $kostA->id, 'user_id' => $student->id, 'move_in_date' => $firstMoveIn],
            [
                'owner_id' => $owner->id,
                'move_in_date' => $firstMoveIn,
                'tenant_phone' => '081234567890',
                'tenant_notes' => 'Butuh kamar lantai 1.',
                'status' => Booking::STATUS_PENDING,
            ]
        );

        Booking::updateOrCreate(
            ['kost_id' => $kostB->id, 'user_id' => $student->id, 'move_in_date' => $secondMoveIn],
            [
                'owner_id' => $owner->id,
                'move_in_date' => $secondMoveIn,
                'tenant_phone' => '081234567890',
                'tenant_notes' => null,
                'status' => Booking::STATUS_APPROVED,
                'approved_at' => now(),
            ]
        );
    }
}
