<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class WebsiteInformationController extends Controller
{
    public function index()
    {
        $announcements = DB::table('announcements')
            ->orderBy('is_archived')
            ->orderBy('sort_order')
            ->orderByDesc('published_at')
            ->get();

        $resources = DB::table('resources as r')
            ->leftJoin('categories as c', 'r.category_id', '=', 'c.id')
            ->leftJoin('material_types as mt', 'r.material_type_id', '=', 'mt.id')
            ->leftJoin('publishers as p', 'r.publisher_id', '=', 'p.id')
            ->leftJoin('resource_authors as ra', 'r.id', '=', 'ra.resource_id')
            ->leftJoin('authors as a', 'ra.author_id', '=', 'a.id')
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
                'c.category_name',
                'mt.material_type_name',
                'p.publisher_name',
                DB::raw("COALESCE(STRING_AGG(DISTINCT a.author_name, ', '), '') as authors")
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
                'c.category_name',
                'mt.material_type_name',
                'p.publisher_name'
            )
            ->orderBy('r.is_archived')
            ->orderByDesc('r.created_at')
            ->get();

        $facilities = DB::table('facilities')
            ->orderBy('is_archived')
            ->orderByDesc('is_active')
            ->orderBy('facility_name')
            ->get()
            ->map(function ($facility) {
                $facility->equipment_text = implode(', ', json_decode($facility->equipment ?? '[]', true) ?: []);
                $facility->usage_for_text = implode(', ', json_decode($facility->usage_for ?? '[]', true) ?: []);

                return $facility;
            });

        return view('admin.website.index', [
            'announcements' => $announcements,
            'resources' => $resources,
            'facilities' => $facilities,

            'categories' => DB::table('categories')->orderBy('category_name')->get(),
            'materialTypes' => DB::table('material_types')->orderBy('material_type_name')->get(),
            'publishers' => DB::table('publishers')->orderBy('publisher_name')->get(),
            'libraryBranches' => DB::table('library_branches')->orderBy('branch_name')->get(),
            'copyStatuses' => DB::table('copy_statuses')->orderBy('status_name')->get(),
        ]);
    }
}