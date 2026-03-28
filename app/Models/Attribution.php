<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attribution extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'espace_id',
        'type',
        'description',
        'start_date',
        'end_date',
        'access_hours',
        'task_status',
        'title',
        'completed_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'access_hours' => 'array',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function espace()
    {
        return $this->belongsTo(Espace::class);
    }

    /**
     * Obtenir le type d'attribution en français
     */
    public function getTypeDisplayAttribute()
    {
        $types = [
            'access' => 'Accès général',
            'reservation' => 'Réservation ponctuelle',
            'permanent' => 'Attribution permanente',
            'temporary' => 'Attribution temporaire',
            'maintenance' => 'Accès maintenance',
            'administration' => 'Accès administratif'
        ];

        return $types[$this->type] ?? 'Accès général';
    }

    /**
     * Obtenir la couleur associée au type
     */
    public function getTypeColorAttribute()
    {
        $colors = [
            'access' => '#10b981',        // Vert pour accès général
            'reservation' => '#f59e0b',   // Orange pour réservation
            'permanent' => '#059669',     // Vert foncé pour permanent
            'temporary' => '#ef4444',     // Rouge pour temporaire
            'maintenance' => '#6366f1',   // Bleu pour maintenance
            'administration' => '#8b5cf6' // Violet pour administration
        ];

        return $colors[$this->type] ?? '#10b981';
    }

    /**
     * Vérifier si l'attribution est active
     */
    public function isActive()
    {
        $now = now()->toDateString();
        
        // Si pas de dates, considérer comme actif
        if (!$this->start_date && !$this->end_date) {
            return true;
        }
        
        // Si date de début mais pas de fin
        if ($this->start_date && !$this->end_date) {
            return $now >= $this->start_date->toDateString();
        }
        
        // Si date de fin mais pas de début
        if ($this->end_date && !$this->start_date) {
            return $now <= $this->end_date->toDateString();
        }
        
        // Si les deux dates sont définies
        return $now >= $this->start_date->toDateString() && $now <= $this->end_date->toDateString();
    }

    /**
     * Obtenir le statut basé sur task_status pour les travailleurs
     */
    public function getStatusAttribute()
    {
        // Prioriser task_status si disponible (pour les travailleurs)
        if ($this->task_status) {
            return $this->task_status === 'completed' ? 'expired' : $this->task_status;
        }

        // Fallback vers la logique d'attribution classique (pour les admins)
        if ($this->isActive()) {
            return 'active';
        }
        
        $now = now()->toDateString();
        if ($this->start_date && $now < $this->start_date->toDateString()) {
            return 'pending';
        }
        
        return 'expired';
    }

    /**
     * Obtenir le libellé du statut pour affichage
     */
    public function getStatusDisplayAttribute()
    {
        $statuses = [
            'active' => 'Actif',
            'pending' => 'En attente',
            'expired' => 'Expiré'
        ];

        return $statuses[$this->status] ?? 'Actif';
    }

    /**
     * Obtenir le statut de la tâche pour affichage spécifique travailleurs
     */
    public function getTaskStatusDisplayAttribute()
    {
        $statuses = [
            'pending' => 'En attente',
            'active' => 'En cours',
            'paused' => 'En pause',
            'completed' => 'Terminée'
        ];

        return $statuses[$this->task_status] ?? 'En attente';
    }

    /**
     * Mettre à jour le statut de la tâche
     */
    public function updateTaskStatus($status)
    {
        $validStatuses = ['pending', 'active', 'paused', 'completed'];
        
        if (!in_array($status, $validStatuses)) {
            return false;
        }

        // Store the old status before updating
        $oldStatus = $this->task_status;
        
        $this->task_status = $status;
        
        // Set completion time only for completed status
        if ($status === 'completed') {
            $this->completed_at = now();
        } elseif ($oldStatus === 'completed' && $status !== 'completed') {
            // Reset completion time if moving away from completed status
            $this->completed_at = null;
        }
        
        return $this->save();
    }

    /**
     * Vérifier si la tâche est terminée
     */
    public function isCompleted()
    {
        return $this->task_status === 'completed';
    }

    /**
     * Vérifier si la tâche est en pause
     */
    public function isPaused()
    {
        return $this->task_status === 'paused';
    }

    /**
     * Vérifier si la tâche est active
     */
    public function isTaskActive()
    {
        return $this->task_status === 'active';
    }

    /**
     * Vérifier si la tâche peut être modifiée (pas terminée)
     */
    public function canBeModified()
    {
        return $this->task_status !== 'completed';
    }

    /**
     * Obtenir le titre de la tâche ou générer un par défaut
     */
    public function getTitleAttribute($value)
    {
        if ($value) {
            return $value;
        }
        
        // Vérifier si la relation espace est chargée pour éviter les requêtes N+1
        if ($this->relationLoaded('espace') && $this->espace) {
            return "Tâche assignée dans {$this->espace->nom}";
        }
        
        return "Tâche assignée";
    }
}