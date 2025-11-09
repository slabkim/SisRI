<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Kost;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class OwnerDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:owner']);
    }

    public function __invoke(Request $request)
    {
        $owner = $request->user();

        $ownerId = $owner->id;
        $kostQuery = Kost::ownedBy($ownerId);
        $totalKost = (clone $kostQuery)->count();

        $approvedBookingsTotal = Booking::ownedBy($ownerId)
            ->where('status', Booking::STATUS_APPROVED)
            ->count();

        $pendingBookings = Booking::ownedBy($ownerId)
            ->where('status', Booking::STATUS_PENDING)
            ->count();

        $availableRooms = max($totalKost - $approvedBookingsTotal, 0);

        $approvalWindowStart = Carbon::now()->subDays(30);
        $recentDecisions = Booking::ownedBy($ownerId)
            ->whereIn('status', [Booking::STATUS_APPROVED, Booking::STATUS_REJECTED])
            ->where('updated_at', '>=', $approvalWindowStart)
            ->get();

        $recentApproved = $recentDecisions->where('status', Booking::STATUS_APPROVED)->count();
        $approvalRate = $recentDecisions->count() > 0
            ? round(($recentApproved / $recentDecisions->count()) * 100)
            : null;

        $trendEnd = Carbon::today();
        $trendStart = (clone $trendEnd)->subDays(29);
        $trendData = Booking::ownedBy($ownerId)
            ->whereBetween('created_at', [$trendStart->copy()->startOfDay(), $trendEnd->copy()->endOfDay()])
            ->selectRaw('DATE(created_at) as day, COUNT(*) as total')
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('total', 'day')
            ->mapWithKeys(fn ($value, $key) => [Carbon::parse($key)->format('Y-m-d') => $value]);

        $chartLabels = [];
        $chartValues = [];

        for ($i = 0; $i < 30; $i++) {
            $current = $trendStart->copy()->addDays($i);
            if ($current->greaterThan($trendEnd)) {
                break;
            }
            $chartLabels[] = $current->format('d M');
            $chartValues[] = (int) ($trendData[$current->format('Y-m-d')] ?? 0);
        }

        $latestPending = Booking::ownedBy($ownerId)
            ->with(['kost', 'tenant'])
            ->where('status', Booking::STATUS_PENDING)
            ->latest()
            ->take(5)
            ->get();

        $recentKosts = $kostQuery
            ->with('photos')
            ->latest()
            ->take(3)
            ->get();

        return view('owner.dashboard', [
            'stats' => [
                'kost' => $totalKost,
                'available' => $availableRooms,
                'pending' => $pendingBookings,
                'approvalRate' => $approvalRate,
            ],
            'chart' => [
                'labels' => $chartLabels,
                'values' => $chartValues,
            ],
            'latestPending' => $latestPending,
            'recentKosts' => $recentKosts,
        ]);
    }
}
