<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $users = User::where('role', 'user')
                    ->with('espaces')
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $espaces = \App\Models\Espace::orderBy('is_active', 'desc')->orderBy('nom')->get();
        return view('admin.users.create', compact('espaces'));
    }

    public function store(StoreUserRequest $request)
    {
        // Logique métier : vérifier si l'email appartient au domaine SUPMTI
        if (!$this->isValidSupmtiEmail($request->email)) {
            return back()->withErrors(['email' => 'L\'adresse email doit appartenir au domaine SUPMTI (@supmti.ac.ma).'])
                        ->withInput();
        }

        try {
            $userData = [
                'name' => ucwords(strtolower(trim($request->name))),
                'email' => strtolower(trim($request->email)),
                'password' => Hash::make($request->password),
                'role' => 'user',
                'is_active' => $request->boolean('is_active'),
            ];

            // Gestion de la photo de profil
            if ($request->hasFile('profile_photo')) {
                $photo = $request->file('profile_photo');
                $filename = $this->storeProfilePhoto($photo);
                $userData['profile_photo'] = $filename;
            }

            $user = User::create($userData);
            
            // Clear sidebar cache when user data changes
            Cache::forget('admin_sidebar_counts');
            
            \Log::info('Utilisateur créé avec ID: ' . $user->id . ' - Email: ' . $user->email);

            // Créer une attribution automatique si un espace a été sélectionné
            if ($request->filled('espace_id') && $request->espace_id) {
                $espace = \App\Models\Espace::find($request->espace_id);
                if ($espace && $espace->is_active) {
                    \App\Models\Attribution::create([
                        'user_id' => $user->id,
                        'espace_id' => $espace->id,
                        'type' => 'permanente',
                        'description' => 'Attribution automatique lors de la création de l\'utilisateur',
                    ]);
                    
                    // Mettre à jour l'email de l'utilisateur basé sur l'espace attribué
                    $user->load('espaces');
                    $newDisplayEmail = $user->display_email;
                    if ($newDisplayEmail !== $user->email) {
                        $user->update(['email' => $newDisplayEmail]);
                        \Log::info('Email mis à jour pour l\'utilisateur (ID: ' . $user->id . ') après attribution automatique : ' . $newDisplayEmail);
                    }
                    
                    $successMessage = 'Utilisateur créé avec succès et attribué à l\'espace "' . $espace->nom . '".';
                    
                    // Rediriger vers la page d'attribution avec un message de succès
                    return redirect()->route('admin.attributions.index')
                                   ->with('success', $successMessage)
                                   ->with('highlight_user', $user->id);
                } else {
                    $successMessage = 'Utilisateur créé avec succès. L\'espace sélectionné n\'est plus disponible.';
                }
            } else {
                $successMessage = 'Utilisateur créé avec succès. L\'attribution aux espaces peut se faire via la gestion des tâches.';
                
                // Si pas d'attribution automatique, rediriger vers la page d'attribution pour en créer une
                return redirect()->route('admin.attributions.index')
                               ->with('success', $successMessage)
                               ->with('suggest_user', $user->id);
            }

            return redirect()->route('admin.users.index')
                           ->with('success', $successMessage);

        } catch (\Exception $e) {
            \Log::error('Erreur création utilisateur: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Erreur lors de la création de l\'utilisateur: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    public function show(User $user)
    {
        if ($user->isAdmin()) {
            return redirect()->route('admin.users.index')
                           ->with('error', 'Impossible d\'afficher les détails d\'un administrateur.');
        }

        $user->load('espaces');
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        if ($user->isAdmin()) {
            return redirect()->route('admin.users.index')
                           ->with('error', 'Impossible de modifier un administrateur.');
        }

        // Charger les espaces disponibles
        $espaces = \App\Models\Espace::where('is_active', true)->orderBy('nom')->get();
        
        return view('admin.users.edit', compact('user', 'espaces'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        if ($user->isAdmin()) {
            return redirect()->route('admin.users.index')
                           ->with('error', 'Impossible de modifier un administrateur.');
        }

        // Note: L'email n'est plus validé depuis le formulaire car il est généré automatiquement

        try {
            $updateData = [
                'name' => ucwords(strtolower(trim($request->name))),
                'is_active' => $request->boolean('is_active'),
                // Note: L'email n'est plus mis à jour directement depuis le formulaire
                // Il sera automatiquement généré selon les espaces attribués
            ];

            // Mettre à jour le mot de passe seulement s'il est fourni
            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            // Gestion de la photo de profil
            if ($request->boolean('remove_photo')) {
                // Supprimer la photo actuelle
                if ($user->profile_photo) {
                    $this->deleteProfilePhoto($user->profile_photo);
                    $updateData['profile_photo'] = null;
                }
            } elseif ($request->hasFile('profile_photo')) {
                // Supprimer l'ancienne photo si elle existe
                if ($user->profile_photo) {
                    $this->deleteProfilePhoto($user->profile_photo);
                }
                // Stocker la nouvelle photo
                $photo = $request->file('profile_photo');
                $filename = $this->storeProfilePhoto($photo);
                $updateData['profile_photo'] = $filename;
            }

            $user->update($updateData);

            // Note: Les espaces sélectionnés dans le formulaire ne créent plus d'attributions automatiques
            // L'attribution se fait manuellement via la page des tâches par l'administrateur
            
            // Recharger l'utilisateur pour obtenir les relations actuelles
            $user->load('espaces');
            
            // Mettre à jour l'email avec l'extension basée sur les espaces existants
            $newDisplayEmail = $user->display_email;
            if ($newDisplayEmail !== $user->email) {
                $user->update(['email' => $newDisplayEmail]);
                \Log::info('Email mis à jour pour l\'utilisateur (ID: ' . $user->id . ') : ' . $newDisplayEmail);
            }

            return redirect()->route('admin.users.index')
                           ->with('success', 'Utilisateur modifié avec succès.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de la modification de l\'utilisateur.'])
                        ->withInput();
        }
    }

    public function destroy(User $user)
    {
        if ($user->isAdmin()) {
            return redirect()->route('admin.users.index')
                           ->with('error', 'Impossible de supprimer un administrateur.');
        }

        // Logique métier : vérifier si l'utilisateur a des attributions
        if ($user->espaces()->count() > 0) {
            return redirect()->route('admin.users.index')
                           ->with('error', 'Impossible de supprimer un utilisateur ayant des attributions d\'espaces. Retirez d\'abord ses attributions.');
        }

        try {
            $userName = $user->name;
            
            // Supprimer la photo de profil si elle existe
            if ($user->profile_photo) {
                $this->deleteProfilePhoto($user->profile_photo);
            }
            
            $user->delete();
            
            // Clear sidebar cache when user data changes
            Cache::forget('admin_sidebar_counts');

            return redirect()->route('admin.users.index')
                           ->with('success', "L'utilisateur {$userName} a été supprimé avec succès.");

        } catch (\Exception $e) {
            return redirect()->route('admin.users.index')
                           ->with('error', 'Erreur lors de la suppression de l\'utilisateur.');
        }
    }

    public function toggleStatus(User $user)
    {
        if ($user->isAdmin()) {
            return response()->json(['error' => 'Impossible de modifier le statut d\'un administrateur.'], 403);
        }

        try {
            $user->update(['is_active' => !$user->is_active]);
            
            $status = $user->is_active ? 'activé' : 'désactivé';
            return response()->json([
                'success' => true,
                'message' => "L'utilisateur {$user->name} a été {$status}.",
                'is_active' => $user->is_active
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la modification du statut.'], 500);
        }
    }

    /**
     * Stocker une photo de profil de manière sécurisée
     */
    private function storeProfilePhoto($photo)
    {
        // Générer un nom unique et sécurisé
        $filename = uniqid('profile_', true) . '_' . time() . '.' . $photo->getClientOriginalExtension();
        
        // Stocker dans le dossier profile_photos
        $photo->storeAs('public/profile_photos', $filename);
        
        return $filename;
    }

    /**
     * Supprimer une photo de profil
     */
    private function deleteProfilePhoto($filename)
    {
        if ($filename && Storage::exists('public/profile_photos/' . $filename)) {
            Storage::delete('public/profile_photos/' . $filename);
        }
    }

    /**
     * Logique métier : Vérifier si l'email appartient au domaine SUPMTI
     */
    private function isValidSupmtiEmail($email)
    {
        $baseDomain = 'supmti.ac.ma';
        $emailDomain = strtolower(substr(strrchr($email, "@"), 1));
        
        // Accepter le domaine exact et tous ses sous-domaines
        return $emailDomain === $baseDomain || str_ends_with($emailDomain, '.' . $baseDomain);
    }

}