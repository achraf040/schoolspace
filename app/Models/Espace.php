<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Espace extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'email',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'attributions')
                    ->withPivot(['type', 'description', 'start_date', 'end_date', 'access_hours'])
                    ->withTimestamps();
    }
    
    public function attributions()
    {
        return $this->hasMany(\App\Models\Attribution::class);
    }

    /**
     * Générer un email automatique basé sur le nom de l'espace
     */
    public function generateEmailFromName()
    {
        return app(\App\Services\EmailGenerationService::class)->generateSpaceEmail($this);
    }
    
    /**
     * Obtenir le préfixe d'espace
     */
    public function getEspacePrefix()
    {
        $nom = strtolower($this->nom);
        
        // Préfixes spécifiques pour les espaces principaux
        if (strpos($nom, 'scolarité') !== false || strpos($nom, 'scolarite') !== false) {
            return 'scol';
        } elseif (strpos($nom, 'comptabilité') !== false || strpos($nom, 'comptabilite') !== false) {
            return 'compta';
        } elseif (strpos($nom, 'ressources humaines') !== false || strpos($nom, 'rh') !== false) {
            return 'rh';
        } else {
            // Pour autres espaces, créer un préfixe court basé sur le nom
            $slug = $this->createSlugFromName($this->nom);
            return substr($slug, 0, 5); // Limiter à 5 caractères
        }
    }

    /**
     * Créer un slug à partir du nom
     */
    private function createSlugFromName($name)
    {
        // Supprimer les accents et caractères spéciaux
        $slug = $this->removeAccents(strtolower($name));
        
        // Garder seulement les lettres, chiffres et espaces
        $slug = preg_replace('/[^a-z0-9\s]/', '', $slug);
        
        // Remplacer les espaces par des tirets
        $slug = preg_replace('/\s+/', '-', trim($slug));
        
        // Limiter à 20 caractères pour éviter des emails trop longs
        return substr($slug, 0, 20);
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
        // Si l'email existe déjà, le retourner tel quel
        if ($this->email && strpos($this->email, '@') !== false) {
            return $this->email;
        }
        
        // Sinon, générer un email basé sur le nom
        return $this->generateEmailFromName();
    }

    /**
     * Obtenir le type d'espace pour affichage
     */
    public function getSpaceTypeAttribute()
    {
        // Déterminer le type d'espace basé sur le nom ou d'autres critères
        $nom = strtolower($this->nom);
        
        if (strpos($nom, 'salle') !== false || strpos($nom, 'classe') !== false) {
            return 'Salle de classe';
        } elseif (strpos($nom, 'labo') !== false || strpos($nom, 'laboratoire') !== false) {
            return 'Laboratoire';
        } elseif (strpos($nom, 'bureau') !== false) {
            return 'Bureau';
        } elseif (strpos($nom, 'amphi') !== false || strpos($nom, 'amphithéâtre') !== false) {
            return 'Amphithéâtre';
        } else {
            return 'Espace de travail';
        }
    }
}