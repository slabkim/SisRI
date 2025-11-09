<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKostRequest;
use App\Http\Requests\UpdateKostRequest;
use App\Models\Booking;
use App\Models\Kost;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class OwnerKostController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:owner']);
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $facilities = array_values(array_filter((array) $request->input('facilities', [])));
        $minPrice = $request->filled('price_min') ? (float) $request->input('price_min') : null;
        $maxPrice = $request->filled('price_max') ? (float) $request->input('price_max') : null;
        $sort = $request->input('sort', 'updated_desc');
        $perPage = (int) $request->input('per_page', 9);
        if (! in_array($perPage, [9, 18, 30], true)) {
            $perPage = 9;
        }

        $kostsQuery = Kost::query()
            ->with('photos')
            ->withCount([
                'bookings as pending_bookings_count' => function ($query) {
                    $query->where('status', Booking::STATUS_PENDING);
                },
                'bookings as approved_bookings_count' => function ($query) {
                    $query->where('status', Booking::STATUS_APPROVED);
                },
            ])
            ->ownedBy($user->id)
            ->search($request->input('search'))
            ->priceBetween($minPrice, $maxPrice)
            ->hasFacilities($facilities);

        $kosts = $this->applySort($kostsQuery, $sort)
            ->paginate($perPage)
            ->withQueryString();

        return view('owner.kosts.index', [
            'kosts' => $kosts,
            'filters' => $request->only(['search', 'price_min', 'price_max']) + [
                'facilities' => $facilities,
                'sort' => $sort,
                'per_page' => $perPage,
            ],
        ]);
    }

    public function create()
    {
        return view('owner.kosts.create');
    }

    public function store(StoreKostRequest $request)
    {
        $data = $this->validatedData($request);
        $data['owner_id'] = $request->user()->id;
        $photos = $request->file('photos', []);

        if (count($photos) > 5) {
            throw ValidationException::withMessages([
                'photos' => 'Maksimal 5 foto dapat diunggah untuk satu kost.',
            ]);
        }

        $kost = Kost::create($data);
        $this->storePhotos($kost, $photos);

        return redirect()->route('owner.kosts.index')->with('success', 'Data kost berhasil ditambahkan.');
    }

    public function edit(Kost $kost)
    {
        $this->authorize('update', $kost);

        $kost->load('photos');

        return view('owner.kosts.edit', compact('kost'));
    }

    public function update(UpdateKostRequest $request, Kost $kost)
    {
        $this->authorize('update', $kost);

        $data = $this->validatedData($request);
        $removePhotoIds = array_filter((array) $request->input('remove_photos', []));
        $newPhotos = $request->file('photos', []);

        if ($removePhotoIds) {
            $photosToRemove = $kost->photos()->whereIn('id', $removePhotoIds)->get();
            foreach ($photosToRemove as $photo) {
                $photo->delete();
            }
        }

        $remainingCount = $kost->photos()->count();

        if ($remainingCount + count($newPhotos) > 5) {
            throw ValidationException::withMessages([
                'photos' => 'Total foto tidak boleh lebih dari 5. Hapus beberapa foto sebelum menambahkan yang baru.',
            ]);
        }

        $kost->update($data);
        $this->storePhotos($kost, $newPhotos);
        $this->reorderPhotos($kost);

        return redirect()->route('owner.kosts.index')->with('success', 'Data kost berhasil diperbarui.');
    }

    public function destroy(Kost $kost)
    {
        $this->authorize('delete', $kost);

        $kost->delete();

        return redirect()->route('owner.kosts.index')->with('success', 'Data kost berhasil dihapus.');
    }

    public function bulkAction(Request $request)
    {
        $data = $request->validate([
            'action' => ['required', 'in:archive,delete'],
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:kosts,id'],
        ]);

        $ownerId = $request->user()->id;

        $kosts = Kost::ownedBy($ownerId)
            ->whereIn('id', $data['ids'])
            ->get();

        if ($kosts->isEmpty()) {
            return redirect()->route('owner.kosts.index')->with('error', 'Tidak ada kost yang cocok dengan aksi.');
        }

        $affected = 0;

        foreach ($kosts as $kost) {
            if ($data['action'] === 'archive') {
                $kost->delete();
                $affected++;
            } elseif ($data['action'] === 'delete') {
                $kost->forceDelete();
                $affected++;
            }
        }

        $message = $data['action'] === 'archive'
            ? "{$affected} kost berhasil diarsipkan."
            : "{$affected} kost berhasil dihapus permanen.";

        return redirect()->route('owner.kosts.index')->with('success', $message);
    }

    private function validatedData(FormRequest $request): array
    {
        $data = $request->validated();
        $data['facilities'] = array_values(array_filter($data['facilities'] ?? []));
        if (array_key_exists('map_embed', $data)) {
            $data['map_embed'] = $data['map_embed'] !== null ? trim($data['map_embed']) : null;
        }
        unset($data['photos'], $data['remove_photos']);

        return $data;
    }

    private function storePhotos(Kost $kost, array $photos): void
    {
        if (empty($photos)) {
            return;
        }

        $basePosition = $kost->photos()->max('position');
        $basePosition = $basePosition === null ? -1 : (int) $basePosition;

        foreach ($photos as $index => $photo) {
            $path = $photo->store('kost_photos', 'public');

            $kost->photos()->create([
                'path' => $path,
                'position' => $basePosition + $index + 1,
            ]);
        }
    }

    private function reorderPhotos(Kost $kost): void
    {
        $orderedPhotos = $kost->photos()->orderBy('position')->orderBy('id')->get();

        foreach ($orderedPhotos as $index => $photo) {
            if ((int) $photo->position === $index) {
                continue;
            }

            $photo->update(['position' => $index]);
        }
    }

    private function applySort($query, string $sort)
    {
        return match ($sort) {
            'price_asc' => $query->orderBy('price_per_month', 'asc'),
            'price_desc' => $query->orderBy('price_per_month', 'desc'),
            'name_asc' => $query->orderBy('name', 'asc'),
            'popular_desc' => $query->orderBy('approved_bookings_count', 'desc')->orderBy('pending_bookings_count', 'desc'),
            default => $query->orderBy('updated_at', 'desc'),
        };
    }
}
