<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Espace;
use App\Models\Attribution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        
        // Share sidebar counts with all admin views using cached data
        View::composer('admin.*', function ($view) {
            $sidebarCounts = Cache::remember('admin_sidebar_counts', 300, function () { // Cache for 5 minutes
                return [
                    'users' => User::where('role', 'user')->count(),
                    'espaces' => Espace::where('is_active', true)->count(),
                    'tasks' => Attribution::count()
                ];
            });
            
            $view->with('sidebarCounts', $sidebarCounts);
        });
    }

    public function dashboard()
    {
        // Count all users (including admin)
        $totalUsers = User::count();
        $activeUsers = User::where('is_active', true)->count();
        $inactiveUsers = $totalUsers - $activeUsers;
        
        $totalEspaces = Espace::where('is_active', true)->count();
        $totalAttributions = Attribution::count();
        
        // Recent users (excluding administrators)
        $recentUsers = User::where('role', '!=', 'admin')
                          ->orderBy('created_at', 'desc')
                          ->limit(5)
                          ->get();
                          
        $espacesStats = Espace::withCount('users')
                             ->where('is_active', true)
                             ->get();
                             
        $attributionsRecentes = Attribution::with(['user', 'espace'])
                                          ->orderBy('created_at', 'desc')
                                          ->limit(5)
                                          ->get();

        return view('admin.dashboard', compact(
            'totalUsers', 'activeUsers', 'inactiveUsers',
            'totalEspaces', 'totalAttributions', 'recentUsers',
            'espacesStats', 'attributionsRecentes'
        ));
    }
}