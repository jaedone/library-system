<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\WebsiteInformationController;
use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\AdminResourceController;
use App\Http\Controllers\Admin\AdminFacilityController;
use App\Http\Controllers\Admin\ServiceManagementController;
use App\Http\Controllers\ErrorController;
use App\Http\Controllers\SiteSearchController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/login', [AuthController::class, 'showLoginRegister'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

Route::get('/register', [AuthController::class, 'showLoginRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog.index');

Route::get('/services', [ServiceController::class, 'index'])
    ->name('services.index');

Route::get('/facilities', [FacilityController::class, 'index'])
    ->name('facilities.index');

Route::get('/search', [SiteSearchController::class, 'redirect'])
    ->name('site.search');

Route::middleware('auth')->group(function () {
    Route::get('/account/profile', [ProfileController::class, 'show'])
        ->name('account.profile');

    Route::put('/account/profile', [ProfileController::class, 'update'])
        ->name('account.profile.update');

    Route::get('/account/password', [ProfileController::class, 'editPassword'])
        ->name('account.password.edit');

    Route::put('/account/password', [ProfileController::class, 'updatePassword'])
        ->name('account.password.update');

    Route::put('/account/profile/photo', [ProfileController::class, 'updatePhoto'])
        ->name('account.profile.photo.update');

    Route::get('/services/facility-availability', [ServiceController::class, 'facilityAvailability'])
        ->name('services.facility-availability');

    Route::get('/services/{service}', [ServiceController::class, 'show'])
        ->name('services.show');

    Route::post('/services/{service}', [ServiceController::class, 'store'])
        ->name('services.store');
});


Route::middleware('auth')->group(function () {
    Route::prefix('admin/member-management')
        ->name('admin.members.')
        ->group(function () {
            Route::get('/', [UserManagementController::class, 'index'])->name('index');
            Route::put('/{user}', [UserManagementController::class, 'update'])->name('update');
            Route::delete('/{user}', [UserManagementController::class, 'destroy'])->name('destroy');
            Route::post('/{user}/ban-service', [UserManagementController::class, 'banService'])->name('ban-service');
            Route::delete('/{user}/ban-service/{restriction}', [UserManagementController::class, 'unbanService'])->name('unban-service');
        });

    Route::prefix('admin/website-information')
        ->name('admin.website.')
        ->group(function () {
            Route::get('/', [WebsiteInformationController::class, 'index'])->name('index');

            Route::resource('announcements', AnnouncementController::class)->names('announcements');
            Route::resource('resources', AdminResourceController::class)->names('resources');
            Route::resource('facilities', AdminFacilityController::class)->names('facilities');

            Route::patch('announcements/{announcement}/archive', [AnnouncementController::class, 'archive'])->name('announcements.archive');
            Route::patch('resources/{resource}/archive', [AdminResourceController::class, 'archive'])->name('resources.archive');
            Route::patch('facilities/{facility}/archive', [AdminFacilityController::class, 'archive'])->name('facilities.archive');
        });

    Route::prefix('admin/services-management')
        ->name('admin.services-management.')
        ->group(function () {
            Route::get('/', [ServiceManagementController::class, 'index'])->name('index');
            Route::patch('/{serviceKey}/{requestId}/review', [ServiceManagementController::class, 'review'])->name('review');
            Route::patch('/{serviceKey}/{requestId}/approve', [ServiceManagementController::class, 'approve'])->name('approve');
            Route::patch('/{serviceKey}/{requestId}/reject', [ServiceManagementController::class, 'reject'])->name('reject');
            Route::patch('/{serviceKey}/{requestId}/confirm-return', [ServiceManagementController::class, 'confirmReturn'])->name('confirm-return');
            Route::patch('/{serviceKey}/{requestId}/follow-up', [ServiceManagementController::class, 'followUp'])->name('follow-up');
            Route::post('/{serviceKey}/{requestId}/penalty', [ServiceManagementController::class, 'addPenalty'])->name('penalty');
            Route::post('/{serviceKey}/{requestId}/send-letter', [ServiceManagementController::class, 'sendReferralLetter'])->name('send-letter');
        });
});

Route::fallback([ErrorController::class, 'notFound']);