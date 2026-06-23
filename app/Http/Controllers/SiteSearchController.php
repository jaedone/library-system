<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class SiteSearchController extends Controller
{
    public function redirect(Request $request)
    {
        $query = trim($request->query('q', ''));
        $search = Str::lower($query);

        if ($search === '') {
            return redirect('/');
        }

        $isAdmin = $this->isAdminUser();

        if ($isAdmin) {
            if (Str::contains($search, ['website', 'announcement', 'announcements', 'resources', 'resource manager', 'facility manager'])) {
                return redirect(Route::has('admin.website.index')
                    ? route('admin.website.index')
                    : url('/admin/website-information'));
            }

            if (Str::contains($search, ['service management', 'services management', 'requests', 'borrow request', 'reservation request', 'renewal request', 'return request'])) {
                return redirect(Route::has('admin.services-management.index')
                    ? route('admin.services-management.index', ['search' => $query])
                    : url('/admin/services-management?search=' . urlencode($query)));
            }

            if (Str::contains($search, ['user', 'member', 'members', 'student', 'faculty', 'employee', 'account'])) {
                return redirect(Route::has('admin.members.index')
                    ? route('admin.members.index', ['search' => $query])
                    : url('/admin/members?search=' . urlencode($query)));
            }
        }

        if (Str::contains($search, ['book borrowing', 'borrow book', 'borrowing', 'borrow'])) {
            return redirect(Route::has('services.show')
                ? route('services.show', 'book-borrowing')
                : url('/services/book-borrowing'));
        }

        if (Str::contains($search, ['book reservation', 'reserve book', 'reservation book'])) {
            return redirect(Route::has('services.show')
                ? route('services.show', 'book-reservation')
                : url('/services/book-reservation'));
        }

        if (Str::contains($search, ['book renewal', 'renew book', 'renewal'])) {
            return redirect(Route::has('services.show')
                ? route('services.show', 'book-renewal')
                : url('/services/book-renewal'));
        }

        if (Str::contains($search, ['book return', 'return book', 'return'])) {
            return redirect(Route::has('services.show')
                ? route('services.show', 'book-return')
                : url('/services/book-return'));
        }

        if (Str::contains($search, ['referral', 'referral letter', 'request letter'])) {
            return redirect(Route::has('services.show')
                ? route('services.show', 'referral-letter')
                : url('/services/referral-letter'));
        }

        if (Str::contains($search, ['facility reservation', 'reserve facility', 'room reservation'])) {
            return redirect(Route::has('services.show')
                ? route('services.show', 'facility-reservation')
                : url('/services/facility-reservation'));
        }

        if (Str::contains($search, ['facility', 'facilities', 'room', 'discussion room', 'table', 'desk'])) {
            return redirect(url('/facilities'));
        }

        if (Str::contains($search, ['service', 'services'])) {
            return redirect(url('/services'));
        }

        if (Str::contains($search, ['home', 'about', 'library', 'website'])) {
            return redirect(url('/'));
        }

        return redirect(url('/catalog?q=' . urlencode($query)));
    }

    private function isAdminUser(): bool
    {
        $user = Auth::user();

        if (!$user || !isset($user->role_id)) {
            return false;
        }

        $role = \Illuminate\Support\Facades\DB::table('roles')
            ->where('id', $user->role_id)
            ->first();

        if (!$role) {
            return false;
        }

        $roleName = Str::lower($role->role_name ?? '');
        $displayName = Str::lower($role->display_role_name ?? '');

        return in_array($roleName, [
            'admin',
            'staff',
            'library_staff',
            'librarian',
            'super_admin',
        ], true)
        || Str::contains($displayName, ['admin', 'staff', 'librarian']);
    }
}