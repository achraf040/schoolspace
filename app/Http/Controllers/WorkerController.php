<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\Attribution;
use App\Services\CacheService;

class WorkerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('worker');
    }

    /**
     * Afficher le dashboard du travailleur
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        // Récupérer l'espace du travailleur avec cache optimisé
        $userEspace = CacheService::remember("espace_{$user->id}", 'user_espace', function() use ($user) {
            return $this->getUserEspaceFromEmail($user->email);
        });
        
        if (!$userEspace) {
            return redirect()->route('login')->withErrors(['email' => 'Aucun espace assigné à votre compte.']);
        }
        
        // Optimized query with proper indexing and pagination
        $attributions = Attribution::where('user_id', $user->id)
            ->where('espace_id', $userEspace->id)
            ->with(['espace:id,nom']) // Only select needed columns
            ->select(['id', 'user_id', 'espace_id', 'task_status', 'type', 'description', 'title', 'created_at', 'updated_at'])
            ->orderBy('created_at', 'desc')
            ->limit(100) // Limit results for performance
            ->get();
            
        // Cache statistics avec service optimisé
        $stats = CacheService::remember("stats_{$user->id}_{$userEspace->id}", 'user_stats', function() use ($user, $userEspace) {
            $attributions = Attribution::where('user_id', $user->id)
                ->where('espace_id', $userEspace->id)
                ->get();
            
            $counts = [
                'total' => $attributions->count(),
                'active' => 0,
                'pending' => 0,
                'paused' => 0,
                'completed' => 0,
                'expired' => 0
            ];
            
            foreach ($attributions as $attribution) {
                // Count by task_status for worker view
                switch ($attribution->task_status) {
                    case 'active':
                        $counts['active']++;
                        break;
                    case 'pending':
                        $counts['pending']++;
                        break;
                    case 'paused':
                        $counts['paused']++;
                        break;
                    case 'completed':
                        $counts['completed']++;
                        break;
                    default:
                        // Handle null or other statuses
                        if (!$attribution->task_status) {
                            $counts['pending']++;
                        }
                        break;
                }
                
                // Count expired based on date logic for admin status
                $adminStatus = $attribution->status;
                if ($adminStatus === 'expired') {
                    $counts['expired']++;
                }
            }
            
            return $counts;
        });

        return view('worker.dashboard', compact('user', 'userEspace', 'attributions', 'stats'));
    }

    /**
     * Mettre à jour le statut d'une tâche
     */
    public function updateTaskStatus(Request $request, Attribution $attribution)
    {
        $user = Auth::user();
        
        // Vérifications de sécurité multiples
        if (!$this->canAccessAttribution($user, $attribution)) {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }
        
        $request->validate([
            'status' => 'required|in:pending,active,paused,completed'
        ]);
        
        // Log the attempt for debugging
        \Log::info('Tentative de mise à jour du statut de la tâche', [
            'user_id' => $user->id,
            'attribution_id' => $attribution->id,
            'old_status' => $attribution->task_status,
            'new_status' => $request->status
        ]);

        // Utiliser la nouvelle méthode pour mettre à jour le statut
        try {
            $success = $attribution->updateTaskStatus($request->status);
            
            if (!$success) {
                \Log::error('Échec de la mise à jour du statut de la tâche', [
                    'attribution_id' => $attribution->id,
                    'status' => $request->status
                ]);
                return response()->json(['error' => 'Erreur lors de la mise à jour du statut'], 500);
            }
            
            \Log::info('Statut de la tâche mis à jour avec succès', [
                'attribution_id' => $attribution->id,
                'new_status' => $request->status
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Exception lors de la mise à jour du statut', [
                'attribution_id' => $attribution->id,
                'status' => $request->status,
                'error' => $e->getMessage()
            ]);
            return response()->json(['error' => 'Erreur technique: ' . $e->getMessage()], 500);
        }
        
        // Clear related caches when updating task status
        CacheService::clearUserCache($user->id);
        
        return response()->json([
            'success' => true,
            'message' => 'Statut de la tâche mis à jour avec succès'
        ]);
    }

    /**
     * Obtenir l'espace d'un utilisateur basé sur son email
     */
    private function getUserEspaceFromEmail($email)
    {
        $user = Auth::user();
        
        // Utiliser l'espace principal défini dans primary_espace_id
        $espace = $user->primaryEspace;
        
        // Si aucun espace principal défini, prendre le premier espace attribué
        if (!$espace) {
            $espace = $user->espaces()->first();
        }
        
        // En dernier recours, essayer l'attribution automatique
        if (!$espace) {
            $user->autoAssignEspace();
            $espace = $user->espaces()->first();
        }
        
        return $espace;
    }

    /**
     * Afficher les détails d'une tâche
     */
    public function showTask(Attribution $attribution)
    {
        $user = Auth::user();
        
        // Vérifications de sécurité multiples
        if (!$this->canAccessAttribution($user, $attribution)) {
            abort(403, 'Accès non autorisé à cette tâche');
        }
        
        return view('worker.task-details', compact('attribution'));
    }

    /**
     * Vérifier si un utilisateur peut accéder à une attribution
     */
    private function canAccessAttribution($user, $attribution)
    {
        // Vérifier que l'attribution appartient à l'utilisateur
        if ($attribution->user_id !== $user->id) {
            return false;
        }
        
        // Vérifier que l'utilisateur a accès à l'espace de cette attribution
        $userEspaces = $user->espaces()->pluck('espaces.id')->toArray();
        if (!in_array($attribution->espace_id, $userEspaces)) {
            return false;
        }
        
        // Vérifier que l'utilisateur n'est pas un admin (sécurité supplémentaire)
        if ($user->isAdmin()) {
            return false;
        }
        
        return true;
    }

    /**
     * Sécuriser l'accès aux données - Méthode utilisée par le middleware
     */
    public function checkWorkerAccess($user)
    {
        // Vérifier que l'utilisateur est actif
        if (!$user->is_active) {
            return false;
        }
        
        // Vérifier que l'utilisateur n'est pas admin
        if ($user->isAdmin()) {
            return false;
        }
        
        // Vérifier que l'utilisateur a au moins un espace
        if ($user->espaces()->count() === 0) {
            // Essayer l'attribution automatique
            $user->autoAssignEspace();
            
            // Vérifier à nouveau
            if ($user->espaces()->count() === 0) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Afficher la page "Mes tâches"
     */
    public function myTasks()
    {
        $user = Auth::user();
        
        // Récupérer l'espace du travailleur
        $userEspace = CacheService::remember("espace_{$user->id}", 'user_espace', function() use ($user) {
            return $this->getUserEspaceFromEmail($user->email);
        });
        
        if (!$userEspace) {
            return redirect()->route('worker.dashboard')->withErrors(['error' => 'Aucun espace assigné à votre compte.']);
        }
        
        // Récupérer toutes les tâches actives, en attente et en pause
        $activeTasks = Attribution::where('user_id', $user->id)
            ->where('espace_id', $userEspace->id)
            ->where(function($query) {
                $query->whereIn('task_status', ['active', 'pending', 'paused'])
                      ->orWhereNull('task_status');
            })
            ->with(['espace:id,nom'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('worker.tasks', compact('user', 'userEspace', 'activeTasks'));
    }

    /**
     * Afficher l'historique des tâches
     */
    public function history()
    {
        $user = Auth::user();
        
        // Récupérer l'espace du travailleur
        $userEspace = CacheService::remember("espace_{$user->id}", 'user_espace', function() use ($user) {
            return $this->getUserEspaceFromEmail($user->email);
        });
        
        if (!$userEspace) {
            return redirect()->route('worker.dashboard')->withErrors(['error' => 'Aucun espace assigné à votre compte.']);
        }
        
        // Récupérer toutes les tâches terminées
        $completedTasks = Attribution::where('user_id', $user->id)
            ->where('espace_id', $userEspace->id)
            ->where('task_status', 'completed')
            ->with(['espace:id,nom'])
            ->orderBy('updated_at', 'desc')
            ->paginate(15);
            
        return view('worker.history', compact('user', 'userEspace', 'completedTasks'));
    }

    /**
     * API endpoint pour récupérer les statistiques en temps réel
     */
    public function getStats()
    {
        $user = Auth::user();
        
        // Récupérer l'espace du travailleur
        $userEspace = CacheService::remember("espace_{$user->id}", 'user_espace', function() use ($user) {
            return $this->getUserEspaceFromEmail($user->email);
        });
        
        if (!$userEspace) {
            return response()->json(['error' => 'Aucun espace assigné'], 404);
        }
        
        // Statistiques mises à jour
        $attributions = Attribution::where('user_id', $user->id)
            ->where('espace_id', $userEspace->id)
            ->get();
        
        $stats = [
            'total' => $attributions->count(),
            'active' => 0,
            'pending' => 0,
            'paused' => 0,
            'completed' => 0,
            'expired' => 0
        ];
        
        foreach ($attributions as $attribution) {
            // Count by task_status for worker view
            switch ($attribution->task_status) {
                case 'active':
                    $stats['active']++;
                    break;
                case 'pending':
                    $stats['pending']++;
                    break;
                case 'paused':
                    $stats['paused']++;
                    break;
                case 'completed':
                    $stats['completed']++;
                    break;
                default:
                    // Handle null or other statuses
                    if (!$attribution->task_status) {
                        $stats['pending']++;
                    }
                    break;
            }
            
            // Count expired based on date logic for admin status
            $adminStatus = $attribution->status;
            if ($adminStatus === 'expired') {
                $stats['expired']++;
            }
        }
        
        // Calculer le pourcentage de progression basé sur les tâches complétées
        $totalTasks = $stats['total'];
        $progressPercentage = $totalTasks > 0 ? round(($stats['completed'] / $totalTasks) * 100) : 0;
        
        return response()->json([
            'stats' => $stats,
            'progress' => $progressPercentage,
            'user_space' => $userEspace->nom,
            'timestamp' => now()->toISOString()
        ]);
    }
}