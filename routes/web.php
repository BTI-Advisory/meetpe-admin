<?php

use App\Http\Controllers\Admin\AdminGuideController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\StripeConnectController;
use Illuminate\Support\Facades\Route;
use App\Exports\ExperiencesExport;
use App\Exports\GuidesExport;
use App\Exports\VoyageursExport;
use App\Exports\ReservationsExport;
use App\Exports\AvisExport;
use Maatwebsite\Excel\Facades\Excel;

Route::get('/', fn () => redirect('/admin'));

// Callbacks Stripe Connect (accessibles sans authentification admin — Stripe redirige les guides ici)
Route::get('stripe/connect/refresh/{accountId}', [StripeConnectController::class, 'refresh'])->name('account.refresh');
Route::get('stripe/connect/return/{accountId}', [StripeConnectController::class, 'return'])->name('account.return');

Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('login', [AuthenticatedSessionController::class, 'store']);

Route::middleware(['auth', 'admin'])->group(function () {

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    // Exports
    Route::get('/export-experiences/{status}', fn ($s) => Excel::download(new ExperiencesExport($s), "experiences-$s.xlsx"))->name('export.experiences');
    Route::get('/export-guides', fn () => Excel::download(new GuidesExport(), 'guides.xlsx'))->name('export.guides');
    Route::get('/export-voyageurs', fn () => Excel::download(new VoyageursExport(), 'voyageurs.xlsx'))->name('export.voyageurs');
    Route::get('/export-reservations', fn () => Excel::download(new ReservationsExport(), 'reservations.xlsx'))->name('export.reservations');
    Route::get('/export-avis/{userId}', fn ($id) => Excel::download(new AvisExport($id), "avis-$id.xlsx"))->name('export.avis');

    // Stripe
    Route::get("make-stripe-account-guide/{user_id}", [AdminGuideController::class, "createStripeAccountForGuide"]);

    // Guides
    Route::post("guide/update-status/{id}", [AdminGuideController::class, "UpdateStatus"])->name("UpdateStatus");
    Route::post("guide/add-comment/{id}", [AdminGuideController::class, "addComment"])->name("addComment");
    Route::post("guide/add", [AdminGuideController::class, "createGuide"])->name("createGuide");
    Route::get("guide-experience/add/{id}", [AdminGuideController::class, "add_GuideExperience"])->name("add_GuideExperience");
    Route::get("guides/add", fn () => view("dashboard.add-guide"))->name("add-guide");
    Route::get("guide-experience/{index?}", [AdminGuideController::class, "getGuideExperiences"])->name("getGuideExperiences");
    Route::get("guide-experience-non-complete/{index?}", [AdminGuideController::class, "getNonCompleted"])->name("getNonCompleted");
    Route::get("guide-experience-online/{index?}", [AdminGuideController::class, "getOnlineExperiences"])->name("getOnlineExperiences");
    Route::get("guide-experience-rejected/{index?}", [AdminGuideController::class, "getRejectedGuidExperiences"])->name("getRejectedGuidExperiences");
    Route::get("guide-experience-archived/{index?}", [AdminGuideController::class, "getArchivedGuidExperiences"])->name("getArchivedGuidExperiences");
    Route::get("guide-experience-horsligne/{index?}", [AdminGuideController::class, "getHorsLigneGuidExperiences"])->name("getHorsLigneGuidExperiences");
    Route::get("guide-experience-deleted/{index?}", [AdminGuideController::class, "getDeletedExperiences"])->name("getDeletedExperiences");
    Route::get("guide-experience-another-document/{index?}", [AdminGuideController::class, "another_document"])->name("another_document");

    // Voyageurs & réservations
    Route::get('reservations/{index?}', [AdminGuideController::class, "getAllReservations"])->name("getAllreservations");
    Route::get('Voyageurs/{index?}', [AdminGuideController::class, "getAllVoyageurs"])->name("getAllVoyageurs");
    Route::get('Voyageur/{UserId}', [AdminGuideController::class, "getVoyageursDetails"])->name("getVoyageursDetails");
    Route::get("guides/{index?}", [AdminGuideController::class, "getGuide"])->name("getGuide");
    Route::get("guidesFiles/{user_id}", [AdminGuideController::class, "guidesFiles"])->name("guidesFiles");
});
