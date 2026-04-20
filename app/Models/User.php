<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * User role constants
     */
    const ROLE_ADMIN = 'admin';

    const ROLE_USER = 'user';

    const ROLE_PROFESSIONAL = 'professional';

    const ROLE_LIVREUR = 'livreur';

    const ROLE_FINANCIERE = 'financiere';

    const ROLE_SERVICE_CLIENT = 'service_client';

    const ROLE_SOIGNANT = 'soignant';

    const ROLE_MEMBRE = 'membre';

    /**
     * Available user roles
     *
     * @var array<string, string>
     */
    const ROLES = [
        self::ROLE_ADMIN => 'Administrateur',
        self::ROLE_USER => 'Utilisateur',
        self::ROLE_PROFESSIONAL => 'Professionnel',
        self::ROLE_LIVREUR => 'Livreur',
        self::ROLE_FINANCIERE => 'Agent Financier',
        self::ROLE_SERVICE_CLIENT => 'Service Client',
        self::ROLE_SOIGNANT => 'Soignant de Proximité',
        self::ROLE_MEMBRE => 'Membre Mutualisation',
    ];

    /**
     * User status constants
     */
    const STATUS_ACTIVE = 'active';

    const STATUS_INACTIVE = 'inactive';

    /**
     * Available user statuses
     */
    const STATUSES = [
        self::STATUS_ACTIVE => 'Actif',
        self::STATUS_INACTIVE => 'Inactif',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'status',
        'profile',
        'latitude',
        'longitude',
        'address',
        'city',
        'quartier',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
        ];
    }

    /**
     * Get the role label
     */
    public function getRoleLabel(): string
    {
        return self::ROLES[$this->role] ?? 'Inconnu';
    }

    /**
     * Check if user is a specific role
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * Check if user is livreur
     */
    public function isLivreur(): bool
    {
        return $this->role === self::ROLE_LIVREUR;
    }

    /**
     * Check if user is soignant
     */
    public function isSoignant(): bool
    {
        return $this->role === self::ROLE_SOIGNANT;
    }

    /**
     * Get full location string
     */
    public function getFullLocation(): ?string
    {
        $parts = array_filter([$this->address, $this->quartier, $this->city]);

        return count($parts) > 0 ? implode(', ', $parts) : null;
    }

    /**
     * Check if user has geolocation
     */
    public function hasGeolocation(): bool
    {
        return $this->latitude !== null && $this->longitude !== null;
    }

    /**
     * Check if user is active
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Check if user is inactive
     */
    public function isInactive(): bool
    {
        return $this->status === self::STATUS_INACTIVE;
    }

    /**
     * Get the status label
     */
    public function getStatusLabel(): string
    {
        return self::STATUSES[$this->status] ?? 'Inconnu';
    }

    /**
     * Scope for active users
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope for inactive users
     */
    public function scopeInactive($query)
    {
        return $query->where('status', self::STATUS_INACTIVE);
    }

    public function isProfessional(): bool
    {
        return $this->role === self::ROLE_PROFESSIONAL;
    }

    public function dossierProfessionnel()
    {
        return $this->hasOne(DossierProfessionnel::class, 'user_id');
    }

    public function dossierMedical()
    {
        return $this->hasOne(DossierMedical::class, 'user_id');
    }

    public function dossiersMedicaux()
    {
        return $this->hasMany(DossierMedical::class, 'user_id');
    }

    public function rendezVousPatients()
    {
        return $this->hasMany(RendezVousProfessionnel::class, 'patient_user_id');
    }

    public function facturesProfessionnelles()
    {
        return $this->hasMany(FactureProfessionnelle::class, 'patient_user_id');
    }

    public function consultationsProfessionnelles()
    {
        return $this->hasMany(ConsultationProfessionnelle::class, 'patient_user_id');
    }
}
