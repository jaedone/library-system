<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AdminResourceController extends Controller
{
    public function index()
    {
        $resources = DB::table('resources as r')
            ->join('material_types as mt', 'r.material_type_id', '=', 'mt.id')
            ->join('categories as c', 'r.category_id', '=', 'c.id')
            ->leftJoin('publishers as p', 'r.publisher_id', '=', 'p.id')
            ->leftJoin('resource_authors as ra', 'r.id', '=', 'ra.resource_id')
            ->leftJoin('authors as a', 'ra.author_id', '=', 'a.id')
            ->leftJoin('resource_copies as rc', 'r.id', '=', 'rc.resource_id')
            ->select(
                'r.id',
                'r.title',
                'r.isbn',
                'r.publication_year',
                'r.edition',
                'r.description',
                'r.cover_image_path',
                'r.material_type_id',
                'r.category_id',
                'r.publisher_id',
                'r.is_reference_only',
                'r.is_digital',
                'r.digital_url',
                'r.is_archived',
                'r.created_at',
                'mt.material_type_name',
                'c.category_name',
                'p.publisher_name',
                DB::raw("COALESCE(STRING_AGG(DISTINCT a.author_name, ', '), 'Unknown Author') as authors"),
                DB::raw('COUNT(DISTINCT rc.id) as total_copies')
            )
            ->groupBy(
                'r.id',
                'r.title',
                'r.isbn',
                'r.publication_year',
                'r.edition',
                'r.description',
                'r.cover_image_path',
                'r.material_type_id',
                'r.category_id',
                'r.publisher_id',
                'r.is_reference_only',
                'r.is_digital',
                'r.digital_url',
                'r.is_archived',
                'r.created_at',
                'mt.material_type_name',
                'c.category_name',
                'p.publisher_name'
            )
            ->orderBy('r.is_archived')
            ->orderByDesc('r.created_at')
            ->paginate(10);

        return view('admin.website.resources.index', compact('resources'));
    }

    public function create()
    {
        return redirect()->route('admin.website.index');
    }

    public function store(Request $request)
    {
        $validated = $this->validateResource($request);

        $publisherId = $this->resolvePublisherId(
            $validated['publisher_id'] ?? null,
            $validated['publisher_name'] ?? null
        );

        $coverPath = null;

        if ($request->hasFile('cover_image')) {
            $coverPath = $request->file('cover_image')
                ->store('resource-covers', 'public');
        }

        $resourceId = DB::transaction(function () use ($request, $validated, $publisherId, $coverPath) {
            $resourceId = DB::table('resources')->insertGetId([
                'material_type_id' => $validated['material_type_id'],
                'category_id'      => $validated['category_id'],
                'publisher_id'     => $publisherId,
                'title'            => $validated['title'],
                'isbn'             => $validated['isbn'] ?? null,
                'publication_year' => $validated['publication_year'] ?? null,
                'edition'          => $validated['edition'] ?? null,
                'description'      => $validated['description'] ?? null,
                'cover_image_path' => $coverPath,
                'is_reference_only'=> $request->boolean('is_reference_only'),
                'is_digital'       => $request->boolean('is_digital'),
                'digital_url'      => $validated['digital_url'] ?? null,
                'is_archived'      => false,
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);

            $this->syncAuthors($resourceId, $validated['author_names'] ?? '');
            $this->createCopies($resourceId, $validated, $request);

            return $resourceId;
        });

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Resource created successfully.',
                'item'    => $this->findResourceForWebsite($resourceId),
            ]);
        }

        return redirect()
            ->route('admin.website.index')
            ->with('success', 'Resource created successfully.');
    }

    public function show(int $resource)
    {
        return redirect()->route('admin.website.index');
    }

    public function edit(int $resource)
    {
        return redirect()->route('admin.website.index');
    }

    public function update(Request $request, int $resource)
    {
        $resourceData = DB::table('resources')
            ->where('id', $resource)
            ->first();

        abort_if(!$resourceData, 404);

        $validated   = $this->validateResource($request, $resource);
        $publisherId = $this->resolvePublisherId(
            $validated['publisher_id'] ?? null,
            $validated['publisher_name'] ?? null
        );

        $coverPath = $resourceData->cover_image_path;

        if ($request->hasFile('cover_image')) {
            if ($coverPath) {
                Storage::disk('public')->delete($coverPath);
            }
            $coverPath = $request->file('cover_image')
                ->store('resource-covers', 'public');
        }

        DB::transaction(function () use ($request, $resource, $validated, $publisherId, $coverPath) {
            DB::table('resources')
                ->where('id', $resource)
                ->update([
                    'material_type_id' => $validated['material_type_id'],
                    'category_id'      => $validated['category_id'],
                    'publisher_id'     => $publisherId,
                    'title'            => $validated['title'],
                    'isbn'             => $validated['isbn'] ?? null,
                    'publication_year' => $validated['publication_year'] ?? null,
                    'edition'          => $validated['edition'] ?? null,
                    'description'      => $validated['description'] ?? null,
                    'cover_image_path' => $coverPath,
                    'is_reference_only'=> $request->boolean('is_reference_only'),
                    'is_digital'       => $request->boolean('is_digital'),
                    'digital_url'      => $validated['digital_url'] ?? null,
                    'updated_at'       => now(),
                ]);

            $this->syncAuthors($resource, $validated['author_names'] ?? '');
            $this->createCopies($resource, $validated, $request);
        });

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Resource updated successfully.',
                'item'    => $this->findResourceForWebsite($resource),
            ]);
        }

        return redirect()
            ->route('admin.website.index')
            ->with('success', 'Resource updated successfully.');
    }

    public function archive(Request $request, int $resource)
    {
        $resourceData = DB::table('resources')
            ->where('id', $resource)
            ->first();

        abort_if(!$resourceData, 404);

        $archive = $request->boolean('archive');

        DB::table('resources')
            ->where('id', $resource)
            ->update([
                'is_archived' => $archive,
                'updated_at'  => now(),
            ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $archive ? 'Resource archived successfully.' : 'Resource restored successfully.',
                'item'    => $this->findResourceForWebsite($resource),
            ]);
        }

        return redirect()
            ->route('admin.website.index')
            ->with('success', $archive ? 'Resource archived successfully.' : 'Resource restored successfully.');
    }

    public function destroy(Request $request, int $resource)
    {
        $resourceData = DB::table('resources')
            ->where('id', $resource)
            ->first();

        abort_if(!$resourceData, 404);

        $hasBorrowRecords = DB::table('borrow_transactions as bt')
            ->join('resource_copies as rc', 'bt.copy_id', '=', 'rc.id')
            ->where('rc.resource_id', $resource)
            ->exists();

        if ($hasBorrowRecords) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This resource cannot be deleted because it already has borrowing records.',
                ], 422);
            }

            return back()->with('error', 'This resource cannot be deleted because it already has borrowing records.');
        }

        DB::transaction(function () use ($resource, $resourceData) {
            DB::table('resource_authors')
                ->where('resource_id', $resource)
                ->delete();

            DB::table('resource_copies')
                ->where('resource_id', $resource)
                ->delete();

            DB::table('resources')
                ->where('id', $resource)
                ->delete();

            if ($resourceData->cover_image_path) {
                Storage::disk('public')->delete($resourceData->cover_image_path);
            }
        });

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Resource deleted successfully.',
            ]);
        }

        return redirect()
            ->route('admin.website.index')
            ->with('success', 'Resource deleted successfully.');
    }

    private function formData(): array
    {
        return [
            'categories'      => DB::table('categories')->orderBy('category_name')->get(),
            'materialTypes'   => DB::table('material_types')->orderBy('material_type_name')->get(),
            'publishers'      => DB::table('publishers')->orderBy('publisher_name')->get(),
            'libraryBranches' => DB::table('library_branches')->orderBy('branch_name')->get(),
            'copyStatuses'    => DB::table('copy_statuses')->orderBy('status_name')->get(),
        ];
    }

    private function validateResource(Request $request, ?int $resourceId = null): array
    {
        return $request->validate([
            'material_type_id'  => ['required', 'exists:material_types,id'],
            'category_id'       => ['required', 'exists:categories,id'],
            'publisher_id'      => ['nullable', 'exists:publishers,id'],
            'publisher_name'    => ['nullable', 'string', 'max:180'],
            'title'             => ['required', 'string', 'max:255'],
            'isbn'              => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('resources', 'isbn')->ignore($resourceId),
            ],
            'publication_year'  => ['nullable', 'integer', 'min:1500', 'max:' . date('Y')],
            'edition'           => ['nullable', 'string', 'max:100'],
            'description'       => ['nullable', 'string', 'max:5000'],
            'cover_image'       => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_reference_only' => ['nullable', 'boolean'],
            'is_digital'        => ['nullable', 'boolean'],
            'digital_url'       => ['nullable', 'url', 'max:1000'],
            'author_names'      => ['nullable', 'string', 'max:2000'],
            'copy_count'        => ['nullable', 'integer', 'min:0', 'max:100'],
            'branch_id'         => ['nullable', 'exists:library_branches,id'],
            'copy_status_id'    => ['nullable', 'exists:copy_statuses,id'],
            'accession_prefix'  => ['nullable', 'string', 'max:40'],
            'barcode_prefix'    => ['nullable', 'string', 'max:40'],
            'shelf_location'    => ['nullable', 'string', 'max:100'],
            'copy_condition'    => ['nullable', 'string', 'max:80'],
            'copy_is_borrowable'=> ['nullable', 'boolean'],
        ]);
    }

    private function findResourceForWebsite(int $id)
    {
        return DB::table('resources as r')
            ->join('material_types as mt', 'r.material_type_id', '=', 'mt.id')
            ->join('categories as c', 'r.category_id', '=', 'c.id')
            ->leftJoin('publishers as p', 'r.publisher_id', '=', 'p.id')
            ->leftJoin('resource_authors as ra', 'r.id', '=', 'ra.resource_id')
            ->leftJoin('authors as a', 'ra.author_id', '=', 'a.id')
            ->where('r.id', $id)
            ->select(
                'r.id',
                'r.title',
                'r.isbn',
                'r.publication_year',
                'r.edition',
                'r.description',
                'r.cover_image_path',
                'r.material_type_id',
                'r.category_id',
                'r.publisher_id',
                'r.is_reference_only',
                'r.is_digital',
                'r.digital_url',
                'r.is_archived',
                'mt.material_type_name',
                'c.category_name',
                'p.publisher_name',
                DB::raw("COALESCE(STRING_AGG(DISTINCT a.author_name, ', '), 'Unknown Author') as authors")
            )
            ->groupBy(
                'r.id',
                'r.title',
                'r.isbn',
                'r.publication_year',
                'r.edition',
                'r.description',
                'r.cover_image_path',
                'r.material_type_id',
                'r.category_id',
                'r.publisher_id',
                'r.is_reference_only',
                'r.is_digital',
                'r.digital_url',
                'r.is_archived',
                'mt.material_type_name',
                'c.category_name',
                'p.publisher_name'
            )
            ->first();
    }

    private function resolvePublisherId(?int $publisherId, ?string $publisherName): ?int
    {
        if ($publisherId) {
            return $publisherId;
        }

        if (!$publisherName) {
            return null;
        }

        $existingId = DB::table('publishers')
            ->where('publisher_name', $publisherName)
            ->value('id');

        if ($existingId) {
            return $existingId;
        }

        return DB::table('publishers')->insertGetId([
            'publisher_key'  => $this->uniqueKey('publishers', 'publisher_key', $publisherName),
            'publisher_name' => $publisherName,
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);
    }

    private function syncAuthors(int $resourceId, ?string $authorNames): void
    {
        $names = collect(preg_split('/[\r\n,]+/', $authorNames ?? ''))
            ->map(fn ($name) => trim($name))
            ->filter()
            ->unique()
            ->values();

        DB::table('resource_authors')
            ->where('resource_id', $resourceId)
            ->delete();

        foreach ($names as $name) {
            $authorId = DB::table('authors')
                ->where('author_name', $name)
                ->value('id');

            if (!$authorId) {
                $authorId = DB::table('authors')->insertGetId([
                    'author_key'  => $this->uniqueKey('authors', 'author_key', $name),
                    'author_name' => $name,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }

            DB::table('resource_authors')->insertOrIgnore([
                'resource_id' => $resourceId,
                'author_id'   => $authorId,
            ]);
        }
    }

    private function createCopies(int $resourceId, array $validated, Request $request): void
    {
        $copyCount = (int) ($validated['copy_count'] ?? 0);

        if ($copyCount <= 0) {
            return;
        }

        if (empty($validated['branch_id']) || empty($validated['copy_status_id'])) {
            throw ValidationException::withMessages([
                'copy_count' => 'Branch and copy status are required when adding copies.',
            ]);
        }

        $isBorrowable     = $request->has('copy_is_borrowable')
            ? $request->boolean('copy_is_borrowable')
            : true;
        $accessionPrefix  = strtoupper($validated['accession_prefix'] ?? 'ACC');
        $barcodePrefix    = strtoupper($validated['barcode_prefix'] ?? 'BC');

        for ($i = 1; $i <= $copyCount; $i++) {
            $uniquePart = $resourceId . '-' . now()->format('YmdHis') . '-' . str_pad((string) $i, 3, '0', STR_PAD_LEFT);

            DB::table('resource_copies')->insert([
                'resource_id'      => $resourceId,
                'branch_id'        => $validated['branch_id'],
                'copy_status_id'   => $validated['copy_status_id'],
                'accession_number' => $accessionPrefix . '-' . $uniquePart,
                'barcode'          => $barcodePrefix . '-' . $uniquePart,
                'shelf_location'   => $validated['shelf_location'] ?? null,
                'is_borrowable'    => $isBorrowable,
                'copy_condition'   => $validated['copy_condition'] ?? 'Good',
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);
        }
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