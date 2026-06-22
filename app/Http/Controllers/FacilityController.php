<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class FacilityController extends Controller
{
    public function index()
    {
        $facilities = DB::table('facilities')
            ->where('is_active', true)
            ->orderBy('facility_name')
            ->get()
            ->map(function ($facility) {
                $facility->equipment = $facility->equipment
                    ? json_decode($facility->equipment, true)
                    : [];

                $facility->usage_for = $facility->usage_for
                    ? json_decode($facility->usage_for, true)
                    : [];

                return $facility;
            });

        return view('facilities.index', compact('facilities'));
    }
}