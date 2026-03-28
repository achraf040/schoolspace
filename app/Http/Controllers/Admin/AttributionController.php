<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Espace;
use App\Models\Attribution;
use Illuminate\Support\Facades\Cache;

class AttributionController extends Controller
{
    public function index()
    {
        $attributions = Attribution::with(['user', 'espace'])->latest()->paginate(15);
        $espaces = Espace::where('is_active', true)->get();
        
        // Ne plus charger tous les utilisateurs - ils seront chargés dynamiquement via l'API
        return view('admin.attributions.index', compact('attributions', 'espaces'));
    }

    public function show(Attribution $attribution)
    {
        $attribution->load(['user', 'espace']);
        
        return response()->json([
            'success' => true,
            'attribution' => [
                'id' => $attribution->id,
                'type' => $attribution->type,
                'type_display' => $attribution->type_display,
                'type_color' => $attribution->type_color,
                'description' => $attribution->description,
                'start_date' => $attribution->start_date ? $attribution->start_date->format('d/m/Y') : null,
                'end_date' => $attribution->end_date ? $attribution->end_date->format('d/m/Y') : null,
                'access_hours' => $attribution->access_hours,
                'status' => $attribution->status,
                'status_display' => $attribution->status_display,
                'task_status' => $attribution->task_status,
                'task_status_display' => $attribution->task_status_display,
                'completed_at' => $attribution->completed_at ? $attribution->completed_at->format('d/m/Y à H:i') : null,
                'created_at' => $attribution->created_at ? $attribution->created_at->format('d/m/Y à H:i') : null,
                'updated_at' => $attribution->updated_at ? $attribution->updated_at->format('d/m/Y à H:i') : null,
                'user' => [
                    'id' => $attribution->user->id ?? null,
                    'name' => $attribution->user->name ?? 'Utilisateur supprimé',
                    'email' => $attribution->user->display_email ?? '',
                    'account_type' => $attribution->user->account_type ?? '',
                    'is_active' => $attribution->user->is_active ?? false,
                ],
                'espace' => [
                    'id' => $attribution->espace->id ?? null,
                    'nom' => $attribution->espace->nom ?? 'Espace supprimé',
                    'email' => $attribution->espace->display_email ?? '',
                    'space_type' => $attribution->espace->space_type ?? '',
                    'is_active' => $attribution->espace->is_active ?? false,
                ],
            ]
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'espace_id' => 'required|exists:espaces,id',
            'type' => 'required|in:permanente,ponctuelle,temporaire',
            'description' => 'nullable|string|max:1000',
            'start_date' => 'nullable|date|after_or_equal:today',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'access_hours' => 'nullable|string',
        ]);

        try {
            // Parse access hours if provided
            $accessHours = null;
            if ($request->access_hours) {
                $accessHours = json_decode($request->access_hours, true);
            }

            $attribution = Attribution::create([
                'user_id' => $request->user_id,
                'espace_id' => $request->espace_id,
                'type' => $request->type,
                'description' => $request->description,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'access_hours' => $accessHours,
            ]);
            
            // Clear sidebar cache when attribution data changes
            Cache::forget('admin_sidebar_counts');

            // Mettre à jour l'email de l'utilisateur basé sur le nouvel espace
            $user = \App\Models\User::find($request->user_id);
            $user->load('espaces');
            $newDisplayEmail = $user->display_email;
            if ($newDisplayEmail !== $user->email) {
                $user->update(['email' => $newDisplayEmail]);
                \Log::info('Email mis à jour pour l\'utilisateur (ID: ' . $user->id . ') après création d\'attribution : ' . $newDisplayEmail);
            }

            return response()->json(['success' => true, 'message' => 'Tâche créée avec succès']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => 'Erreur lors de la création de la tâche: ' . $e->getMessage()]);
        }
    }


    public function update(Request $request, Attribution $attribution)
    {
        $request->validate([
            'espace_id' => 'required|exists:espaces,id',
            'type' => 'required|in:permanente,ponctuelle,temporaire',
            'description' => 'nullable|string|max:1000',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'access_hours' => 'nullable|string',
        ]);

        try {
            // Parse access hours if provided
            $accessHours = null;
            if ($request->access_hours) {
                $accessHours = json_decode($request->access_hours, true);
            }

            $attribution->update([
                'espace_id' => $request->espace_id,
                'type' => $request->type,
                'description' => $request->description,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'access_hours' => $accessHours,
            ]);

            // Mettre à jour l'email de l'utilisateur basé sur le nouvel espace
            $user = $attribution->user;
            $user->load('espaces');
            $newDisplayEmail = $user->display_email;
            if ($newDisplayEmail !== $user->email) {
                $user->update(['email' => $newDisplayEmail]);
                \Log::info('Email mis à jour pour l\'utilisateur (ID: ' . $user->id . ') après modification d\'attribution : ' . $newDisplayEmail);
            }

            return response()->json(['success' => true, 'message' => 'Tâche modifiée avec succès']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => 'Erreur lors de la modification de la tâche: ' . $e->getMessage()]);
        }
    }

    public function destroy(Attribution $attribution)
    {
        $attribution->delete();
        
        // Clear sidebar cache when attribution data changes
        Cache::forget('admin_sidebar_counts');
        
        return response()->json(['success' => true, 'message' => 'Tâche supprimée avec succès']);
    }


    /**
     * Récupérer les utilisateurs associés à un espace spécifique
     */
    public function getUsersBySpace($espaceId)
    {
        try {
            // Vérifier que l'espace existe
            $espace = Espace::findOrFail($espaceId);
            
            // Récupérer les utilisateurs qui ont des attributions dans cet espace
            $users = User::whereHas('attributions', function($query) use ($espaceId) {
                $query->where('espace_id', $espaceId);
            })
            ->where('role', 'user')
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

            // Aussi inclure tous les utilisateurs actifs pour permettre d'ajouter de nouveaux utilisateurs
            $allActiveUsers = User::where('role', 'user')
                ->where('is_active', true)
                ->whereNotIn('id', $users->pluck('id'))
                ->orderBy('name')
                ->get(['id', 'name', 'email']);

            return response()->json([
                'success' => true,
                'users' => [
                    'existing' => $users,
                    'available' => $allActiveUsers
                ],
                'espace' => [
                    'id' => $espace->id,
                    'nom' => $espace->nom
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'error' => 'Erreur lors de la récupération des utilisateurs: ' . $e->getMessage()
            ], 500);
        }
    }
}