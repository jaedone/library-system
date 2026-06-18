<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BorrowEligibilityService
{
    public function getBlockReason(): ?string
    {
        if (!Auth::check()) {
            return 'Sign in Required';
        }

        $hasActivePenalty = DB::table('penalties as p')
            ->leftJoin('penalty_statuses as ps', 'p.penalty_status_id', '=', 'ps.id')
            ->where('p.user_id', Auth::id())
            ->whereIn('ps.status_key', [
                'unpaid',
                'pending',
            ])
            ->exists();

        if ($hasActivePenalty) {
            return 'Settle Penalties First';
        }

        return null;
    }
}