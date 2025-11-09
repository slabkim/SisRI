<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Kost;
use Illuminate\Http\Request;

class KostController extends Controller
{
    public function index(Request $request)
    {
        $facilities = array_values(array_filter((array) $request->input('facilities', [])));
        $minPrice = $request->filled('price_min') ? (float) $request->input('price_min') : null;
        $maxPrice = $request->filled('price_max') ? (float) $request->input('price_max') : null;
        $sort = $request->input('sort', 'newest');
        $perPage = (int) $request->input('per_page', 9);
        if (! in_array($perPage, [9, 18, 30], true)) {
            $perPage = 9;
        }

        $query = Kost::query()
            ->with(['owner', 'photos'])
            ->withCount([
                'bookings as approved_bookings_count' => function ($query) {
                    $query->where('status', Booking::STATUS_APPROVED);
                },
            ]);

        $query->search($request->input('search'))
            ->priceBetween($minPrice, $maxPrice)
            ->hasFacilities($facilities);

        $sortedQuery = match ($sort) {
            'price_asc' => $query->orderBy('price_per_month', 'asc'),
            'price_desc' => $query->orderBy('price_per_month', 'desc'),
            'popular_desc' => $query->orderBy('approved_bookings_count', 'desc')->orderBy('updated_at', 'desc'),
            default => $query->latest(),
        };

        $kosts = $sortedQuery->paginate($perPage)->withQueryString();

        return view('kosts.index', [
            'kosts' => $kosts,
            'filters' => $request->only(['search', 'price_min', 'price_max']) + [
                'facilities' => $facilities,
                'sort' => $sort,
                'per_page' => $perPage,
            ],
        ]);
    }

    public function show(Kost $kost)
    {
        $kost->load(['owner', 'photos'])
            ->loadCount([
                'bookings as approved_bookings_count' => function ($query) {
                    $query->where('status', Booking::STATUS_APPROVED);
                },
            ]);

        return view('kosts.show', compact('kost'));
    }
}
