<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Services\BorrowEligibilityService;

class HomeController extends Controller
{
    public function index(BorrowEligibilityService $borrowEligibility)
    {
        $borrowBlockReason = $borrowEligibility->getBlockReason();

        $hero = [
            'background_image' => '/images/auth-bg/bg1.jpg',
            'youtube_video_id' => 'aTPmw5Nva-Q',
        ];

        $announcements = collect([
            [
                'icon' => 'bi-calendar-event',
                'title' => 'Library Holiday Schedule',
                'description' => 'Check library operating days and holiday closures.',
            ],
            [
                'icon' => 'bi-journal-plus',
                'title' => 'New Books Available',
                'description' => 'New academic and reference materials are now available.',
            ],
            [
                'icon' => 'bi-megaphone',
                'title' => 'Library Services Advisories',
                'description' => 'Stay updated on borrowing, reservation, and facility services.',
            ],
            [
                'icon' => 'bi-building-exclamation',
                'title' => 'Facility Closure Notice',
                'description' => 'View temporary room or facility closure announcements.',
            ],
        ]);

        $services = collect([
            [
                'icon' => 'bi-book',
                'title' => 'Book Borrowing',
                'description' => 'Borrow eligible library materials.',
                'url' => url('/services'),
            ],
            [
                'icon' => 'bi-bookmark-check',
                'title' => 'Book Reservation',
                'description' => 'Reserve available books online.',
                'url' => url('/services'),
            ],
            [
                'icon' => 'bi-envelope-paper',
                'title' => 'Referral Letter Request',
                'description' => 'Request referral letters for other libraries.',
                'url' => url('/services'),
            ],
            [
                'icon' => 'bi-building',
                'title' => 'Facility Reservation',
                'description' => 'Reserve library rooms and tables.',
                'url' => url('/facilities'),
            ],
            [
                'icon' => 'bi-globe',
                'title' => 'Online Resources',
                'description' => 'Access authorized digital resources.',
                'url' => url('/catalog'),
            ],
        ]);

        $newArrivals = $this->getBooksQuery()
            ->orderByDesc('r.created_at')
            ->limit(8)
            ->get();

        $mostBorrowed = $this->getBooksQuery()
            ->orderByDesc('borrow_count')
            ->limit(8)
            ->get();

        $recommendedBooks = $this->getBooksQuery()
            ->inRandomOrder()
            ->limit(8)
            ->get();
        
        return view('home.index', compact(
            'borrowBlockReason',
            'hero',
            'announcements',
            'services',
            'newArrivals',
            'mostBorrowed',
            'recommendedBooks'
        ));
    }

    private function getBooksQuery()
    {
        return DB::table('resources as r')
            ->leftJoin('resource_authors as ra', 'r.id', '=', 'ra.resource_id')
            ->leftJoin('authors as a', 'ra.author_id', '=', 'a.id')
            ->leftJoin('material_types as mt', 'r.material_type_id', '=', 'mt.id')
            ->leftJoin('categories as c', 'r.category_id', '=', 'c.id')
            ->leftJoin('resource_copies as rc', 'r.id', '=', 'rc.resource_id')
            ->leftJoin('copy_statuses as cs', 'rc.copy_status_id', '=', 'cs.id')
            ->leftJoin('library_branches as lb', 'rc.branch_id', '=', 'lb.id')
            ->leftJoin('borrow_transactions as bt', 'rc.id', '=', 'bt.copy_id')
            ->select(
                'r.id',
                'r.title',
                'r.description',
                'r.cover_image_path',
                'r.created_at',
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
                DB::raw("COUNT(DISTINCT rc.id) as total_copies"),
                DB::raw("COALESCE(SUM(CASE WHEN cs.status_key = 'available' THEN 1 ELSE 0 END), 0) as available_copies"),
                DB::raw("COUNT(DISTINCT bt.id) as borrow_count")
            )
            ->groupBy(
                'r.id',
                'r.title',
                'r.description',
                'r.cover_image_path',
                'r.created_at',
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