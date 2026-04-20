<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\DossierRelationController;
use App\Http\Controllers\Api\V1\ServiceRequestController;
use Illuminate\Support\Facades\Route;

// API v1 Routes
Route::prefix('v1')->name('api.v1.')->group(function () {
    // ==================== PUBLIC ROUTES ====================

    // Authentication (Public)
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
    Route::post('/register', [AuthController::class, 'register'])->name('auth.register');

    // ==================== PROTECTED ROUTES ====================

    // Custom token authentication using auth-token middleware
    Route::middleware('auth.token')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
        Route::post('/refresh', [AuthController::class, 'refresh'])->name('auth.refresh');
        Route::get('/me', [AuthController::class, 'me'])->name('auth.me');
        Route::get('/verify', [AuthController::class, 'verify'])->name('auth.verify');
        Route::put('/profile', [AuthController::class, 'updateProfile'])->name('auth.profile.update');

        // Dossier médical
        Route::post('/dossier/sync', [AuthController::class, 'syncDossier'])->name('dossier.sync');
        Route::get('/dossier', [AuthController::class, 'getDossier'])->name('dossier.get');
        Route::put('/dossier', [AuthController::class, 'updateDossier'])->name('dossier.update');
        Route::post('/dossier/photo', [AuthController::class, 'uploadPhoto'])->name('dossier.photo');

        // Déclarations de relations
        Route::get('/dossier/relations', [DossierRelationController::class, 'getRelations'])->name('dossier.relations');
        Route::post('/dossier/personne-a-charge', [DossierRelationController::class, 'declarePersonneACharge'])->name('dossier.personne-a-charge');
        Route::post('/dossier/employeur', [DossierRelationController::class, 'declareEmployeur'])->name('dossier.employeur');
        Route::post('/dossier/standalone', [DossierRelationController::class, 'declareStandalone'])->name('dossier.standalone');

        // Gestion des personnes à charge
        Route::get('/dossier/dependents', [DossierRelationController::class, 'getDependents'])->name('dossier.dependents');
        Route::post('/dossier/dependents', [DossierRelationController::class, 'addDependent'])->name('dossier.dependents.add');
        Route::delete('/dossier/dependents', [DossierRelationController::class, 'removeDependent'])->name('dossier.dependents.remove');

        // Stats subscription
        Route::get('/dossier/subscription-stats', [DossierRelationController::class, 'getSubscriptionStats'])->name('dossier.subscription-stats');

        // Notifications
        Route::post('/notifications/register', [AuthController::class, 'registerDeviceToken'])->name('notifications.register');
        Route::get('/notifications', [AuthController::class, 'getNotifications'])->name('notifications.list');
        Route::patch('/notifications/{id}/read', [AuthController::class, 'markNotificationRead'])->name('notifications.read');
        Route::patch('/notifications/read-all', [AuthController::class, 'markAllNotificationsRead'])->name('notifications.read-all');

        // Service requests
        Route::get('/services', [ServiceRequestController::class, 'getServices'])->name('services.list');
        Route::get('/services/my-requests', [ServiceRequestController::class, 'getMyRequests'])->name('services.my-requests');
        Route::get('/services/dependents', [ServiceRequestController::class, 'getDependents'])->name('services.dependents');
        Route::get('/services/quota', [ServiceRequestController::class, 'getQuotaUsage'])->name('services.quota');
        Route::post('/services/request', [ServiceRequestController::class, 'createRequest'])->name('services.request');
        Route::get('/services/request/{id}', [ServiceRequestController::class, 'getDetail'])->name('services.detail');
        Route::get('/rendezvous', [ServiceRequestController::class, 'getMyRendezVous'])->name('services.rendezvous');
    });

});
