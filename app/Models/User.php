<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'profile_photo',
        'access_token',
        'token_expires_at',
        'primary_espace_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed',
        'is_active' => 'boolean',
        'token_expires_at' => 'datetime',
    ];

    public function espaces()
    {
        return $this->belongsToMany(Espace::class, 'attributions')
                    ->withPivot(['type', 'description', 'start_date', 'end_date', 'access_hours'])
                    ->withTimestamps();
    }
    
    public function attributions()
    {
        return $this->hasMany(\App\Models\Attribution::class);
    }

    public function primaryEspace()
    {
        return $this->belongsTo(Espace::class, 'primary_espace_id');
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function getProfilePhotoUrlAttribute()
    {
        if ($this->hasProfilePhoto()) {
            return asset('storage/profile_photos/' . $this->profile_photo);
        }
        return null;
    }

    /**
     * Get profile photo URL with fallback for display
     */
    public function getProfilePhotoDisplayUrlAttribute()
    {
        if ($this->hasProfilePhoto()) {
            $url = asset('storage/profile_photos/' . $this->profile_photo);
            // Additional check if file is accessible via HTTP would require more complex logic
            return $url;
        }
        return null;
    }

    public function hasProfilePhoto()
    {
        return !is_null($this->profile_photo) && 
               !empty($this->profile_photo) && 
               file_exists(storage_path('app/public/profile_photos/' . $this->profile_photo));
    }

    /**
     * Générer un token d'accès sécurisé
     */
    public function generateAccessToken($expiresInHours = 24)
    {
        $token = bin2hex(random_bytes(32));
        $hashedToken = hash('sha256', $token);
        
        $this->update([
            'access_token' => $hashedToken,
            'token_expires_at' => now()->addHours($expiresInHours),
        ]);
        
        return $token; // Retourner le token non hashé pour l'URL
    }

    /**
     * Vérifier un token d'accès
     */
    public static function findByToken($token)
    {
        $hashedToken = hash('sha256', $token);
        
        return self::where('access_token', $hashedToken)
                   ->where('token_expires_at', '>', now())
                   ->first();
    }

    /**
     * Révoquer le token d'accès
     */
    public function revokeAccessToken()
    {
        $this->update([
            'access_token' => null,
            'token_expires_at' => null,
        ]);
    }

    /**
     * Vérifier si le token est valide
     */
    public function hasValidToken()
    {
        return $this->access_token && $this->token_expires_at && $this->token_expires_at->isFuture();
    }

    /**
     * Obtenir l'extension d'email selon le type de compte et les espaces attribués
     */
    public function getEmailExtension()
    {
        return app(\App\Services\EmailGenerationService::class)->getDefaultDomain();
    }

    /**
     * Obtenir l'espace d'un travailleur basé sur son email
     */
    public function getEspaceFromEmail()
    {
        // Si c'est un admin, retourner null
        if ($this->isAdmin()) {
            return null;
        }

        // Extraire le domaine de l'email
        $email = $this->email;
        $emailParts = explode('@', $email);
        
        if (count($emailParts) !== 2) {
            return null;
        }

        $domain = $emailParts[1];
        $domainParts = explode('.', $domain);
        
        if (count($domainParts) < 2) {
            return null;
        }

        // Récupérer le sous-domaine (partie avant le domaine principal)
        // Format attendu: nomprenom@nomEspace-...ma
        $subDomain = $domainParts[0];
        
        // Extraire le nom de l'espace après le tiret
        if (strpos($subDomain, '-') === false) {
            return null;
        }

        $spaceParts = explode('-', $subDomain, 2); // Limiter à 2 parties maximum
        if (count($spaceParts) < 2 || empty($spaceParts[1])) {
            return null;
        }

        $espaceSlug = trim($spaceParts[1]); // Récupérer et nettoyer la partie après le tiret
        
        // Rechercher l'espace correspondant (sans duplication)
        return \App\Models\Espace::where('nom', 'LIKE', "%{$espaceSlug}%")
                                 ->where('is_active', true)
                                 ->first();
    }

    /**
     * Assigner automatiquement un espace basé sur l'email
     */
    public function autoAssignEspace()
    {
        $espace = $this->getEspaceFromEmail();
        
        if ($espace && $this->espaces()->where('espace_id', $espace->id)->count() === 0) {
            $this->espaces()->attach($espace->id, [
                'type' => 'access',
                'description' => 'Attribution automatique basée sur l\'email',
                'start_date' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Créer un sous-domaine email basé sur le nom de l'espace
     */
    private function createEmailFromEspace($nom)
    {
        // Supprimer les accents et caractères spéciaux
        $slug = $this->removeAccents(strtolower($nom));
        
        // Garder seulement les lettres et chiffres
        $slug = preg_replace('/[^a-z0-9]/', '', $slug);
        
        // Limiter à 10 caractères pour éviter des sous-domaines trop longs
        return substr($slug, 0, 10);
    }
    
    /**
     * Supprimer les accents de manière complète
     */
    private function removeAccents($string)
    {
        // Utiliser la translitération native de PHP pour une conversion complète
        $string = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $string);
        
        // Fallback manuel pour les caractères qui pourraient persister
        $accents = [
            'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a',
            'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A',
            'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e',
            'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E',
            'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
            'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I',
            'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o',
            'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O',
            'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u',
            'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U',
            'ý' => 'y', 'ÿ' => 'y', 'Ý' => 'Y',
            'ñ' => 'n', 'Ñ' => 'N',
            'ç' => 'c', 'Ç' => 'C',
            "'" => '', '"' => '', '"' => '', "'" => '',
            '–' => '-', '—' => '-'
        ];

        return strtr($string, $accents);
    }

    /**
     * Obtenir l'email avec extension pour affichage
     */
    public function getDisplayEmailAttribute()
    {
        return app(\App\Services\EmailGenerationService::class)->generateUserEmail($this);
    }
    
    /**
     * Obtenir le préfixe d'espace pour l'email
     */
    private function getEspacePrefix()
    {
        $firstEspace = $this->espaces()->first();
        
        if (!$firstEspace) {
            return null;
        }
        
        $nom = strtolower($firstEspace->nom);
        
        // Préfixes spécifiques pour les espaces principaux
        if (strpos($nom, 'scolarité') !== false || strpos($nom, 'scolarite') !== false) {
            return 'scol';
        } elseif (strpos($nom, 'comptabilité') !== false || strpos($nom, 'comptabilite') !== false) {
            return 'compta';
        } elseif (strpos($nom, 'ressources humaines') !== false || strpos($nom, 'rh') !== false) {
            return 'rh';
        } else {
            // Pour autres espaces, créer un préfixe court
            $slug = $this->removeAccents(strtolower($firstEspace->nom));
            $slug = preg_replace('/[^a-z0-9]/', '', $slug);
            return substr($slug, 0, 5); // Limiter à 5 caractères
        }
    }

    /**
     * Obtenir le type de compte pour affichage
     */
    public function getAccountTypeAttribute()
    {
        // Si c'est un administrateur, retourner Administrateur
        if ($this->role === 'admin') {
            return 'Administrateur';
        }
        
        // Pour les utilisateurs normaux, se baser sur l'espace attribué
        $firstEspace = $this->espaces()->first();
        
        if ($firstEspace) {
            $nom = strtolower($firstEspace->nom);
            
            // Déterminer le type basé sur le nom de l'espace
            if (strpos($nom, 'scolarité') !== false || strpos($nom, 'scolarite') !== false) {
                return 'Scolarité';
            } elseif (strpos($nom, 'comptabilité') !== false || strpos($nom, 'comptabilite') !== false) {
                return 'Comptabilité';
            } elseif (strpos($nom, 'rh') !== false || strpos($nom, 'ressources humaines') !== false) {
                return 'Ressources Humaines';
            } elseif (strpos($nom, 'administration') !== false || strpos($nom, 'admin') !== false) {
                return 'Administration';
            } elseif (strpos($nom, 'salle') !== false || strpos($nom, 'classe') !== false) {
                return 'Enseignement';
            } elseif (strpos($nom, 'labo') !== false || strpos($nom, 'laboratoire') !== false) {
                return 'Laboratoire';
            } else {
                return ucfirst($firstEspace->nom);
            }
        }
        
        // Par défaut
        return 'Utilisateur';
    }
}
