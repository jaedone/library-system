<?php

namespace App\Http\Controllers;

use App\Services\BorrowEligibilityService;
use App\Support\PublicWebsiteContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CatalogController extends Controller
{
    public function index(Request $request, BorrowEligibilityService $borrowEligibility)
    {
        $borrowBlockReason = $borrowEligibility->getBlockReason();

        $categories = DB::table('categories')
            ->orderBy('category_name')
            ->get();

        $materialTypes = DB::table('material_types')
            ->orderBy('material_type_name')
            ->get();

        $copyStatuses = DB::table('copy_statuses')
            ->orderBy('status_name')
            ->get();

        $branches = DB::table('library_branches')
            ->orderBy('branch_name')
            ->get();

        $resources = $this->baseResourcesQuery()
            ->orderByDesc('r.created_at')
            ->get();

        $catalogResources = $resources
            ->map(function ($resource) {
                return [
                    'id' => $resource->id,
                    'title' => $resource->title,
                    'isbn' => $resource->isbn,
                    'description' => $resource->description,
                    'publication_year' => $resource->publication_year,
                    'category_id' => $resource->category_id,
                    'category_name' => $resource->category_name,
                    'material_type_id' => $resource->material_type_id,
                    'material_type_name' => $resource->material_type_name,
                    'authors' => $resource->authors,
                    'library_locations' => $resource->library_locations,
                    'availability_statuses' => $resource->availability_statuses,
                    'copy_status_ids' => $resource->copy_status_ids,
                    'branch_ids' => $resource->branch_ids,

                    'total_copies' => $resource->total_copies,
                    'available_copies' => $resource->available_copies,

                    'cover_url' => !empty($resource->cover_image_path)
                        ? (\Illuminate\Support\Str::startsWith($resource->cover_image_path, ['http://', 'https://'])
                            ? $resource->cover_image_path
                            : (\Illuminate\Support\Str::startsWith($resource->cover_image_path, ['storage/'])
                                ? asset($resource->cover_image_path)
                                : asset('storage/' . $resource->cover_image_path)))
                        : asset('images/auth-bg/bg4.jpg'),
                ];
            })
            ->values();

        return view('catalog.index', compact(
            'borrowBlockReason',
            'categories',
            'materialTypes',
            'copyStatuses',
            'branches',
            'resources',
            'catalogResources'
        ));
    }

    private function baseResourcesQuery()
    {
        $resources = DB::table('resources as r')
            ->leftJoin('resource_authors as ra', 'r.id', '=', 'ra.resource_id')
            ->leftJoin('authors as a', 'ra.author_id', '=', 'a.id')
            ->leftJoin('material_types as mt', 'r.material_type_id', '=', 'mt.id')
            ->leftJoin('categories as c', 'r.category_id', '=', 'c.id')
            ->leftJoin('resource_copies as rc', 'r.id', '=', 'rc.resource_id')
            ->leftJoin('copy_statuses as cs', 'rc.copy_status_id', '=', 'cs.id')
            ->leftJoin('library_branches as lb', 'rc.branch_id', '=', 'lb.id')
            ->leftJoin('borrow_transactions as bt', 'rc.id', '=', 'bt.copy_id');

        PublicWebsiteContent::onlyVisibleResources($resources, 'r');

        return $resources
            ->select(
                'r.id',
                'r.title',
                'r.isbn',
                'r.description',
                'r.cover_image_path',
                'r.publication_year',
                'r.is_reference_only',
                'r.is_digital',
                'r.digital_url',
                'r.category_id',
                'r.material_type_id',
                'mt.material_type_name',
                'c.category_name',
                DB::raw("COALESCE(STRING_AGG(DISTINCT a.author_name, ', '), 'Unknown Author') as authors"),
                DB::raw("COALESCE(STRING_AGG(DISTINCT lb.branch_name, ', '), 'No location assigned') as library_locations"),
                DB::raw("COALESCE(STRING_AGG(DISTINCT cs.status_name, ', '), 'No copies available') as availability_statuses"),
                DB::raw("COALESCE(STRING_AGG(DISTINCT cs.id::text, ','), '') as copy_status_ids"),
                DB::raw("COALESCE(STRING_AGG(DISTINCT lb.id::text, ','), '') as branch_ids"),
                DB::raw("COUNT(DISTINCT rc.id) as total_copies"),
                DB::raw("COALESCE(SUM(CASE WHEN cs.status_key = 'available' THEN 1 ELSE 0 END), 0) as available_copies"),
                DB::raw("COUNT(DISTINCT bt.id) as borrow_count")
            )
            ->groupBy(
                'r.id',
                'r.title',
                'r.isbn',
                'r.description',
                'r.cover_image_path',
                'r.publication_year',
                'r.is_reference_only',
                'r.is_digital',
                'r.digital_url',
                'r.category_id',
                'r.material_type_id',
                'mt.material_type_name',
                'c.category_name'
            );
    }
}
