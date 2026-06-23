<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminFacilityController extends Controller
{
    public function index()
    {
        $facilities = DB::table('facilities')
            ->orderBy('is_archived')
            ->orderBy('facility_name')
            ->paginate(10);

        return view('admin.website.facilities.index', compact('facilities'));
    }

    public function create()
    {
        return redirect()->route('admin.website.index');
    }

    public function store(Request $request)
    {
        $validated = $this->validateFacility($request);
        $imagePath = $this->storeFacilityImage($request);

        $facilityId = DB::table('facilities')->insertGetId([
            'facility_key'      => $this->uniqueKey('facilities', 'facility_key', $validated['facility_name']),
            'facility_name'     => $validated['facility_name'],
            'description'       => $validated['description'] ?? null,
            'capacity'          => $validated['capacity'] ?? null,
            'location'          => $validated['location'] ?? null,
            'availability_days' => $validated['availability_days'] ?? null,
            'availability_hours'=> $validated['availability_hours'] ?? null,
            'equipment'         => json_encode($this->listToArray($validated['equipment'] ?? null)),
            'usage_for'         => json_encode($this->listToArray($validated['usage_for'] ?? null)),
            'image_path'        => $imagePath,
            'is_active'         => true,
            'is_archived'       => false,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Facility created successfully.',
                'item'    => $this->findFacilityForWebsite($facilityId),
            ]);
        }

        return redirect()
            ->route('admin.website.index')
            ->with('success', 'Facility created successfully.');
    }

    public function show(int $facility)
    {
        return redirect()->route('admin.website.index');
    }

    public function edit(int $facility)
    {
        return redirect()->route('admin.website.index');
    }

    public function update(Request $request, int $facility)
    {
        $facilityData = DB::table('facilities')
            ->where('id', $facility)
            ->first();

        abort_if(!$facilityData, 404);

        $validated  = $this->validateFacility($request);
        $imagePath  = $facilityData->image_path;

        if ($request->hasFile('facility_image')) {
            $this->deleteFacilityImage($imagePath);
            $imagePath = $this->storeFacilityImage($request);
        }

        DB::table('facilities')
            ->where('id', $facility)
            ->update([
                'facility_name'     => $validated['facility_name'],
                'description'       => $validated['description'] ?? null,
                'capacity'          => $validated['capacity'] ?? null,
                'location'          => $validated['location'] ?? null,
                'availability_days' => $validated['availability_days'] ?? null,
                'availability_hours'=> $validated['availability_hours'] ?? null,
                'equipment'         => json_encode($this->listToArray($validated['equipment'] ?? null)),
                'usage_for'         => json_encode($this->listToArray($validated['usage_for'] ?? null)),
                'image_path'        => $imagePath,
                'is_active'         => true,
            ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Facility updated successfully.',
                'item'    => $this->findFacilityForWebsite($facility),
            ]);
        }

        return redirect()
            ->route('admin.website.index')
            ->with('success', 'Facility updated successfully.');
    }

    public function archive(Request $request, int $facility)
    {
        $facilityData = DB::table('facilities')
            ->where('id', $facility)
            ->first();

        abort_if(!$facilityData, 404);

        $archive = $request->boolean('archive');

        DB::table('facilities')
            ->where('id', $facility)
            ->update([
                'is_archived' => $archive,
            ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $archive ? 'Facility archived successfully.' : 'Facility restored successfully.',
                'item'    => $this->findFacilityForWebsite($facility),
            ]);
        }

        return redirect()
            ->route('admin.website.index')
            ->with('success', $archive ? 'Facility archived successfully.' : 'Facility restored successfully.');
    }

    public function destroy(Request $request, int $facility)
    {
        $facilityData = DB::table('facilities')
            ->where('id', $facility)
            ->first();

        abort_if(!$facilityData, 404);

        $hasReservations = DB::table('facility_reservations')
            ->where('facility_id', $facility)
            ->exists();

        if ($hasReservations) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This facility cannot be deleted because it already has reservation records.',
                ], 422);
            }

            return back()->with('error', 'This facility cannot be deleted because it already has reservation records.');
        }

        DB::table('facilities')
            ->where('id', $facility)
            ->delete();

        $this->deleteFacilityImage($facilityData->image_path);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Facility deleted successfully.',
            ]);
        }

        return redirect()
            ->route('admin.website.index')
            ->with('success', 'Facility deleted successfully.');
    }

    private function validateFacility(Request $request): array
    {
        return $request->validate([
            'facility_name'     => ['required', 'string', 'max:150'],
            'description'       => ['nullable', 'string', 'max:5000'],
            'capacity'          => ['nullable', 'integer', 'min:1', 'max:10000'],
            'location'          => ['nullable', 'string', 'max:255'],
            'availability_days' => ['nullable', 'string', 'max:150'],
            'availability_hours'=> ['nullable', 'string', 'max:150'],
            'equipment'         => ['nullable', 'string', 'max:2000'],
            'usage_for'         => ['nullable', 'string', 'max:2000'],
            'facility_image'    => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
        ]);
    }

    private function findFacilityForWebsite(int $id)
    {
        $facility = DB::table('facilities')
            ->where('id', $id)
            ->first();

        if (!$facility) {
            return null;
        }

        $facility->equipment_text = implode(', ', json_decode($facility->equipment ?? '[]', true) ?: []);
        $facility->usage_for_text = implode(', ', json_decode($facility->usage_for ?? '[]', true) ?: []);

        return $facility;
    }

    private function storeFacilityImage(Request $request): ?string
    {
        if (!$request->hasFile('facility_image')) {
            return null;
        }

        $path = $request->file('facility_image')
            ->store('facility-images', 'public');

        return 'storage/' . $path;
    }

    private function deleteFacilityImage(?string $imagePath): void
    {
        if (!$imagePath) {
            return;
        }

        if (!Str::startsWith($imagePath, 'storage/')) {
            return;
        }

        $path = Str::after($imagePath, 'storage/');
        Storage::disk('public')->delete($path);
    }

    private function listToArray(?string $value): array
    {
        return collect(preg_split('/[\r\n,]+/', $value ?? ''))
            ->map(fn ($item) => trim($item))
            ->filter()
            ->unique()
            ->values()
            ->toArray();
    }

    private function uniqueKey(string $table, string $column, string $value): string
    {
        $baseKey = Str::slug($value);
        $key     = $baseKey;
        $counter = 1;

        while (DB::table($table)->where($column, $key)->exists()) {
            $key = $baseKey . '-' . $counter;
            $counter++;
        }

        return $key;
    }
}