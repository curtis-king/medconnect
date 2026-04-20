<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AuthToken;
use App\Models\DossierMedical;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Handle user login - Simple token authentication.
     */
    public function login(Request $request): JsonResponse
    {
        // Rate limiting - max 5 login attempts per minute
        if ($this->isRateLimited($request->ip())) {
            return response()->json([
                'message' => 'Trop de tentatives. Réessayez dans 1 minute.',
            ], 429);
        }

        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            $this->recordFailedAttempt($request->ip());

            return response()->json([
                'message' => 'Identifiants invalides',
                'errors' => ['authentication' => ['Email ou mot de passe incorrect']],
            ], 401);
        }

        // Revoke old tokens for this user
        $user->authTokens()->delete();

        // Create new token
        $token = AuthToken::generateToken();
        AuthToken::create([
            'user_id' => $user->id,
            'token' => $token,
            'role' => $user->role,
            'email' => $user->email,
            'expires_at' => now()->addHours(24),
        ]);

        // Clear failed attempts on successful login
        $this->clearFailedAttempts($request->ip());

        return response()->json([
            'message' => 'Connexion réussie',
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone ?? null,
                    'role' => $user->role,
                    'avatar_url' => $user->avatar_url ?? null,
                ],
                'token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => 86400, // 24 hours in seconds
                'expires_at' => now()->addHours(24)->toIso8601String(),
            ],
        ], 200);
    }

    /**
     * Handle user registration.
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $token = AuthToken::generateToken();
        AuthToken::create([
            'user_id' => $user->id,
            'token' => $token,
            'role' => $user->role,
            'email' => $user->email,
            'expires_at' => now()->addHours(24),
        ]);

        return response()->json([
            'message' => 'Inscription réussie',
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone ?? null,
                    'role' => $user->role,
                ],
                'token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => 86400,
                'expires_at' => now()->addHours(24)->toIso8601String(),
            ],
        ], 201);
    }

    /**
     * Handle user logout.
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            // Get token from Authorization header
            $token = $this->getTokenFromHeader($request);
            if ($token) {
                AuthToken::where('token', $token)->delete();
            }

            return response()->json([
                'message' => 'Déconnexion réussie',
                'success' => true,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la déconnexion',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Logout from all devices.
     */
    public function logoutAll(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            if ($user) {
                $user->authTokens()->delete();
            }

            return response()->json([
                'message' => 'Déconnection de tous les appareils réussie',
                'success' => true,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la déconnexion',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get current authenticated user with full profile.
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'city' => $user->city,
                'quartier' => $user->quartier,
                'address' => $user->address,
                'role' => $user->role,
                'avatar_url' => $user->avatar_url,
                'created_at' => $user->created_at->toIso8601String(),
                'email_verified_at' => $user->email_verified_at?->toIso8601String(),
            ],
        ], 200);
    }

    /**
     * Update user profile.
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'phone' => 'nullable|string|max:20',
            'city' => 'nullable|string|max:255',
            'quartier' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Données invalides',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user->fill($validator->validated())->save();

        return response()->json([
            'success' => true,
            'message' => 'Profil mis à jour',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'city' => $user->city,
                'quartier' => $user->quartier,
                'address' => $user->address,
                'latitude' => $user->latitude,
                'longitude' => $user->longitude,
                'role' => $user->role,
                'avatar_url' => $user->avatar_url,
            ],
        ], 200);
    }

    /**
     * Check if token is still valid.
     */
    public function verify(Request $request): JsonResponse
    {
        return response()->json([
            'valid' => true,
            'user' => [
                'id' => $request->user()->id,
                'name' => $request->user()->name,
                'email' => $request->user()->email,
            ],
        ], 200);
    }

    /**
     * Refresh authentication token.
     */
    public function refresh(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'message' => 'Non authentifié',
            ], 401);
        }

        // Revoke current token
        $token = $this->getTokenFromHeader($request);
        if ($token) {
            AuthToken::where('token', $token)->delete();
        }

        // Create new token
        $newToken = AuthToken::generateToken();
        AuthToken::create([
            'user_id' => $user->id,
            'token' => $newToken,
            'role' => $user->role,
            'email' => $user->email,
            'expires_at' => now()->addHours(24),
        ]);

        return response()->json([
            'message' => 'Token rafraîchi',
            'success' => true,
            'data' => [
                'token' => $newToken,
                'token_type' => 'Bearer',
                'expires_in' => 86400,
                'expires_at' => now()->addHours(24)->toIso8601String(),
            ],
        ], 200);
    }

    /**
     * Get list of active sessions/devices.
     */
    public function devices(Request $request): JsonResponse
    {
        $devices = $request->user()->authTokens()->get()->map(function ($token) {
            return [
                'id' => $token->id,
                'device_name' => $token->id,
                'created_at' => $token->created_at->toIso8601String(),
                'expires_at' => $token->expires_at->toIso8601String(),
            ];
        });

        return response()->json([
            'success' => true,
            'devices' => $devices,
            'total' => $devices->count(),
        ], 200);
    }

    /**
     * Revoke specific device/session.
     */
    public function revokeDevice(Request $request, int $tokenId): JsonResponse
    {
        $token = $request->user()->authTokens()->find($tokenId);

        if (! $token) {
            return response()->json([
                'message' => 'Session non trouvée',
            ], 404);
        }

        $token->delete();

        return response()->json([
            'message' => 'Session révoquée',
            'success' => true,
        ], 200);
    }

    /**
     * Check rate limiting for login attempts.
     */
    private function isRateLimited(string $ip): bool
    {
        $key = "login_attempts:{$ip}";
        $attempts = cache($key, 0);

        return $attempts >= 5;
    }

    /**
     * Record failed login attempt.
     */
    private function recordFailedAttempt(string $ip): void
    {
        $key = "login_attempts:{$ip}";
        $attempts = cache($key, 0);
        cache([$key => $attempts + 1], now()->addMinute());
    }

    /**
     * Clear failed login attempts.
     */
    private function clearFailedAttempts(string $ip): void
    {
        $key = "login_attempts:{$ip}";
        cache()->forget($key);
    }

    /**
     * Extract token from Authorization header.
     */
    private function getTokenFromHeader(Request $request): ?string
    {
        $header = $request->header('Authorization', '');

        if (preg_match('/^Bearer\s+(.+)$/i', $header, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Authenticate user using our custom token and set as current user.
     */
    private function authenticate(Request $request): ?User
    {
        $token = $this->getTokenFromHeader($request);

        if (! $token) {
            return null;
        }

        $authToken = AuthToken::where('token', $token)
            ->where(function ($query) {
                $query->where('expires_at', '>', now())
                    ->orWhereNull('expires_at');
            })
            ->first();

        if (! $authToken) {
            return null;
        }

        $user = User::find($authToken->user_id);

        if ($user) {
            Auth::login($user);
        }

        return $user;
    }

    /**
     * Synchronize medical dossier with user account.
     */
    public function syncDossier(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'numero_dossier' => 'required|string|max:50',
        ]);

        $dossier = DossierMedical::where('numero_unique', $validated['numero_dossier'])
            ->first();

        if (! $dossier) {
            return response()->json([
                'success' => false,
                'message' => 'Dossier médical non trouvé',
            ], 404);
        }

        if ($dossier->user_id && $dossier->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Ce dossier est déjà lié à un autre compte',
            ], 422);
        }

        $dossier->user_id = $user->id;
        $dossier->save();

        return response()->json([
            'success' => true,
            'message' => 'Dossier médical synchronisé',
            'dossier' => [
                'id' => $dossier->id,
                'numero_unique' => $dossier->numero_unique,
                'nom' => $dossier->nom,
                'prenom' => $dossier->prenom,
                'date_naissance' => $dossier->date_naissance?->format('Y-m-d'),
                'sexe' => $dossier->sexe,
                'telephone' => $dossier->telephone,
                'adresse' => $dossier->adresse,
                'groupe_sanguin' => $dossier->groupe_sanguin,
                'allergies' => $dossier->allergies,
                'maladies_chroniques' => $dossier->maladies_chroniques,
                'traitements_en_cours' => $dossier->traitements_en_cours,
                'antecedents_familiaux' => $dossier->antecedents_familiaux,
                'antecedents_personnels' => $dossier->antecedents_personnels,
                'contact_urgence_nom' => $dossier->contact_urgence_nom,
                'contact_urgence_telephone' => $dossier->contact_urgence_telephone,
                'contact_urgence_relation' => $dossier->contact_urgence_relation,
                'type_piece_identite' => $dossier->type_piece_identite,
                'numero_piece_identite' => $dossier->numero_piece_identite,
                'photo_profil_path' => $dossier->photo_profil_path,
                'statut_paiement_inscription' => $dossier->statut_paiement_inscription,
                'mode_paiement_inscription' => $dossier->mode_paiement_inscription,
                'reference_paiement_inscription' => $dossier->reference_paiement_inscription,
                'source_creation' => $dossier->source_creation,
                'est_personne_a_charge' => $dossier->est_personne_a_charge,
                'lien_avec_responsable' => $dossier->lien_avec_responsable,
                'partage_actif' => $dossier->partage_actif,
                'partage_active_le' => $dossier->partage_active_le?->format('Y-m-d H:i:s'),
                'code_partage' => $dossier->code_partage,
                'actif' => $dossier->actif,
            ],
        ], 200);
    }

    /**
     * Get user's medical dossier.
     */
    public function getDossier(Request $request): JsonResponse
    {
        $user = $request->user();

        $dossier = DossierMedical::where('user_id', $user->id)
            ->first();

        if (! $dossier) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun dossier médical trouvé',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'dossier' => [
                'id' => $dossier->id,
                'numero_unique' => $dossier->numero_unique,
                'nom' => $dossier->nom,
                'prenom' => $dossier->prenom,
                'date_naissance' => $dossier->date_naissance?->format('Y-m-d'),
                'sexe' => $dossier->sexe,
                'telephone' => $dossier->telephone,
                'adresse' => $dossier->adresse,
                'groupe_sanguin' => $dossier->groupe_sanguin,
                'allergies' => $dossier->allergies,
                'maladies_chroniques' => $dossier->maladies_chroniques,
                'traitements_en_cours' => $dossier->traitements_en_cours,
                'antecedents_familiaux' => $dossier->antecedents_familiaux,
                'antecedents_personnels' => $dossier->antecedents_personnels,
                'contact_urgence_nom' => $dossier->contact_urgence_nom,
                'contact_urgence_telephone' => $dossier->contact_urgence_telephone,
                'contact_urgence_relation' => $dossier->contact_urgence_relation,
                'type_piece_identite' => $dossier->type_piece_identite,
                'numero_piece_identite' => $dossier->numero_piece_identite,
                'photo_profil_url' => $dossier->photo_profil_path ? asset('storage/'.$dossier->photo_profil_path) : null,
                'statut_paiement_inscription' => $dossier->statut_paiement_inscription,
                'mode_paiement_inscription' => $dossier->mode_paiement_inscription,
                'reference_paiement_inscription' => $dossier->reference_paiement_inscription,
                'source_creation' => $dossier->source_creation,
                'est_personne_a_charge' => $dossier->est_personne_a_charge,
                'lien_avec_responsable' => $dossier->lien_avec_responsable,
                'partage_actif' => $dossier->partage_actif,
                'partage_active_le' => $dossier->partage_active_le?->format('Y-m-d H:i:s'),
                'code_partage' => $dossier->code_partage,
                'actif' => $dossier->actif,
            ],
        ], 200);
    }

    /**
     * Update medical dossier information.
     */
    public function updateDossier(Request $request): JsonResponse
    {
        $user = $request->user();

        $dossier = DossierMedical::where('user_id', $user->id)
            ->first();

        if (! $dossier) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun dossier médical trouvé',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'telephone' => 'nullable|string|max:20',
            'adresse' => 'nullable|string|max:500',
            'groupe_sanguin' => 'nullable|string|max:5',
            'allergies' => 'nullable|string',
            'maladies_chroniques' => 'nullable|string',
            'traitements_en_cours' => 'nullable|string',
            'antecedents_familiaux' => 'nullable|string',
            'antecedents_personnels' => 'nullable|string',
            'contact_urgence_nom' => 'nullable|string|max:255',
            'contact_urgence_telephone' => 'nullable|string|max:20',
            'contact_urgence_relation' => 'nullable|string|max:100',
            'photo_url' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Données invalides',
                'errors' => $validator->errors(),
            ], 422);
        }

        $dossier->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Dossier mis à jour',
            'dossier' => [
                'id' => $dossier->id,
                'telephone' => $dossier->telephone,
                'adresse' => $dossier->adresse,
                'allergies' => $dossier->allergies,
                'maladies_chroniques' => $dossier->maladies_chroniques,
                'traitements_en_cours' => $dossier->traitements_en_cours,
                'antecedents_familiaux' => $dossier->antecedents_familiaux,
                'antecedents_personnels' => $dossier->antecedents_personnels,
                'contact_urgence_nom' => $dossier->contact_urgence_nom,
                'contact_urgence_telephone' => $dossier->contact_urgence_telephone,
                'contact_urgence_relation' => $dossier->contact_urgence_relation,
                'photo_profil_path' => $dossier->photo_profil_path,
            ],
        ], 200);
    }

    /**
     * Upload dossier photo.
     */
    public function uploadPhoto(Request $request): JsonResponse
    {
        $user = $request->user();

        $dossier = DossierMedical::where('user_id', $user->id)
            ->first();

        if (! $dossier) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun dossier médical trouvé',
            ], 404);
        }

        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('dossiers/'.$dossier->id, 'public');
            $dossier->photo_profil_path = $path;
            $dossier->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Photo uploadée',
            'photo_url' => asset('storage/'.$dossier->photo_profil_path),
        ], 200);
    }

    public function registerDeviceToken(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required|string',
            'platform' => 'required|string|in:android,ios',
        ]);

        $user = $request->user();

        $existingToken = \App\Models\UserDeviceToken::where('user_id', $user->id)
            ->where('token', $request->token)
            ->first();

        if (! $existingToken) {
            \App\Models\UserDeviceToken::create([
                'user_id' => $user->id,
                'token' => $request->token,
                'platform' => $request->platform,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Token enregistré',
        ], 200);
    }

    public function getNotifications(Request $request): JsonResponse
    {
        $user = $request->user();

        $notifications = \App\Models\Notification::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(50)
            ->get()
            ->map(function ($n) {
                return [
                    'id' => $n->id,
                    'type' => $n->type,
                    'title' => $n->title,
                    'body' => $n->body,
                    'data' => $n->data,
                    'read' => $n->read_at !== null,
                    'created_at' => $n->created_at->toIso8601String(),
                ];
            });

        $unreadCount = \App\Models\Notification::where('user_id', $user->id)
            ->whereNull('read_at')
            ->count();

        return response()->json([
            'success' => true,
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ], 200);
    }

    public function markNotificationRead(Request $request, int $id): JsonResponse
    {
        $user = $request->user();

        $notification = \App\Models\Notification::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (! $notification) {
            return response()->json([
                'success' => false,
                'message' => 'Notification non trouvée',
            ], 404);
        }

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notification marquée comme lue',
        ], 200);
    }

    public function markAllNotificationsRead(Request $request): JsonResponse
    {
        $user = $request->user();

        \App\Models\Notification::where('user_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'Toutes les notifications marquées comme lues',
        ], 200);
    }
}
