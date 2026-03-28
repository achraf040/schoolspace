<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - SUPMTI Workspace</title>
    
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
    
    <!-- Meta tags -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#059669">
    <meta name="description" content="Interface Travailleur SUPMTI - Gestion des tâches">
    
    @stack('styles')
</head>
<body class="supmti-navbar-reset" data-user-type="worker">
    
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
                <p class="supmti-brand-subtitle">Espace Travailleur</p>
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
                    <p class="supmti-user-role">{{ Auth::user()->account_type ?? 'Travailleur' }}</p>
                </div>
            </div>
            
            <!-- Navigation Menu -->
            <ul class="supmti-nav">
                <li class="supmti-nav-item">
                    <a href="{{ route('worker.dashboard') }}" 
                       class="supmti-nav-link {{ request()->routeIs('worker.dashboard') ? 'active' : '' }}"
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
                    <a href="{{ route('worker.tasks') }}" 
                       class="supmti-nav-link"
                       aria-label="Voir mes tâches">
                        <div class="supmti-nav-link-content">
                            <div class="supmti-nav-icon">
                                <i class="fas fa-tasks" aria-hidden="true"></i>
                            </div>
                            <span class="supmti-nav-text">Mes tâches</span>
                        </div>
                        @if(isset($stats) && isset($stats['active']) && $stats['active'] > 0)
                            <span class="supmti-badge warning">{{ $stats['active'] }}</span>
                        @endif
                    </a>
                </li>
                
                <li class="supmti-nav-item">
                    <a href="{{ route('worker.history') }}" 
                       class="supmti-nav-link"
                       aria-label="Historique des tâches">
                        <div class="supmti-nav-link-content">
                            <div class="supmti-nav-icon">
                                <i class="fas fa-history" aria-hidden="true"></i>
                            </div>
                            <span class="supmti-nav-text">Historique</span>
                        </div>
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
                    <h1 class="supmti-header-title">@yield('page-title', 'Espace de travail')</h1>
                </div>
                
                <div class="supmti-header-right">
                    <!-- Current Workspace/Espace Info -->
                    @if(isset($userEspace) && $userEspace)
                        <div class="supmti-space-info">
                            <i class="fas fa-building" aria-hidden="true"></i>
                            <span>{{ $userEspace->nom }}</span>
                        </div>
                    @else
                        <div class="supmti-space-info">
                            <i class="fas fa-user-cog" aria-hidden="true"></i>
                            <span>Travailleur</span>
                        </div>
                    @endif
                    
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
                                <h4><i class="fas fa-user-hard-hat"></i> Espace Travailleur</h4>
                                <div class="supmti-footer-user-info">
                                    <p><i class="fas fa-user"></i> {{ Auth::user()->name }}</p>
                                    <p><i class="fas fa-envelope"></i> {{ Auth::user()->email }}</p>
                                </div>
                            </div>
                            
                            <div class="supmti-footer-section">
                                <h4><i class="fas fa-info-circle"></i> Système</h4>
                                <div class="supmti-footer-links">
                                    <a href="#" onclick="showSystemInfo()"><i class="fas fa-cogs"></i> Informations</a>
                                    <a href="#" onclick="showHelp()"><i class="fas fa-question-circle"></i> Aide</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="supmti-footer-bottom">
                        <div class="supmti-footer-copyright">
                            <p>&copy; {{ date('Y') }} Summit - Tous droits réservés</p>
                            <p class="supmti-footer-version">Version 2.1.0 | Travailleur</p>
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
            alert('Informations Système\n\n' +
                  'Version: 2.1.0\n' +
                  'Type: Espace Travailleur\n' +
                  'Navigateur: ' + navigator.userAgent.split(' ').slice(-1)[0] + '\n' +
                  'Résolution: ' + screen.width + 'x' + screen.height + '\n' +
                  'Connexion: En ligne');
        }
        
        // Help modal
        function showHelp() {
            alert('Aide Espace Travailleur\n\n' +
                  '• Tableau de bord: Vue d\'ensemble de vos tâches\n' +
                  '• Mes tâches: Gestion de vos attributions\n' +
                  '• Historique: Consultation des tâches terminées\n\n' +
                  'Pour plus d\'aide, contactez l\'administrateur.');
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

        /* Welcome Section Styles */
        .welcome-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 32px;
            color: white;
            box-shadow: 0 8px 32px rgba(16, 185, 129, 0.3);
        }

        .welcome-content h2 {
            font-size: 1.75rem;
            font-weight: 700;
            margin: 0 0 8px 0;
            color: white !important;
        }

        .welcome-content p {
            font-size: 1rem;
            margin: 0;
            opacity: 0.9;
            color: white !important;
        }

        .welcome-time {
            text-align: right;
        }

        .welcome-time span {
            font-size: 0.875rem;
            opacity: 0.8;
            color: white !important;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            border: 1px solid #e5e7eb;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            color: white;
        }

        .stat-card.primary .stat-icon {
            background: #3b82f6;
        }

        .stat-card.success .stat-icon {
            background: #10b981;
        }

        .stat-card.warning .stat-icon {
            background: #f59e0b;
        }

        .stat-card.danger .stat-icon {
            background: #ef4444;
        }

        .stat-content h3 {
            font-size: 1.875rem;
            font-weight: 700;
            margin: 0 0 4px 0;
            color: #1f2937;
        }

        .stat-content p {
            font-size: 0.875rem;
            color: #6b7280;
            margin: 0;
        }

        /* Progress bars and indicators */
        .stat-progress {
            margin-top: 0.75rem;
        }

        .progress-bar {
            width: 100%;
            height: 4px;
            background: #e5e7eb;
            border-radius: 2px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #3b82f6, #1d4ed8);
            border-radius: 2px;
            transition: width 0.5s ease;
        }

        .stat-card.success .progress-fill {
            background: linear-gradient(90deg, #10b981, #059669);
        }

        .stat-card.warning .progress-fill {
            background: linear-gradient(90deg, #f59e0b, #d97706);
        }

        .stat-card.danger .progress-fill {
            background: linear-gradient(90deg, #ef4444, #dc2626);
        }

        .stat-trend {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 0.5rem;
            font-size: 0.8rem;
        }

        .stat-trend.positive {
            color: #10b981;
        }

        .stat-trend.negative {
            color: #ef4444;
        }

        .stat-trend.neutral {
            color: #6b7280;
        }

        /* Overall progress card */
        .overall-progress-card {
            background: white;
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 32px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            border: 1px solid #e5e7eb;
        }

        .progress-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }

        .progress-header h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1f2937;
            margin: 0;
        }

        .progress-header span {
            font-size: 1.5rem;
            font-weight: 700;
            color: #3b82f6;
        }

        .progress-container {
            margin-bottom: 12px;
        }

        .progress-track {
            width: 100%;
            height: 8px;
            background: #e5e7eb;
            border-radius: 4px;
            overflow: hidden;
        }

        .progress-fill-main {
            height: 100%;
            background: linear-gradient(90deg, #3b82f6, #1d4ed8);
            border-radius: 4px;
            transition: width 0.8s ease;
        }

        .progress-labels {
            display: flex;
            justify-content: space-between;
            font-size: 0.875rem;
            color: #6b7280;
        }

        /* Quick actions */
        .quick-actions {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 0.75rem;
            flex-wrap: wrap;
        }

        .completed-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #10b981;
            font-weight: 500;
            font-size: 0.875rem;
        }

        /* Animation classes */
        .stat-updated {
            animation: statPulse 0.6s ease;
        }

        @keyframes statPulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.2); }
            100% { transform: scale(1); }
        }

        .status-updated {
            animation: statusUpdateGlow 1s ease;
        }

        @keyframes statusUpdateGlow {
            0% { box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); }
            50% { box-shadow: 0 0 20px rgba(59, 130, 246, 0.3), 0 4px 12px rgba(0, 0, 0, 0.15); }
            100% { box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); }
        }

        /* Loading indicator */
        .loading-indicator {
            position: fixed;
            top: 20px;
            right: 20px;
            background: white;
            padding: 12px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            gap: 12px;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .loading-indicator.hidden {
            opacity: 0;
            transform: translateY(-10px);
            pointer-events: none;
        }

        .loading-spinner {
            width: 20px;
            height: 20px;
            border: 2px solid #e5e7eb;
            border-top: 2px solid #3b82f6;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Button enhancements */
        .btn {
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.3s, height 0.3s;
        }

        .btn:hover::before {
            width: 200px;
            height: 200px;
        }

        .btn-success {
            background: linear-gradient(135deg, #10b981, #059669);
        }

        .btn-warning {
            background: linear-gradient(135deg, #f59e0b, #d97706);
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .welcome-section {
                flex-direction: column;
                text-align: center;
                gap: 16px;
            }

            .welcome-time {
                text-align: center;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .quick-actions {
                flex-direction: column;
            }

            .loading-indicator {
                right: 10px;
                top: 10px;
                padding: 8px 16px;
            }
        }
    </style>
</body>
</html>