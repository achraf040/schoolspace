<?php

namespace App\Services;

use App\Models\User;
use App\Models\Espace;

class EmailGenerationService
{
    /**
     * Domaine par défaut pour tous les emails
     */
    private const DEFAULT_DOMAIN = '@supmti.ac.ma';

    /**
     * Mappage des préfixes d'espaces spécifiques
     */
    private const SPACE_PREFIXES = [
        'scolarité' => 'scol',
        'scolarite' => 'scol',
        'comptabilité' => 'compta',
        'comptabilite' => 'compta',
        'ressources humaines' => 'rh',
        'rh' => 'rh',
        'administration' => 'admin',
        'admin' => 'admin',
    ];

    /**
     * Générer un email complet pour un utilisateur
     */
    public function generateUserEmail(User $user): string
    {
        $username = $this->createUsernameFromName($user->name);
        $prefix = $this->getUserSpacePrefix($user);
        
        if ($prefix) {
            return $prefix . '.' . $username . self::DEFAULT_DOMAIN;
        }
        
        return $username . self::DEFAULT_DOMAIN;
    }

    /**
     * Générer un email pour un espace
     */
    public function generateSpaceEmail(Espace $espace): string
    {
        $prefix = $this->getSpacePrefixFromName($espace->nom);
        return $prefix . self::DEFAULT_DOMAIN;
    }

    /**
     * Créer un nom d'utilisateur à partir du nom complet
     */
    public function createUsernameFromName(string $name): string
    {
        $cleanName = $this->removeAccents(strtolower(trim($name)));
        return str_replace(' ', '.', $cleanName);
    }

    /**
     * Obtenir le préfixe d'espace pour un utilisateur
     */
    public function getUserSpacePrefix(User $user): ?string
    {
        // Priorité au primary_espace_id
        $primaryEspace = $user->primaryEspace;
        
        if ($primaryEspace) {
            return $this->getSpacePrefixFromName($primaryEspace->nom);
        }
        
        // Fallback vers le premier espace attribué
        $firstEspace = $user->espaces()->first();
        
        if (!$firstEspace) {
            return null;
        }
        
        return $this->getSpacePrefixFromName($firstEspace->nom);
    }

    /**
     * Obtenir le préfixe d'un espace basé sur son nom
     */
    public function getSpacePrefixFromName(string $spaceName): string
    {
        $normalizedName = strtolower(trim($spaceName));
        
        // Vérifier les préfixes spécifiques
        foreach (self::SPACE_PREFIXES as $keyword => $prefix) {
            if (strpos($normalizedName, $keyword) !== false) {
                return $prefix;
            }
        }
        
        // Générer un préfixe générique
        return $this->createGenericPrefix($normalizedName);
    }

    /**
     * Créer un préfixe générique à partir du nom
     */
    private function createGenericPrefix(string $name): string
    {
        $slug = $this->removeAccents($name);
        $slug = preg_replace('/[^a-z0-9\s]/', '', $slug);
        $slug = preg_replace('/\s+/', '', $slug);
        
        return substr($slug, 0, 5);
    }

    /**
     * Supprimer les accents et caractères spéciaux
     */
    public function removeAccents(string $string): string
    {
        // Utiliser la translitération native de PHP
        $transliterated = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $string);
        
        if ($transliterated !== false) {
            $string = $transliterated;
        }
        
        // Fallback manuel pour les caractères persistants
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
     * Valider un email selon le domaine autorisé
     */
    public function isValidEmail(string $email): bool
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        
        return str_ends_with(strtolower($email), self::DEFAULT_DOMAIN);
    }

    /**
     * Obtenir le domaine par défaut
     */
    public function getDefaultDomain(): string
    {
        return self::DEFAULT_DOMAIN;
    }

    /**
     * Obtenir tous les préfixes d'espaces disponibles
     */
    public function getAvailableSpacePrefixes(): array
    {
        return self::SPACE_PREFIXES;
    }
}