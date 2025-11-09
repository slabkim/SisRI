<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Models\Booking;
use App\Models\Kost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class BookingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(StoreBookingRequest $request, Kost $kost)
    {
        $user = $request->user();

        if ($kost->owner_id === $user->id) {
            return back()->with('error', 'Anda tidak dapat melakukan booking pada kost sendiri.');
        }

        Booking::create([
            'kost_id' => $kost->id,
            'owner_id' => $kost->owner_id,
            'user_id' => $user->id,
            'move_in_date' => $request->date('move_in_date'),
            'tenant_phone' => $request->input('tenant_phone'),
            'tenant_notes' => $request->input('tenant_notes'),
            'status' => Booking::STATUS_PENDING,
        ]);

        return redirect()->route('bookings.index')->with('success', 'Permintaan booking berhasil dikirim.');
    }

    public function tenantIndex(Request $request)
    {
        $perPage = (int) $request->input('per_page', 10);
        if (! in_array($perPage, [10, 20, 50], true)) {
            $perPage = 10;
        }

        $bookings = Booking::with(['kost', 'owner'])
            ->forTenant($request->user()->id)
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return view('bookings.index', [
            'bookings' => $bookings,
            'mode' => 'tenant',
            'filters' => [
                'per_page' => $perPage,
            ],
        ]);
    }

    public function ownerIndex(Request $request)
    {
        $perPage = (int) $request->input('per_page', 10);
        if (! in_array($perPage, [10, 20, 50], true)) {
            $perPage = 10;
        }

        $bookings = Booking::with(['kost', 'tenant'])
            ->ownedBy($request->user()->id)
            ->status($request->input('status'))
            ->dateRange($request->input('date_from'), $request->input('date_to'))
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return view('bookings.index', [
            'bookings' => $bookings,
            'mode' => 'owner',
            'filters' => $request->only(['status', 'date_from', 'date_to']) + [
                'per_page' => $perPage,
            ],
        ]);
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        $this->ensureOwner($request, $booking);

        $data = $request->validate([
            'action' => ['required', 'in:approve,reject'],
            'owner_note' => ['nullable', 'string', 'max:500'],
        ]);

        if ($data['action'] === 'approve') {
            $booking->update([
                'status' => Booking::STATUS_APPROVED,
                'approved_at' => now(),
                'rejected_at' => null,
                'owner_notes' => $data['owner_note'] ?? null,
            ]);
            $message = 'Booking disetujui.';
        } else {
            $booking->update([
                'status' => Booking::STATUS_REJECTED,
                'rejected_at' => now(),
                'approved_at' => null,
                'owner_notes' => $data['owner_note'] ?? null,
            ]);
            $message = 'Booking ditolak.';
        }

        return back()
            ->with('success', $message)
            ->with('highlight_booking', $booking->id);
    }

    public function export(Request $request)
    {
        $user = $request->user();
        abort_unless($user->isOwner(), 403);

        $bookings = Booking::with(['kost', 'tenant'])
            ->ownedBy($user->id)
            ->status($request->input('status'))
            ->dateRange($request->input('date_from'), $request->input('date_to'))
            ->latest()
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="booking-export.csv"',
        ];

        $callback = static function () use ($bookings) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Kost', 'Mahasiswa', 'Tanggal Masuk', 'Telepon', 'Status']);

            foreach ($bookings as $booking) {
                fputcsv($handle, [
                    $booking->kost->name,
                    $booking->tenant->name,
                    $booking->move_in_date->format('Y-m-d'),
                    $booking->tenant_phone,
                    strtoupper($booking->status),
                ]);
            }

            fclose($handle);
        };

        return Response::stream($callback, 200, $headers);
    }

    private function ensureOwner(Request $request, Booking $booking): void
    {
        if ($booking->owner_id !== $request->user()->id) {
            abort(403, 'Akses ditolak.');
        }
    }
}
