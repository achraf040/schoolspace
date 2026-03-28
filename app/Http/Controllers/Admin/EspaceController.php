<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Espace;
use App\Http\Requests\StoreEspaceRequest;
use App\Http\Requests\UpdateEspaceRequest;
use Illuminate\Support\Facades\Cache;

class EspaceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $espaces = Espace::withCount('users')
                         ->orderBy('created_at', 'desc')
                         ->paginate(10);

        return view('admin.espaces.index', compact('espaces'));
    }

    public function create()
    {
        return view('admin.espaces.create');
    }

    public function store(StoreEspaceRequest $request)
    {
        try {
            Espace::create([
                'nom' => trim($request->nom),
                'email' => strtolower(trim($request->email)),
                'description' => $request->description,
                'is_active' => $request->boolean('is_active', true),
            ]);
            
            // Clear sidebar cache when espace data changes
            Cache::forget('admin_sidebar_counts');

            return redirect()->route('admin.espaces.index')
                           ->with('success', 'Espace créé avec succès.');

        } catch (\Exception $e) {
            \Log::error('Erreur création espace: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Erreur lors de la création de l\'espace.'])
                        ->withInput();
        }
    }

    public function show(Espace $espace)
    {
        // Charger les utilisateurs avec les informations de pivot (timestamps)
        $espace->load(['users' => function ($query) {
            $query->withPivot(['type', 'description', 'start_date', 'end_date', 'access_hours', 'created_at', 'updated_at'])
                  ->orderBy('attributions.created_at', 'desc');
        }]);
        
        return view('admin.espaces.show', compact('espace'));
    }

    public function edit(Espace $espace)
    {
        return view('admin.espaces.edit', compact('espace'));
    }

    public function update(UpdateEspaceRequest $request, Espace $espace)
    {
        try {
            $espace->update([
                'nom' => trim($request->nom),
                'email' => strtolower(trim($request->email)),
                'description' => $request->description,
                'is_active' => $request->boolean('is_active'),
            ]);

            return redirect()->route('admin.espaces.index')
                           ->with('success', 'Espace modifié avec succès.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de la modification de l\'espace.'])
                        ->withInput();
        }
    }

    public function destroy(Espace $espace)
    {
        // Vérifier si l'espace a des utilisateurs attribués
        if ($espace->users()->count() > 0) {
            return redirect()->route('admin.espaces.index')
                           ->with('error', 'Impossible de supprimer un espace ayant des utilisateurs attribués.');
        }

        try {
            $nomEspace = $espace->nom;
            $espace->delete();
            
            // Clear sidebar cache when espace data changes
            Cache::forget('admin_sidebar_counts');

            return redirect()->route('admin.espaces.index')
                           ->with('success', "L'espace {$nomEspace} a été supprimé avec succès.");

        } catch (\Exception $e) {
            return redirect()->route('admin.espaces.index')
                           ->with('error', 'Erreur lors de la suppression de l\'espace.');
        }
    }

    /**
     * Générer un email automatique basé sur le nom
     */
    public function generateEmail()
    {
        $nom = request('nom');
        if (!$nom) {
            return response()->json(['error' => 'Nom requis'], 400);
        }

        $espace = new Espace(['nom' => $nom]);
        $email = $espace->generateEmailFromName();
        
        return response()->json(['email' => $email]);
    }
}
