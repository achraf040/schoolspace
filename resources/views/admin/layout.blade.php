<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - SUPMTI Admin</title>
    
    <!-- Favicons -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.svg') }}">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- New Navbar CSS -->
    <link rel="stylesheet" href="{{ asset('css/new-navbar.css') }}">
    
    <!-- Admin CSS -->
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    
    <!-- Meta tags -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#059669">
    <meta name="description" content="Interface d'administration SUPMTI - Gestion des espaces et utilisateurs">
    
    @stack('styles')
</head>
<body class="supmti-navbar-reset" data-user-type="admin">
    <!-- Skip Link for Accessibility -->
    <div class="supmti-layout">
        
        <!-- Mobile Toggle Button -->
        <button class="supmti-mobile-toggle" aria-label="Toggle navigation menu">
            <i class="fas fa-bars"></i>
        </button>
        
        <!-- Sidebar Navigation -->
        <nav class="supmti-sidebar" role="navigation" aria-label="Main navigation">
            
            <!-- Brand Section -->
            <div class="supmti-brand">
                <div class="supmti-brand-logo">
                    <img src="{{ asset('images/Logosup.png') }}" alt="SUPMTI Logo">
                </div>
                <h1 class="supmti-brand-title">SUPMTI</h1>
                <p class="supmti-brand-subtitle">Administration</p>
            </div>
            
            <!-- User Info Section -->
            <div class="supmti-user-section">
                <div class="supmti-user-avatar">
                    @if(Auth::user()->hasProfilePhoto())
                        <img src="{{ Auth::user()->profile_photo_url }}" alt="Photo de profil de {{ Auth::user()->name }}">
                    @else
                        <div class="supmti-user-placeholder">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                    @endif
                </div>
                <div class="supmti-user-info">
                    <h2 class="supmti-user-name">{{ Auth::user()->name }}</h2>
                    <p class="supmti-user-role">{{ Auth::user()->account_type ?? 'Administrateur' }}</p>
                </div>
            </div>
            
            <!-- Navigation Menu -->
            <ul class="supmti-nav">
                <li class="supmti-nav-item">
                    <a href="{{ route('admin.dashboard') }}" 
                       class="supmti-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                       aria-label="Accéder au tableau de bord">
                        <div class="supmti-nav-link-content">
                            <div class="supmti-nav-icon">
                                <i class="fas fa-chart-line" aria-hidden="true"></i>
                            </div>
                            <span class="supmti-nav-text">Tableau de bord</span>
                        </div>
                    </a>
                </li>
                
                <li class="supmti-nav-item">
                    <a href="{{ route('admin.users.index') }}" 
                       class="supmti-nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
                       aria-label="Gérer les utilisateurs">
                        <div class="supmti-nav-link-content">
                            <div class="supmti-nav-icon">
                                <i class="fas fa-user-friends" aria-hidden="true"></i>
                            </div>
                            <span class="supmti-nav-text">Utilisateurs</span>
                        </div>
                        @if(isset($sidebarCounts['users']) && $sidebarCounts['users'] > 0)
                            <span class="supmti-badge">{{ $sidebarCounts['users'] }}</span>
                        @endif
                    </a>
                </li>
                
                <li class="supmti-nav-item">
                    <a href="{{ route('admin.espaces.index') }}" 
                       class="supmti-nav-link {{ request()->routeIs('admin.espaces.*') ? 'active' : '' }}"
                       aria-label="Gérer les espaces">
                        <div class="supmti-nav-link-content">
                            <div class="supmti-nav-icon">
                                <i class="fas fa-map-marked-alt" aria-hidden="true"></i>
                            </div>
                            <span class="supmti-nav-text">Espaces</span>
                        </div>
                        @if(isset($sidebarCounts['espaces']) && $sidebarCounts['espaces'] > 0)
                            <span class="supmti-badge success">{{ $sidebarCounts['espaces'] }}</span>
                        @endif
                    </a>
                </li>
                
                <li class="supmti-nav-item">
                    <a href="{{ route('admin.attributions.index') }}" 
                       class="supmti-nav-link {{ request()->routeIs('admin.attributions.*') ? 'active' : '' }}"
                       aria-label="Gérer les tâches et attributions">
                        <div class="supmti-nav-link-content">
                            <div class="supmti-nav-icon">
                                <i class="fas fa-clipboard-list" aria-hidden="true"></i>
                            </div>
                            <span class="supmti-nav-text">Tâches</span>
                        </div>
                        @if(isset($sidebarCounts['tasks']) && $sidebarCounts['tasks'] > 0)
                            <span class="supmti-badge warning">{{ $sidebarCounts['tasks'] }}</span>
                        @endif
                    </a>
                </li>
                
                <!-- Navigation Separator -->
                <li><div class="supmti-nav-separator" role="separator"></div></li>
                
                <!-- Logout Section -->
                <li class="supmti-nav-item">
                    <form method="POST" action="{{ route('logout') }}" class="supmti-logout-form">
                        @csrf
                        <button type="submit" class="supmti-nav-link supmti-logout-btn" aria-label="Se déconnecter">
                            <div class="supmti-nav-link-content">
                                <div class="supmti-nav-icon">
                                    <i class="fas fa-power-off" aria-hidden="true"></i>
                                </div>
                                <span class="supmti-nav-text">Déconnexion</span>
                            </div>
                        </button>
                    </form>
                </li>
            </ul>
        </nav>
        
        <!-- Main Content Area -->
        <main class="supmti-main">
            
            <!-- Header -->
            <header class="supmti-header">
                <div class="supmti-header-left">
                    <h1 class="supmti-header-title">@yield('page-title', 'Administration')</h1>
                </div>
                
                <div class="supmti-header-right">
                    <!-- Admin Info Badge -->
                    <div class="supmti-space-info">
                        <i class="fas fa-user-shield" aria-hidden="true"></i>
                        <span>Administration</span>
                    </div>
                    
                    <!-- Header User Info -->
                    <div class="supmti-header-user">
                        <div class="supmti-header-avatar">
                            @if(Auth::user()->hasProfilePhoto())
                                <img src="{{ Auth::user()->profile_photo_url }}" alt="Photo de profil">
                            @else
                                <div class="supmti-header-avatar-placeholder">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Page Content -->
            <div class="supmti-content" id="supmti-main-content">
                
                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="alert alert-success" role="alert">
                        <i class="fas fa-check-circle" aria-hidden="true"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-circle" aria-hidden="true"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif
                
                @if(session('warning'))
                    <div class="alert alert-warning" role="alert">
                        <i class="fas fa-exclamation-triangle" aria-hidden="true"></i>
                        <span>{{ session('warning') }}</span>
                    </div>
                @endif
                
                @if(session('info'))
                    <div class="alert alert-info" role="alert">
                        <i class="fas fa-info-circle" aria-hidden="true"></i>
                        <span>{{ session('info') }}</span>
                    </div>
                @endif
                
                <!-- Main Content -->
                @yield('content')
                
            </div>
            
            <!-- Enhanced Footer -->
            <footer class="supmti-footer-enhanced">
                <div class="supmti-footer-pattern"></div>
                <div class="supmti-footer-content">
                    <div class="supmti-footer-main">
                        <div class="supmti-footer-brand">
                            <div class="supmti-footer-logo">
                                <img src="{{ asset('images/Logosup.png') }}" alt="SUPMTI Logo">
                            </div>
                            <div class="supmti-footer-brand-text">
                                <h3>SUPMTI</h3>
                                <p>École Supérieure Privée de Management et de Technologie de l'Information</p>
                            </div>
                        </div>
                        
                        <div class="supmti-footer-info">
                            <div class="supmti-footer-section">
                                <h4><i class="fas fa-shield-alt"></i> Administration</h4>
                                <div class="supmti-footer-user-info">
                                    <p><i class="fas fa-user-shield"></i> {{ Auth::user()->name }}</p>
                                    <p><i class="fas fa-envelope"></i> {{ Auth::user()->email }}</p>
                                </div>
                            </div>
                            
                            <div class="supmti-footer-section">
                                <h4><i class="fas fa-chart-bar"></i> Statistiques</h4>
                                <div class="supmti-footer-stats">
                                    <p><i class="fas fa-users"></i> Utilisateurs actifs</p>
                                    <p><i class="fas fa-building"></i> Espaces gérés</p>
                                </div>
                            </div>
                            
                            <div class="supmti-footer-section">
                                <h4><i class="fas fa-tools"></i> Outils Admin</h4>
                                <div class="supmti-footer-links">
                                    <a href="#" onclick="showSystemInfo()"><i class="fas fa-server"></i> État système</a>
                                    <a href="#" onclick="showLogs()"><i class="fas fa-file-alt"></i> Journaux</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="supmti-footer-bottom">
                        <div class="supmti-footer-copyright">
                            <p>&copy; {{ date('Y') }} Summit - Tous droits réservés</p>
                            <p class="supmti-footer-version">Version 2.1.0 | Administration</p>
                        </div>
                        <div class="supmti-footer-status">
                            <span class="supmti-footer-status-indicator online">
                                <i class="fas fa-circle"></i> En ligne
                            </span>
                            <span class="supmti-footer-time" id="current-time"></span>
                        </div>
                    </div>
                </div>
            </footer>
        </main>
    </div>
    
    <!-- New Navbar JavaScript -->
    <script src="{{ asset('js/new-navbar.js') }}" defer></script>
    
    <!-- Enhanced Footer JavaScript -->
    <script>
        // Update time in footer
        function updateFooterTime() {
            const timeElement = document.getElementById('current-time');
            if (timeElement) {
                const now = new Date();
                const timeString = now.toLocaleTimeString('fr-FR', { 
                    hour: '2-digit', 
                    minute: '2-digit',
                    second: '2-digit'
                });
                timeElement.textContent = timeString;
            }
        }
        
        // System info modal
        function showSystemInfo() {
            alert('État du Système\n\n' +
                  'Version: 2.1.0\n' +
                  'Type: Interface Administration\n' +
                  'Serveur: Laravel ' + '{{ app()->version() }}' + '\n' +
                  'Base de données: Connectée\n' +
                  'Cache: Actif\n' +
                  'Statut: Opérationnel');
        }
        
        // Logs modal
        function showLogs() {
            alert('Journaux Système\n\n' +
                  'Dernières activités:\n' +
                  '• Connexion admin: {{ Auth::user()->name }}\n' +
                  '• Dernière sauvegarde: Aujourd\'hui\n' +
                  '• Maintenance: Planifiée pour demain\n\n' +
                  'Consultez les journaux complets dans le panneau d\'administration.');
        }
        
        // Initialize footer
        document.addEventListener('DOMContentLoaded', function() {
            updateFooterTime();
            setInterval(updateFooterTime, 1000); // Update every second
        });
    </script>
    
    @stack('scripts')
    
    <!-- Additional Styles for Alerts -->
    <style>
        .alert {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 24px;
            border: 1px solid;
            font-weight: 500;
        }
        
        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border-color: #34d399;
        }
        
        .alert-danger {
            background: #fee2e2;
            color: #991b1b;
            border-color: #f87171;
        }
        
        .alert-warning {
            background: #fef3c7;
            color: #92400e;
            border-color: #fbbf24;
        }
        
        .alert-info {
            background: #dbeafe;
            color: #1e40af;
            border-color: #60a5fa;
        }
        
        .alert i {
            font-size: 18px;
            flex-shrink: 0;
        }
    </style>
</body>
</html>