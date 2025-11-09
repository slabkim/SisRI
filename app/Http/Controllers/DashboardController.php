<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Kost;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except('index');
    }

    public function index()
    {
        if (auth()->check()) {
            $user = auth()->user();

            if ($user->isOwner()) {
                $stats = [
                    'total_kost' => $user->kosts()->count(),
                    'total_booking' => Booking::ownedBy($user->id)->count(),
                    'pending_booking' => Booking::ownedBy($user->id)->status(Booking::STATUS_PENDING)->count(),
                ];
                $recentBookings = Booking::ownedBy($user->id)
                    ->with(['kost', 'tenant'])
                    ->latest()
                    ->take(5)
                    ->get();
                $spotlight = $user->kosts()
                    ->with('photos')
                    ->latest('updated_at')
                    ->take(3)
                    ->get();
            } else {
                $stats = [
                    'total_kost' => Kost::count(),
                    'total_booking' => $user->bookings()->count(),
                    'approved_booking' => $user->bookings()->status(Booking::STATUS_APPROVED)->count(),
                ];
                $recentBookings = $user->bookings()
                    ->with(['kost', 'owner'])
                    ->latest()
                    ->take(5)
                    ->get();
                $spotlight = Kost::with(['photos', 'owner'])
                    ->latest()
                    ->take(3)
                    ->get();
            }

            return view('dashboard.index', [
                'user' => $user,
                'stats' => $stats,
                'recentBookings' => $recentBookings,
                'spotlight' => $spotlight,
            ]);
        } else {
            // Guest user - redirect to kosts.index
            return redirect()->route('kosts.index');
        }
    }
}
