<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\IdentityComplianceReviewService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    /**
     * Display a listing of users with search and sorting
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role') && $request->role !== 'all') {
            $query->where('role', $request->role);
        }

        // Filter by status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');

        $allowedSorts = ['name', 'email', 'role', 'status', 'created_at'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDirection);
        }

        $users = $query->paginate(15)->withQueryString();

        return view('user-management.index', [
            'users' => $users,
            'roles' => User::ROLES,
            'search' => $request->search,
            'selectedRole' => $request->role,
            'selectedStatus' => $request->status,
            'sortBy' => $sortBy,
            'sortDirection' => $sortDirection,
        ]);
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        return view('user-management.create', [
            'roles' => User::ROLES,
        ]);
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', Rule::in(array_keys(User::ROLES))],
            'status' => ['required', 'string', 'in:active,inactive'],
            'profile' => ['nullable', 'string', 'max:1000'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:100'],
            'quartier' => ['nullable', 'string', 'max:100'],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'status' => $request->status,
            'profile' => $request->profile,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'address' => $request->address,
            'city' => $request->city,
            'quartier' => $request->quartier,
        ]);

        return redirect()->route('user-management.index')
            ->with('success', 'Utilisateur créé avec succès.');
    }

    /**
     * Display the specified user
     */
    public function show(User $user, IdentityComplianceReviewService $identityComplianceReviewService)
    {
        // Debug: vérifier que l'utilisateur est bien passé
        if (! $user) {
            abort(404, 'Utilisateur non trouvé');
        }

        // Debug: vérifier l'utilisateur connecté
        $currentUser = auth()->user();
        if (! $currentUser) {
            abort(403, 'Vous devez être connecté');
        }

        if ($currentUser->role !== 'admin') {
            abort(403, 'Accès réservé aux administrateurs');
        }

        $user->load(['dossiersMedicaux', 'dossierProfessionnel']);

        $medicalComplianceReviews = $user->dossiersMedicaux
            ->map(fn ($dossierMedical) => [
                'dossier' => $dossierMedical,
                'review' => $identityComplianceReviewService->reviewMedicalDossier($dossierMedical),
            ])
            ->values();

        $professionalComplianceReview = $user->dossierProfessionnel
            ? $identityComplianceReviewService->reviewProfessionalDossier($user->dossierProfessionnel)
            : null;

        return view('user-management.show', [
            'user' => $user,
            'roles' => User::ROLES,
            'medicalComplianceReviews' => $medicalComplianceReviews,
            'professionalComplianceReview' => $professionalComplianceReview,
        ]);
    }

    /**
     * Show the form for editing the specified user
     */
    public function edit(User $user)
    {
        // Debug: vérifier que l'utilisateur est bien passé
        if (! $user) {
            abort(404, 'Utilisateur non trouvé');
        }

        // Debug: vérifier l'utilisateur connecté
        $currentUser = auth()->user();
        if (! $currentUser) {
            abort(403, 'Vous devez être connecté');
        }

        if ($currentUser->role !== 'admin') {
            abort(403, 'Accès réservé aux administrateurs');
        }

        return view('user-management.edit', [
            'user' => $user,
            'roles' => User::ROLES,
        ]);
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', Rule::in(array_keys(User::ROLES))],
            'status' => ['required', 'string', 'in:active,inactive'],
            'profile' => ['nullable', 'string', 'max:1000'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:100'],
            'quartier' => ['nullable', 'string', 'max:100'],
        ]);

        $emailWillChange = strtolower((string) $request->email) !== strtolower((string) $user->email);
        $identityLocked = $user->dossiersMedicaux()->exists() || $user->dossierProfessionnel()->exists();

        if ($emailWillChange && $identityLocked) {
            return back()
                ->withErrors(['email' => 'Email verrouille: impossible de modifier l email d un compte ayant deja un dossier medical ou professionnel.'])
                ->withInput();
        }

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => $request->role,
            'status' => $request->status,
            'profile' => $request->profile,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'address' => $request->address,
            'city' => $request->city,
            'quartier' => $request->quartier,
        ];

        // Only update password if provided
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return redirect()->route('user-management.index')
            ->with('success', 'Utilisateur mis à jour avec succès.');
    }

    /**
     * Remove the specified user
     */
    public function destroy(User $user)
    {
        // Prevent deleting the current authenticated user
        if ($user->id === auth()->id()) {
            return redirect()->route('user-management.index')
                ->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        $user->delete();

        return redirect()->route('user-management.index')
            ->with('success', 'Utilisateur supprimé avec succès.');
    }

    /**
     * Toggle user status (active/inactive)
     */
    public function toggleStatus(User $user)
    {
        $newStatus = $user->status === 'active' ? 'inactive' : 'active';

        $user->update(['status' => $newStatus]);

        $message = $newStatus === 'active' ? 'Utilisateur activé.' : 'Utilisateur désactivé.';

        return redirect()->back()->with('success', $message);
    }
}
