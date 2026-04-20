<?php

use App\Http\Controllers\ControleClientController;
use App\Http\Controllers\ControleClientDocumentController;
use App\Http\Controllers\DemandeServiceController;
use App\Http\Controllers\DossierMedicalController;
use App\Http\Controllers\DossierProfessionnelController;
use App\Http\Controllers\FraisController;
use App\Http\Controllers\FraisInscriptionController;
use App\Http\Controllers\PaiementController;
use App\Http\Controllers\ProfessionalWorkspaceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RendezVousController;
use App\Http\Controllers\ServiceMedicalController;
use App\Http\Controllers\ServiceProfessionnelController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\SubscriptionProfessionnelleController;
use App\Http\Controllers\TauxReductionController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\UserPatientPortalController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Route publique pour scanner le QR code de la carte médicale
Route::get('/carte-medicale/scan/{code}', [DossierMedicalController::class, 'carteScan'])->name('carte-medicale.scan');

Route::get('/dashboard', [UserPatientPortalController::class, 'dashboard'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Parcours self-service utilisateur
    Route::get('/adherer', [DossierMedicalController::class, 'createSelf'])->name('user.adherer.create');
    Route::post('/adherer', [DossierMedicalController::class, 'storeSelf'])->name('user.adherer.store');
    Route::get('/adherer/{dossierMedical}/paiement', [DossierMedicalController::class, 'paymentForm'])
        ->name('user.adherer.payment');
    Route::post('/adherer/{dossierMedical}/paiement', [DossierMedicalController::class, 'processPayment'])
        ->name('user.adherer.payment.process');
    Route::get('/mon-profil-medical', [DossierMedicalController::class, 'medicalProfile'])
        ->name('user.medical-profile.edit');
    Route::patch('/mon-profil-medical', [DossierMedicalController::class, 'updateMedicalProfile'])
        ->name('user.medical-profile.update');

    Route::get('/devenir-professionnel', [DossierProfessionnelController::class, 'createSelf'])
        ->name('user.professional.create');
    Route::post('/devenir-professionnel', [DossierProfessionnelController::class, 'storeSelf'])
        ->name('user.professional.store');
    Route::get('/devenir-professionnel/{dossierProfessionnel}/paiement', [DossierProfessionnelController::class, 'paymentForm'])
        ->name('user.professional.payment');
    Route::post('/devenir-professionnel/{dossierProfessionnel}/paiement', [DossierProfessionnelController::class, 'processPayment'])
        ->name('user.professional.payment.process');
    Route::get('/mon-profil-professionnel', [DossierProfessionnelController::class, 'professionalProfile'])
        ->name('user.professional.profile');
    Route::get('/mes-profils/validation', [UserPatientPortalController::class, 'validationStatus'])
        ->name('user.validation.status');

    // Prise de rendez-vous — parcours patient
    Route::get('/rendez-vous', [RendezVousController::class, 'index'])->name('rendez-vous.index');
    Route::post('/rendez-vous', [RendezVousController::class, 'store'])->name('rendez-vous.store');

    Route::prefix('espace-patient')->name('patient.')->group(function () {
        Route::get('/paiements', [UserPatientPortalController::class, 'payments'])->name('payments.index');
        Route::patch('/paiements/{factureProfessionnelle}/payer', [UserPatientPortalController::class, 'payPersonally'])->name('payments.pay');
        Route::patch('/paiements/{factureProfessionnelle}/soumettre-backoffice', [UserPatientPortalController::class, 'submitToBackoffice'])->name('payments.submit-backoffice');
        Route::patch('/paiements/{factureProfessionnelle}/annuler-backoffice', [UserPatientPortalController::class, 'cancelBackofficeSubmission'])->name('payments.cancel-backoffice');
        Route::post('/reabonnement/patient', [UserPatientPortalController::class, 'renewMedicalSubscription'])->name('subscriptions.renew-medical');
        Route::get('/reabonnement/patient/statut', [UserPatientPortalController::class, 'medicalRenewalPaymentStatus'])->name('subscriptions.renew-medical.status');
        Route::post('/reabonnement/tout-solder', [UserPatientPortalController::class, 'renewAllSubscriptions'])->name('subscriptions.renew-all');
        Route::post('/reabonnement/professionnel', [UserPatientPortalController::class, 'renewProfessionalSubscription'])->name('subscriptions.renew-professional');
        Route::get('/finances', [UserPatientPortalController::class, 'finances'])->name('finances.index');
        Route::get('/rendez-vous', [UserPatientPortalController::class, 'appointments'])->name('appointments.index');
        Route::get('/documents', [UserPatientPortalController::class, 'documents'])->name('documents.index');
        Route::post('/documents/ordonnances/{ordonnanceProfessionnelle}/analyse-ia', [UserPatientPortalController::class, 'analyzeOrdonnance'])->name('documents.ordonnance.analyze');
        Route::get('/alertes', [UserPatientPortalController::class, 'alerts'])->name('alerts.index');
        Route::get('/alertes/live', [UserPatientPortalController::class, 'liveNotifications'])->name('notifications.live');
        Route::patch('/alertes/notifications/{notification}/lu', [UserPatientPortalController::class, 'markNotificationAsRead'])->name('alerts.read');
        Route::patch('/alertes/notifications/lire-tout', [UserPatientPortalController::class, 'markAllNotificationsAsRead'])->name('alerts.read-all');
    });

    Route::get('/espace-professionnel', [ProfessionalWorkspaceController::class, 'dashboard'])
        ->name('professional.workspace.dashboard');
    Route::get('/espace-professionnel/presentiel', [ProfessionalWorkspaceController::class, 'presentiel'])
        ->name('professional.workspace.presentiel');
    Route::post('/espace-professionnel/presentiel/ouvrir', [ProfessionalWorkspaceController::class, 'startPresentielConsultation'])
        ->name('professional.workspace.presentiel.start');
    Route::get('/espace-professionnel/suivi-patients', [ProfessionalWorkspaceController::class, 'patientsTracking'])
        ->name('professional.workspace.patients.tracking');
    Route::get('/espace-professionnel/finance', [ProfessionalWorkspaceController::class, 'finance'])
        ->name('professional.workspace.finance');
    Route::post('/espace-professionnel/finance/retraits', [ProfessionalWorkspaceController::class, 'requestWithdrawal'])
        ->name('professional.workspace.finance.withdrawal.request');
    Route::patch('/espace-professionnel/finance/factures/{factureProfessionnelle}/payer', [ProfessionalWorkspaceController::class, 'markFactureAsPaid'])
        ->name('professional.workspace.finance.invoice.mark-paid');
    Route::get('/espace-professionnel/repertoire-patients', [ProfessionalWorkspaceController::class, 'patientDirectory'])
        ->name('professional.workspace.patients.directory');
    Route::get('/espace-professionnel/historique-patients', [ProfessionalWorkspaceController::class, 'patientHistory'])
        ->name('professional.workspace.patients.history');
    Route::patch('/espace-professionnel/rendez-vous/{rendezVousProfessionnel}/accepter', [ProfessionalWorkspaceController::class, 'accepterRendezVous'])
        ->name('professional.workspace.rendez-vous.accept');
    Route::patch('/espace-professionnel/rendez-vous/{rendezVousProfessionnel}/decliner', [ProfessionalWorkspaceController::class, 'declinerRendezVous'])
        ->name('professional.workspace.rendez-vous.decline');
    Route::get('/espace-professionnel/consultations/{consultationProfessionnelle}/edit', [ProfessionalWorkspaceController::class, 'editConsultation'])
        ->name('professional.workspace.consultation.edit');
    Route::patch('/espace-professionnel/consultations/{consultationProfessionnelle}', [ProfessionalWorkspaceController::class, 'updateConsultation'])
        ->name('professional.workspace.consultation.update');
    Route::patch('/espace-professionnel/consultations/{consultationProfessionnelle}/suggestion-traitement', [ProfessionalWorkspaceController::class, 'generateTreatmentSuggestion'])
        ->name('professional.workspace.consultation.treatment-suggestion');
    Route::get('/espace-professionnel/consultations/{consultationProfessionnelle}/ordonnance/print', [ProfessionalWorkspaceController::class, 'printOrdonnance'])
        ->name('professional.workspace.consultation.ordonnance.print');
    Route::get('/espace-professionnel/consultations/{consultationProfessionnelle}/print', [ProfessionalWorkspaceController::class, 'printConsultation'])
        ->name('professional.workspace.consultation.print');
    Route::get('/espace-professionnel/consultations/{consultationProfessionnelle}/summary/print', [ProfessionalWorkspaceController::class, 'printSummary'])
        ->name('professional.workspace.consultation.summary.print');
    Route::get('/espace-professionnel/factures/{factureProfessionnelle}/print', [ProfessionalWorkspaceController::class, 'printFacture'])
        ->name('professional.workspace.facture.print');
    Route::get('/espace-professionnel/patients/{dossierMedical}', [ProfessionalWorkspaceController::class, 'patientDossier'])
        ->name('professional.workspace.patient.dossier');
    Route::post('/espace-professionnel/consultations/{consultationProfessionnelle}/documents', [ProfessionalWorkspaceController::class, 'storeDocument'])
        ->name('professional.workspace.consultation.document.store');
    Route::delete('/espace-professionnel/consultations/{consultationProfessionnelle}/documents/{document}', [ProfessionalWorkspaceController::class, 'destroyDocument'])
        ->name('professional.workspace.consultation.document.destroy');
    Route::patch('/espace-professionnel/examens/{examenProfessionnel}/accepter', [ProfessionalWorkspaceController::class, 'accepterExamen'])
        ->name('professional.workspace.examen.accept');

    Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
        Route::get('/soumissions-mutuelle', [\App\Http\Controllers\Admin\SoumissionMutuelleController::class, 'index'])
            ->name('soumissions-mutuelle.index');
        Route::get('/soumissions-mutuelle/{soumissionMutuelle}', [\App\Http\Controllers\Admin\SoumissionMutuelleController::class, 'show'])
            ->name('soumissions-mutuelle.show');
        Route::patch('/soumissions-mutuelle/{soumissionMutuelle}', [\App\Http\Controllers\Admin\SoumissionMutuelleController::class, 'update'])
            ->name('soumissions-mutuelle.update');
        Route::get('/optimization-reports', [\App\Http\Controllers\Admin\OptimizationReportController::class, 'index'])
            ->name('optimization-reports.index');
        Route::get('/optimization-reports/create', [\App\Http\Controllers\Admin\OptimizationReportController::class, 'create'])
            ->name('optimization-reports.create');
        Route::post('/optimization-reports', [\App\Http\Controllers\Admin\OptimizationReportController::class, 'store'])
            ->name('optimization-reports.store');
        Route::get('/optimization-reports/{optimizationReport}', [\App\Http\Controllers\Admin\OptimizationReportController::class, 'show'])
            ->name('optimization-reports.show');
        Route::patch('/optimization-reports/{optimizationReport}/action', [\App\Http\Controllers\Admin\OptimizationReportController::class, 'updateAction'])
            ->name('optimization-reports.update-action');
        Route::delete('/optimization-reports/{optimizationReport}', [\App\Http\Controllers\Admin\OptimizationReportController::class, 'destroy'])
            ->name('optimization-reports.destroy');
    });

    // Routes pour les services professionnels (professionnel propriétaire ou admin)
    Route::prefix('dossier-professionnels/{dossierProfessionnel}/services')->name('services-pro.')->group(function () {
        Route::get('/', [ServiceProfessionnelController::class, 'index'])->name('index');
        Route::get('create', [ServiceProfessionnelController::class, 'create'])->name('create');
        Route::post('/', [ServiceProfessionnelController::class, 'store'])->name('store');
        Route::get('{service}', [ServiceProfessionnelController::class, 'show'])->name('show');
        Route::get('{service}/edit', [ServiceProfessionnelController::class, 'edit'])->name('edit');
        Route::put('{service}', [ServiceProfessionnelController::class, 'update'])->name('update');
        Route::delete('{service}', [ServiceProfessionnelController::class, 'destroy'])->name('destroy');
        Route::patch('{service}/toggle-actif', [ServiceProfessionnelController::class, 'toggleActif'])->name('toggle-actif');
    });

    // Routes pour la gestion des utilisateurs (Admin seulement)
    Route::middleware('admin')->group(function () {
        Route::resource('user-management', UserManagementController::class)
            ->parameters(['user-management' => 'user']);
        Route::patch('user-management/{user}/toggle-status', [UserManagementController::class, 'toggleStatus'])
            ->name('user-management.toggle-status');

        Route::get('paiements/en-ligne', [PaiementController::class, 'onlineHistory'])
            ->name('paiements.online-history');

        // Routes pour le système de taux de réduction
        Route::resource('taux-reductions', TauxReductionController::class);

        // Routes pour les dossiers professionnels (Admin seulement)
        Route::get('dossier-professionnels/en-attente-validation', [DossierProfessionnelController::class, 'pendingValidation'])
            ->name('dossier-professionnels.pending-validation');
        Route::resource('dossier-professionnels', DossierProfessionnelController::class);
        Route::get('dossier-professionnels/{dossierProfessionnel}/verification', [DossierProfessionnelController::class, 'verification'])
            ->name('dossier-professionnels.verification');
        Route::patch('dossier-professionnels/{dossierProfessionnel}/valider', [DossierProfessionnelController::class, 'valider'])
            ->name('dossier-professionnels.valider');
        Route::patch('dossier-professionnels/{dossierProfessionnel}/recaler', [DossierProfessionnelController::class, 'recaler'])
            ->name('dossier-professionnels.recaler');
        Route::patch('dossier-professionnels/{dossierProfessionnel}/remettre', [DossierProfessionnelController::class, 'remettre'])
            ->name('dossier-professionnels.remettre');

        // Routes pour les abonnements professionnels
        Route::prefix('dossier-professionnels/{dossierProfessionnel}/subscriptions')->name('subscriptions-pro.')->group(function () {
            Route::get('/', [SubscriptionProfessionnelleController::class, 'index'])->name('index');
            Route::get('create', [SubscriptionProfessionnelleController::class, 'create'])->name('create');
            Route::post('/', [SubscriptionProfessionnelleController::class, 'store'])->name('store');
            Route::get('{subscription}', [SubscriptionProfessionnelleController::class, 'show'])->name('show');
            Route::patch('{subscription}/cancel', [SubscriptionProfessionnelleController::class, 'cancel'])->name('cancel');
            Route::post('calculer', [SubscriptionProfessionnelleController::class, 'calculer'])->name('calculer');
        });
        Route::get('frais-reabonnement-pro', [SubscriptionProfessionnelleController::class, 'getFraisReabonnement'])
            ->name('subscriptions-pro.frais-reabonnement');
    });

    // Routes pour les frais, frais d'inscription et dossiers médicaux (Admin et Professional)
    Route::middleware('admin_or_professional')->group(function () {
        Route::resource('frais', FraisController::class);
        Route::resource('frais-inscriptions', FraisInscriptionController::class);
        Route::get('dossier-medicals/en-attente-validation', [DossierMedicalController::class, 'pendingValidation'])
            ->middleware('admin')
            ->name('dossier-medicals.pending-validation');
        Route::resource('dossier-medicals', DossierMedicalController::class);

        // Routes pour la carte médicale
        Route::prefix('carte-medicale')->name('carte-medicale.')->group(function () {
            Route::get('/', [DossierMedicalController::class, 'carteIndex'])->name('index');
            Route::get('search', [DossierMedicalController::class, 'carteSearch'])->name('search');
            Route::get('{dossierMedical}/demande', [DossierMedicalController::class, 'carteDemande'])->name('demande');
            Route::get('{dossierMedical}/generer', [DossierMedicalController::class, 'carteGenerer'])->name('generer');
            Route::get('{dossierMedical}/imprimer', [DossierMedicalController::class, 'carteImprimer'])->name('imprimer');
        });

        Route::resource('paiements', PaiementController::class);
        Route::patch('paiements/{paiement}/confirm', [PaiementController::class, 'confirmPayment'])->name('paiements.confirm');
        Route::get('paiements/{paiement}/pdf', [PaiementController::class, 'pdf'])->name('paiements.pdf');
        Route::get('paiements/check-expired', [PaiementController::class, 'checkExpiredPaiements'])->name('paiements.check-expired');

        // Routes pour les services médicaux
        Route::resource('services-medicaux', ServiceMedicalController::class)->parameters([
            'services-medicaux' => 'service',
        ]);

        // Routes pour les demandes de services
        Route::get('demandes-services', [DemandeServiceController::class, 'index'])->name('demandes-services.index');
        Route::get('demandes-services/en-attente', [DemandeServiceController::class, 'enAttente'])->name('demandes-services.en-attente');
        Route::get('demandes-services/{demande}', [DemandeServiceController::class, 'show'])->name('demandes-services.show');
        Route::get('demandes-services/{demande}/edit', [DemandeServiceController::class, 'edit'])->name('demandes-services.edit');
        Route::patch('demandes-services/{demande}/valider', [DemandeServiceController::class, 'valider'])->name('demandes-services.valider');
        Route::patch('demandes-services/{demande}/rejeter', [DemandeServiceController::class, 'rejeter'])->name('demandes-services.rejeter');
        Route::patch('demandes-services/{demande}/terminer', [DemandeServiceController::class, 'terminer'])->name('demandes-services.terminer');
        Route::post('demandes-services/{demande}/piece-jointe', [DemandeServiceController::class, 'storePieceJointe'])->name('demandes-services.piece-jointe');
        Route::post('demandes-services/{demande}/rendez-vous', [DemandeServiceController::class, 'storeRendezVous'])->name('demandes-services.rendez-vous');
        Route::post('demandes-services/{demande}/facture', [DemandeServiceController::class, 'storeFacture'])->name('demandes-services.facture');

        // Routes pour le contrôle et renseignement client
        Route::get('controle-client', [ControleClientController::class, 'index'])->name('controle-client.index');
        Route::get('controle-client/search', [ControleClientController::class, 'search'])->name('controle-client.search');
        Route::get('controle-client/{id}', [ControleClientController::class, 'details'])->name('controle-client.details');
        Route::get('controle-client/{dossierMedical}/documents', [ControleClientDocumentController::class, 'show'])
            ->name('controle-client.documents.show');
        Route::patch('controle-client/{dossierMedical}/documents/validation', [ControleClientDocumentController::class, 'validateDocuments'])
            ->name('controle-client.documents.validate');

        // Routes pour les subscriptions (abonnements mensuels)
        Route::prefix('subscriptions')->name('subscriptions.')->group(function () {
            Route::get('/', [SubscriptionController::class, 'create'])->name('create');
            Route::get('search', [SubscriptionController::class, 'search'])->name('search');
            Route::get('frais-reabonnement', [SubscriptionController::class, 'getFraisReabonnement'])->name('frais-reabonnement');
            Route::post('calculer', [SubscriptionController::class, 'calculer'])->name('calculer');
            Route::post('store', [SubscriptionController::class, 'store'])->name('store');
            Route::get('show/{subscription}', [SubscriptionController::class, 'show'])->name('show');
            Route::patch('{subscription}/cancel', [SubscriptionController::class, 'cancel'])->name('cancel');
            Route::get('dossier/{dossierMedical}', [SubscriptionController::class, 'index'])->name('index');
        });

        // Routes pour l'historique des transactions (Finances)
        Route::prefix('transactions')->name('transactions.')->group(function () {
            Route::get('/', [TransactionController::class, 'index'])->name('index');
            Route::get('export', [TransactionController::class, 'export'])->name('export');
        });
    });
});

require __DIR__.'/auth.php';
