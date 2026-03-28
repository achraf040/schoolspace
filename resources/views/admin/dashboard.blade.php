@extends('admin.layout')

@section('title', 'Dashboard')
@section('page-title', 'Tableau de Bord')

@section('content')
<!-- Statistiques principales améliorées -->
<div class="stats-overview">
    <div class="stats-header">
        <h2 class="stats-title">
            <i class="fas fa-chart-bar"></i>
            Statistiques Générales
        </h2>
        <div class="view-controls">
            <button class="view-btn active" data-view="cards">
                <i class="fas fa-th-large"></i>
                Cartes
            </button>
            <button class="view-btn" data-view="chart">
                <i class="fas fa-chart-line"></i>
                Graphique
            </button>
        </div>
    </div>

    <!-- Vue en cartes (par défaut) -->
    <div class="stats-cards-view active" id="cardsView">
        <div class="stats-grid-improved">
            <div class="stat-card-improved users-card">
                <div class="stat-icon-large">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content-improved">
                    <div class="stat-header-improved">
                        <h3>Utilisateurs</h3>
                        <span class="trend-badge positive">
                            <i class="fas fa-arrow-up"></i>
                            Actif
                        </span>
                    </div>
                    <div class="stat-main-number">{{ $totalUsers }}</div>
                    <div class="stat-details-improved">
                        <div class="detail-row">
                            <span class="detail-label">
                                <i class="fas fa-circle" style="color: #10b981;"></i>
                                Actifs
                            </span>
                            <span class="detail-value">{{ $activeUsers }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">
                                <i class="fas fa-circle" style="color: #ef4444;"></i>
                                Inactifs
                            </span>
                            <span class="detail-value">{{ $inactiveUsers }}</span>
                        </div>
                    </div>
                    <div class="progress-bar-simple">
                        <div class="progress-fill-simple" style="width: {{ $totalUsers > 0 ? ($activeUsers / $totalUsers) * 100 : 0 }}%;"></div>
                    </div>
                </div>
            </div>

            <div class="stat-card-improved spaces-card">
                <div class="stat-icon-large">
                    <i class="fas fa-building"></i>
                </div>
                <div class="stat-content-improved">
                    <div class="stat-header-improved">
                        <h3>Espaces</h3>
                        <span class="trend-badge positive">
                            <i class="fas fa-check"></i>
                            Complet
                        </span>
                    </div>
                    <div class="stat-main-number">{{ $totalEspaces }}</div>
                    <div class="stat-details-improved">
                        @if($espacesStats->count() > 0)
                            @foreach($espacesStats->take(2) as $espace)
                            <div class="detail-row">
                                <span class="detail-label">{{ Str::limit($espace->nom, 20) }}</span>
                                <span class="detail-value">{{ $espace->users_count }} users</span>
                            </div>
                            @endforeach
                        @else
                            <div class="detail-row">
                                <span class="detail-label">Aucun espace configuré</span>
                                <span class="detail-value">-</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="stat-card-improved attributions-card">
                <div class="stat-icon-large">
                    <i class="fas fa-link"></i>
                </div>
                <div class="stat-content-improved">
                    <div class="stat-header-improved">
                        <h3>Attributions</h3>
                        <span class="trend-badge neutral">
                            <i class="fas fa-minus"></i>
                            Stable
                        </span>
                    </div>
                    <div class="stat-main-number">{{ $totalAttributions }}</div>
                    <div class="stat-details-improved">
                        <div class="detail-row">
                            <span class="detail-label">Récentes (7j)</span>
                            <span class="detail-value">{{ $attributionsRecentes->count() }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Par utilisateur</span>
                            <span class="detail-value">{{ number_format($totalUsers > 0 ? ($totalAttributions / $totalUsers) : 0, 1) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="stat-card-improved performance-card">
                <div class="stat-icon-large">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-content-improved">
                    <div class="stat-header-improved">
                        <h3>Performance</h3>
                        <span class="trend-badge {{ $totalAttributions > $totalUsers ? 'positive' : 'neutral' }}">
                            <i class="fas fa-{{ $totalAttributions > $totalUsers ? 'arrow-up' : 'equals' }}"></i>
                            {{ $totalAttributions > $totalUsers ? 'Optimal' : 'Standard' }}
                        </span>
                    </div>
                    <div class="stat-main-number">{{ number_format($totalUsers > 0 ? ($totalAttributions / $totalUsers) : 0, 1) }}</div>
                    <div class="stat-details-improved">
                        <div class="detail-row">
                            <span class="detail-label">Objectif</span>
                            <span class="detail-value">2.5</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Écart</span>
                            <span class="detail-value {{ ($totalUsers > 0 ? ($totalAttributions / $totalUsers) : 0) >= 2.5 ? 'positive' : 'negative' }}">
                                {{ ($totalUsers > 0 ? ($totalAttributions / $totalUsers) : 0) >= 2.5 ? '+' : '' }}{{ number_format((($totalUsers > 0 ? ($totalAttributions / $totalUsers) : 0) - 2.5), 1) }}
                            </span>
                        </div>
                    </div>
                    <div class="gauge-simple">
                        @php
                            $ratio = $totalUsers > 0 ? ($totalAttributions / $totalUsers) : 0;
                            $percentage = min(($ratio / 3) * 100, 100);
                        @endphp
                        <div class="gauge-fill" style="width: {{ $percentage }}%;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Vue graphique -->
    <div class="stats-chart-view" id="chartView">
        <div class="chart-container-simple">
            <div class="chart-controls">
                <div class="metric-selector">
                    <label>Métrique :</label>
                    <select id="metricSelect" class="metric-select">
                        <option value="users">Utilisateurs ({{ $totalUsers }})</option>
                        <option value="spaces">Espaces ({{ $totalEspaces }})</option>
                        <option value="attributions">Attributions ({{ $totalAttributions }})</option>
                    </select>
                </div>
                <div class="chart-info" id="chartInfo">
                    <div class="current-metric">
                        <span class="metric-name">Utilisateurs</span>
                        <span class="metric-total">{{ $totalUsers }}</span>
                    </div>
                </div>
            </div>
            
            <div class="simple-chart" id="simpleChart">
                <div class="chart-bars">
                    <!-- Les barres seront générées dynamiquement par JavaScript -->
                    <div class="chart-placeholder">
                        <div class="placeholder-icon">
                            <i class="fas fa-chart-bar" style="font-size: 48px; color: var(--gray-400);"></i>
                        </div>
                        <p style="color: var(--gray-500); margin-top: 16px;">Cliquez sur "Graphique" pour afficher les données</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script corrigé pour les interactions -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Dashboard script loaded');
    
    // Données pour les différentes vues
    const chartData = {
        users: {
            bars: [
                { label: 'Actifs', value: {{ $activeUsers }}, height: {{ $totalUsers > 0 ? ($activeUsers / $totalUsers) * 100 : 0 }}, class: 'users-bar' },
                { label: 'Inactifs', value: {{ $inactiveUsers }}, height: {{ $totalUsers > 0 ? ($inactiveUsers / $totalUsers) * 100 : 0 }}, class: 'users-bar-inactive' }
            ]
        },
        spaces: {
            bars: [
                @foreach($espacesStats->take(4) as $index => $espace)
                { 
                    label: '{{ Str::limit($espace->nom, 8) }}', 
                    value: {{ $espace->users_count }}, 
                    height: {{ $espacesStats->max('users_count') > 0 ? ($espace->users_count / $espacesStats->max('users_count')) * 100 : 0 }}, 
                    class: 'spaces-bar' 
                }{{ !$loop->last ? ',' : '' }}
                @endforeach
            ]
        },
        attributions: {
            bars: [
                { label: 'Total', value: {{ $totalAttributions }}, height: 80, class: 'attributions-bar' },
                { label: 'Récentes', value: {{ $attributionsRecentes->count() }}, height: {{ $totalAttributions > 0 ? ($attributionsRecentes->count() / $totalAttributions) * 100 : 30 }}, class: 'attributions-bar-recent' }
            ]
        }
    };
    
    // Basculer entre vue cartes et graphique
    const viewBtns = document.querySelectorAll('.view-btn');
    const cardsView = document.getElementById('cardsView');
    const chartView = document.getElementById('chartView');
    
    console.log('Found view buttons:', viewBtns.length);
    console.log('Cards view element:', cardsView);
    console.log('Chart view element:', chartView);
    
    if (viewBtns.length > 0 && cardsView && chartView) {
        viewBtns.forEach((btn, index) => {
            console.log(`Button ${index}:`, btn.dataset.view);
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const view = this.dataset.view;
                console.log('Switching to view:', view);
                
                // Mise à jour des boutons
                viewBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                // Basculer les vues avec debug
                if (view === 'cards') {
                    console.log('Switching to cards view');
                    cardsView.classList.add('active');
                    chartView.classList.remove('active');
                } else if (view === 'chart') {
                    console.log('Switching to chart view');
                    cardsView.classList.remove('active');
                    chartView.classList.add('active');
                    
                    // Forcer un reflow pour s'assurer que le DOM est mis à jour
                    chartView.offsetHeight;
                    
                    // Initialiser le graphique immédiatement
                    console.log('Initializing chart...');
                    initChart('users');
                    setTimeout(() => {
                        animateBars();
                    }, 100);
                }
                
                console.log('Cards view active:', cardsView.classList.contains('active'));
                console.log('Chart view active:', chartView.classList.contains('active'));
            });
        });
    } else {
        console.error('Missing elements - Buttons:', viewBtns.length, 'Cards:', !!cardsView, 'Chart:', !!chartView);
    }
    
    // Changer de métrique dans le graphique
    const metricSelect = document.getElementById('metricSelect');
    if (metricSelect) {
        metricSelect.addEventListener('change', function() {
            const selectedMetric = this.value;
            console.log('Changing metric to:', selectedMetric);
            initChart(selectedMetric);
            updateChartInfo(selectedMetric);
            setTimeout(animateBars, 100);
        });
    }
    
    function initChart(metric) {
        console.log('initChart called with metric:', metric);
        const chartBars = document.querySelector('.chart-bars');
        
        if (!chartBars) {
            console.error('Chart bars container not found');
            return;
        }
        
        if (!chartData[metric]) {
            console.error('Data not found for metric:', metric);
            console.log('Available metrics:', Object.keys(chartData));
            chartBars.innerHTML = '<div class="chart-placeholder"><div class="placeholder-icon"><i class="fas fa-exclamation-triangle" style="font-size: 48px; color: var(--warning-500);"></i></div><p style="color: var(--gray-500); margin-top: 16px;">Données non disponibles pour cette métrique</p></div>';
            return;
        }
        
        const bars = chartData[metric].bars;
        console.log('Bars data for metric', metric, ':', bars);
        
        if (!bars || bars.length === 0) {
            console.warn('No bars data for metric:', metric);
            chartBars.innerHTML = '<div class="chart-placeholder"><div class="placeholder-icon"><i class="fas fa-chart-bar" style="font-size: 48px; color: var(--gray-400);"></i></div><p style="color: var(--gray-500); margin-top: 16px;">Aucune donnée disponible</p></div>';
            return;
        }
        
        let html = '';
        let maxValue = Math.max(...bars.map(bar => bar.value || 0));
        
        bars.forEach((bar, index) => {
            // Calculer la hauteur relative avec une hauteur minimum de 10%
            let height = maxValue > 0 ? Math.max((bar.value / maxValue) * 100, 10) : 10;
            
            html += `
                <div class="bar-group" data-label="${bar.label}">
                    <div class="bar ${bar.class}" 
                         style="height: ${height}%; transform: scaleY(0);" 
                         data-value="${bar.value}">
                    </div>
                    <span class="bar-label">${bar.label}</span>
                    <span class="bar-value">${bar.value}</span>
                </div>
            `;
        });
        
        console.log('Generated HTML:', html.substring(0, 200) + '...');
        chartBars.innerHTML = html;
        
        // Ajouter les tooltips et interactions
        const newBars = chartBars.querySelectorAll('.bar');
        console.log('Found', newBars.length, 'bars after initialization');
        
        newBars.forEach(bar => {
            bar.addEventListener('mouseenter', function() {
                const value = this.dataset.value;
                const label = this.parentElement.dataset.label;
                this.title = `${label}: ${value}`;
            });
        });
        
        // Déclencher un reflow pour s'assurer que les styles sont appliqués
        chartBars.offsetHeight;
    }
    
    function updateChartInfo(metric) {
        const chartInfo = document.getElementById('chartInfo');
        if (!chartInfo) return;
        
        const metricName = chartInfo.querySelector('.metric-name');
        const metricTotal = chartInfo.querySelector('.metric-total');
        
        const info = {
            users: { name: 'Utilisateurs', total: '{{ $totalUsers }}' },
            spaces: { name: 'Espaces', total: '{{ $totalEspaces }}' },
            attributions: { name: 'Attributions', total: '{{ $totalAttributions }}' }
        };
        
        if (metricName && info[metric]) metricName.textContent = info[metric].name;
        if (metricTotal && info[metric]) metricTotal.textContent = info[metric].total;
    }
    
    function animateBars() {
        const bars = document.querySelectorAll('.bar');
        bars.forEach((bar, index) => {
            setTimeout(() => {
                bar.style.transform = 'scaleY(1)';
            }, index * 150);
        });
    }
    
    // Animation initiale des barres de progression
    setTimeout(() => {
        const progressBars = document.querySelectorAll('.progress-fill-simple, .gauge-fill');
        progressBars.forEach(bar => {
            bar.style.transform = 'scaleX(1)';
        });
    }, 1000);
    
    // Préparation des données du graphique pour s'assurer qu'elles sont valides
    console.log('Chart data validation:');
    Object.keys(chartData).forEach(metric => {
        const data = chartData[metric];
        console.log(`${metric}:`, data);
        if (data && data.bars && data.bars.length > 0) {
            console.log(`✓ ${metric} has ${data.bars.length} bars`);
        } else {
            console.warn(`⚠ ${metric} has no valid data`);
        }
    });
    
    console.log('Dashboard script initialization complete');
});
</script>

<!-- Actions rapides améliorées -->
<div class="content-section">
    <div class="section-header">
        <h2 class="section-title">
            <i class="fas fa-bolt" style="color: #f59e0b; margin-right: 8px;"></i>
            Actions Rapides
        </h2>
    </div>
    <div class="form-container">
        <div class="quick-actions">
            <x-admin.quick-action 
                :route="route('admin.users.create')"
                title="Nouveau Utilisateur"
                description="Créer un compte utilisateur"
                icon="user-plus"
                type="primary" />

            <x-admin.quick-action 
                :route="route('admin.espaces.create')"
                title="Nouvel Espace"
                description="Créer un espace de travail"
                icon="plus-circle"
                type="success" />

            <x-admin.quick-action 
                :route="route('admin.attributions.index')"
                title="Attributions"
                description="Gérer les accès aux espaces"
                icon="link"
                type="warning" />

            <x-admin.quick-action 
                :route="route('admin.users.index')"
                title="Gestion Utilisateurs"
                description="Voir tous les utilisateurs"
                icon="users-cog"
                type="info" />
        </div>
    </div>
</div>

<!-- Aperçu des données récentes -->
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px;">
    <!-- Utilisateurs récents -->
    <div class="content-section">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-user-clock" style="color: #3b82f6; margin-right: 8px;"></i>
                Utilisateurs Récents
            </h2>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary" style="font-size: 12px; padding: 6px 12px;">
                Voir tout
            </a>
        </div>
        <div class="form-container">
            @if($recentUsers->count() > 0)
                <div class="recent-list">
                    @foreach($recentUsers as $user)
                    <div class="recent-item">
                        <div class="recent-avatar">
                            @if($user->hasProfilePhoto())
                                <img src="{{ $user->profile_photo_url }}" alt="Photo de {{ $user->name }}">
                            @else
                                <div class="avatar-placeholder">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <div class="recent-info">
                            <div class="recent-name">{{ $user->name }}</div>
                            <div class="recent-details">{{ $user->email }}</div>
                            <div class="recent-time">{{ $user->created_at->diffForHumans() }}</div>
                        </div>
                        <div class="recent-status">
                            <span class="badge {{ $user->is_active ? 'badge-success' : 'badge-warning' }}">
                                {{ $user->is_active ? 'Actif' : 'Inactif' }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-user-plus"></i>
                    <p>Aucun utilisateur récent</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Répartition des espaces -->
    <div class="content-section">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-chart-pie" style="color: #10b981; margin-right: 8px;"></i>
                Répartition par Espace
            </h2>
        </div>
        <div class="form-container">
            @if($espacesStats->count() > 0)
                <div class="space-stats">
                    @foreach($espacesStats as $espace)
                    <div class="space-stat-item">
                        <div class="space-info">
                            <div class="space-name">{{ $espace->nom }}</div>
                            <div class="space-count">{{ $espace->users_count }} utilisateurs</div>
                        </div>
                        <div class="space-progress">
                            <div class="space-bar">
                                <div class="space-fill" style="width: {{ $totalUsers > 0 ? ($espace->users_count / $totalUsers) * 100 : 0 }}%"></div>
                            </div>
                            <div class="space-percentage">{{ $totalUsers > 0 ? round(($espace->users_count / $totalUsers) * 100) : 0 }}%</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-building"></i>
                    <p>Aucun espace configuré</p>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
/* Statistiques améliorées et simplifiées */
.stats-overview {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-xl);
    border: 1px solid rgba(255, 255, 255, 0.2);
    padding: 32px;
    margin-bottom: 32px;
    position: relative;
    overflow: hidden;
}

.stats-overview::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #3b82f6, #10b981, #f59e0b, #8b5cf6);
    opacity: 0.8;
}

/* En-tête */
.stats-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 32px;
}

.stats-title {
    font-size: 24px;
    font-weight: 700;
    color: var(--gray-800);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 12px;
}

.stats-title i {
    color: var(--primary-600);
}

.view-controls {
    display: flex;
    gap: 8px;
    background: var(--gray-100);
    padding: 4px;
    border-radius: var(--radius-lg);
    box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
}

.view-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    border: none;
    background: transparent;
    border-radius: var(--radius);
    font-size: 14px;
    font-weight: 600;
    color: var(--gray-600);
    cursor: pointer;
    transition: all var(--transition-normal);
}

.view-btn.active,
.view-btn:hover {
    background: white;
    color: var(--primary-700);
    box-shadow: var(--shadow-sm);
    transform: translateY(-1px);
}

/* Vue cartes */
.stats-cards-view {
    display: none;
}

.stats-cards-view.active {
    display: block;
    animation: fadeIn 0.3s ease;
}

.stats-grid-improved {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 24px;
}

.stat-card-improved {
    background: white;
    border-radius: var(--radius-xl);
    padding: 24px;
    box-shadow: var(--shadow-md);
    border: 2px solid transparent;
    transition: all var(--transition-normal);
    position: relative;
    overflow: hidden;
}

.stat-card-improved:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-xl);
    border-color: var(--card-color);
}

.stat-card-improved::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--card-color);
    opacity: 0.8;
}

.users-card {
    --card-color: #3b82f6;
}

.spaces-card {
    --card-color: #10b981;
}

.attributions-card {
    --card-color: #f59e0b;
}

.performance-card {
    --card-color: #8b5cf6;
}

.stat-icon-large {
    width: 64px;
    height: 64px;
    border-radius: var(--radius-lg);
    background: var(--card-color);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    margin-bottom: 20px;
    box-shadow: var(--shadow-lg);
}

.stat-content-improved {
    flex: 1;
}

.stat-header-improved {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
}

.stat-header-improved h3 {
    font-size: 18px;
    font-weight: 600;
    color: var(--gray-700);
    margin: 0;
}

.trend-badge {
    display: flex;
    align-items: center;
    gap: 4px;
    padding: 4px 10px;
    border-radius: var(--radius);
    font-size: 12px;
    font-weight: 600;
}

.trend-badge.positive {
    background: var(--success-100);
    color: var(--success-600);
}

.trend-badge.neutral {
    background: var(--gray-100);
    color: var(--gray-600);
}

.trend-badge.negative {
    background: var(--danger-100);
    color: var(--danger-600);
}

.stat-main-number {
    font-size: 48px;
    font-weight: 700;
    color: var(--gray-800);
    line-height: 1;
    margin-bottom: 20px;
}

.stat-details-improved {
    margin-bottom: 16px;
}

.detail-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
    border-bottom: 1px solid var(--gray-100);
}

.detail-row:last-child {
    border-bottom: none;
}

.detail-label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    color: var(--gray-600);
    font-weight: 500;
}

.detail-value {
    font-size: 16px;
    font-weight: 600;
    color: var(--gray-800);
}

.detail-value.positive {
    color: var(--success-600);
}

.detail-value.negative {
    color: var(--danger-600);
}

/* Barres de progression simples */
.progress-bar-simple {
    height: 8px;
    background: var(--gray-200);
    border-radius: 4px;
    overflow: hidden;
}

.progress-fill-simple {
    height: 100%;
    background: linear-gradient(90deg, var(--card-color), var(--card-color));
    border-radius: 4px;
    transition: width 1s ease-in-out;
    transform: scaleX(0);
    transform-origin: left;
}

.gauge-simple {
    height: 8px;
    background: var(--gray-200);
    border-radius: 4px;
    overflow: hidden;
    position: relative;
}

.gauge-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--danger-500), var(--warning-500), var(--success-500));
    border-radius: 4px;
    transition: width 1s ease-in-out;
    transform: scaleX(0);
    transform-origin: left;
}

/* Vue graphique */
.stats-chart-view {
    display: none;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
}

.stats-chart-view.active {
    display: block !important;
    opacity: 1;
    visibility: visible;
    animation: fadeIn 0.3s ease;
}

.chart-container-simple {
    background: white;
    border-radius: var(--radius-lg);
    padding: 24px;
    box-shadow: var(--shadow-md);
    border: 1px solid var(--gray-200);
}

.chart-controls {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    padding-bottom: 16px;
    border-bottom: 2px solid var(--gray-100);
}

.metric-selector label {
    font-size: 14px;
    font-weight: 600;
    color: var(--gray-700);
    margin-right: 12px;
}

.metric-select {
    padding: 8px 12px;
    border: 2px solid var(--gray-200);
    border-radius: var(--radius);
    font-size: 14px;
    font-weight: 500;
    background: white;
    color: var(--gray-700);
    cursor: pointer;
    transition: all var(--transition-normal);
}

.metric-select:focus {
    outline: none;
    border-color: var(--primary-500);
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.chart-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.current-metric {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    background: var(--primary-50);
    border-radius: var(--radius-lg);
    border: 1px solid var(--primary-200);
}

.metric-name {
    font-size: 14px;
    font-weight: 600;
    color: var(--primary-700);
}

.metric-total {
    font-size: 18px;
    font-weight: 700;
    color: var(--primary-800);
}

/* Graphique à barres simple */
.simple-chart {
    height: 300px;
    display: flex;
    align-items: end;
    justify-content: center;
    padding: 20px;
    background: var(--gray-50);
    border-radius: var(--radius-lg);
    border: 2px solid var(--gray-100);
}

.chart-bars {
    display: flex;
    align-items: end;
    gap: 20px;
    height: 100%;
    width: 100%;
    justify-content: center;
}

.bar-group {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    min-width: 60px;
}

.bar {
    width: 40px;
    min-height: 20px;
    background: var(--bar-color, #3b82f6);
    border-radius: 4px 4px 0 0;
    transition: all 0.5s ease;
    transform: scaleY(0);
    transform-origin: bottom;
    box-shadow: var(--shadow-sm);
    position: relative;
    cursor: pointer;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.bar:hover {
    transform: scaleY(1.05);
    box-shadow: var(--shadow-md);
}

.users-bar {
    --bar-color: #3b82f6;
}

.users-bar-inactive {
    --bar-color: #ef4444;
}

.spaces-bar {
    --bar-color: #10b981;
}

.attributions-bar {
    --bar-color: #f59e0b;
}

.attributions-bar-recent {
    --bar-color: #fb923c;
}

.bar-label {
    font-size: 12px;
    font-weight: 600;
    color: var(--gray-600);
    text-align: center;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.bar-value {
    font-size: 16px;
    font-weight: 700;
    color: var(--gray-800);
    background: white;
    padding: 4px 8px;
    border-radius: var(--radius);
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--gray-200);
}

/* Placeholder du graphique */
.chart-placeholder {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
    width: 100%;
}

.placeholder-icon {
    opacity: 0.6;
    margin-bottom: 16px;
}

/* Responsive */
@media (max-width: 768px) {
    .stats-overview {
        padding: 20px;
    }
    
    .stats-header {
        flex-direction: column;
        gap: 16px;
        align-items: stretch;
    }
    
    .stats-grid-improved {
        grid-template-columns: 1fr;
        gap: 16px;
    }
    
    .stat-main-number {
        font-size: 36px;
    }
    
    .chart-controls {
        flex-direction: column;
        gap: 16px;
        align-items: stretch;
    }
    
    .simple-chart {
        height: 250px;
    }
    
    .chart-bars {
        gap: 12px;
    }
    
    .bar {
        width: 30px;
    }
    
    .bar-group {
        min-width: 50px;
    }
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Graphique unifié moderne */
.unified-chart-container {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-xl);
    border: 1px solid rgba(255, 255, 255, 0.2);
    padding: 32px;
    margin-bottom: 32px;
    position: relative;
    overflow: hidden;
}

.unified-chart-container::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--primary-500), var(--info-500), var(--success-500), var(--warning-500));
    opacity: 0.8;
}

/* En-tête du graphique */
.chart-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 32px;
}

.chart-title {
    font-size: 24px;
    font-weight: 700;
    color: var(--gray-800);
    margin: 0;
    display: flex;
    align-items: center;
}

.chart-period {
    display: flex;
    gap: 8px;
    background: var(--gray-100);
    padding: 4px;
    border-radius: var(--radius-lg);
}

.period-btn {
    padding: 8px 16px;
    border: none;
    background: transparent;
    border-radius: var(--radius);
    font-size: 14px;
    font-weight: 500;
    color: var(--gray-600);
    cursor: pointer;
    transition: all var(--transition-normal);
}

.period-btn.active,
.period-btn:hover {
    background: white;
    color: var(--primary-700);
    box-shadow: var(--shadow-sm);
}

/* Navigation des métriques */
.metrics-nav {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    margin-bottom: 32px;
}

.metric-btn {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 20px;
    background: var(--gray-50);
    border: 2px solid transparent;
    border-radius: var(--radius-lg);
    cursor: pointer;
    transition: all var(--transition-normal);
    position: relative;
    overflow: hidden;
}

.metric-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--metric-color, var(--gray-400));
    transform: scaleX(0);
    transition: transform var(--transition-normal);
}

.metric-btn:hover::before,
.metric-btn.active::before {
    transform: scaleX(1);
}

.metric-btn.active {
    background: white;
    border-color: rgba(0, 0, 0, 0.1);
    box-shadow: var(--shadow-md);
    transform: translateY(-2px);
}

.metric-btn[data-color="#3b82f6"] {
    --metric-color: #3b82f6;
}

.metric-btn[data-color="#10b981"] {
    --metric-color: #10b981;
}

.metric-btn[data-color="#f59e0b"] {
    --metric-color: #f59e0b;
}

.metric-btn[data-color="#8b5cf6"] {
    --metric-color: #8b5cf6;
}

.metric-icon {
    width: 48px;
    height: 48px;
    border-radius: var(--radius-lg);
    background: var(--metric-color);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
    box-shadow: var(--shadow);
}

.metric-info {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.metric-label {
    font-size: 14px;
    font-weight: 600;
    color: var(--gray-600);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.metric-value {
    font-size: 28px;
    font-weight: 700;
    color: var(--gray-800);
    line-height: 1;
}

.metric-trend {
    padding: 4px 8px;
    border-radius: var(--radius);
    font-size: 12px;
    font-weight: 600;
    align-self: flex-start;
}

.metric-trend.positive {
    background: var(--success-100);
    color: var(--success-600);
}

.metric-trend.neutral {
    background: var(--gray-100);
    color: var(--gray-600);
}

/* Graphique principal */
.main-chart {
    margin-bottom: 32px;
}

.chart-canvas {
    height: 400px;
    margin-bottom: 16px;
    position: relative;
}

.chart-legend {
    display: flex;
    justify-content: center;
    gap: 24px;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    border-radius: var(--radius-lg);
    background: var(--gray-50);
    transition: all var(--transition-normal);
}

.legend-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
}

.legend-label {
    font-size: 14px;
    color: var(--gray-600);
    font-weight: 500;
}

.legend-value {
    font-size: 16px;
    font-weight: 700;
    color: var(--gray-800);
}

/* Statistiques détaillées */
.detailed-stats {
    position: relative;
}

.stat-card-mini {
    display: none;
    animation: fadeInUp 0.3s ease;
}

.stat-card-mini.active {
    display: block;
}

.stat-breakdown {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
}

.breakdown-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 20px;
    background: var(--gray-50);
    border-radius: var(--radius-lg);
    border-left: 4px solid var(--primary-500);
    transition: all var(--transition-normal);
}

.breakdown-item:hover {
    background: white;
    box-shadow: var(--shadow-sm);
    transform: translateY(-1px);
}

.item-label {
    font-size: 14px;
    color: var(--gray-600);
    font-weight: 500;
}

.item-value {
    font-size: 18px;
    font-weight: 700;
    color: var(--gray-800);
}

.item-percentage {
    font-size: 12px;
    font-weight: 600;
    padding: 2px 6px;
    border-radius: var(--radius);
    background: var(--success-100);
    color: var(--success-600);
}

/* Responsive design */
@media (max-width: 1024px) {
    .metrics-nav {
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }
    
    .metric-btn {
        padding: 16px;
        gap: 12px;
    }
    
    .metric-value {
        font-size: 24px;
    }
    
    .chart-canvas {
        height: 300px;
    }
}

@media (max-width: 768px) {
    .unified-chart-container {
        padding: 20px;
    }
    
    .chart-header {
        flex-direction: column;
        gap: 16px;
        align-items: stretch;
    }
    
    .metrics-nav {
        grid-template-columns: 1fr;
    }
    
    .metric-btn {
        padding: 16px;
    }
    
    .metric-value {
        font-size: 20px;
    }
    
    .chart-canvas {
        height: 250px;
    }
    
    .stat-breakdown {
        grid-template-columns: 1fr;
        gap: 12px;
    }
    
    .breakdown-item {
        padding: 12px 16px;
    }
    
    .chart-legend {
        flex-direction: column;
        align-items: center;
        gap: 12px;
    }
}

@media (max-width: 480px) {
    .chart-period {
        width: 100%;
        justify-content: space-between;
    }
    
    .period-btn {
        flex: 1;
        text-align: center;
    }
    
    .metric-icon {
        width: 40px;
        height: 40px;
        font-size: 18px;
    }
    
    .metric-value {
        font-size: 18px;
    }
    
    .chart-canvas {
        height: 200px;
    }
}

/* Animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.unified-chart-container {
    animation: fadeInUp 0.6s ease;
}

.metric-btn {
    animation: fadeInUp 0.4s ease;
    animation-fill-mode: both;
}

.metric-btn:nth-child(1) { animation-delay: 0.1s; }
.metric-btn:nth-child(2) { animation-delay: 0.2s; }
.metric-btn:nth-child(3) { animation-delay: 0.3s; }
.metric-btn:nth-child(4) { animation-delay: 0.4s; }

/* Cartes statistiques modernes avec graphiques */
.modern-stat-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-lg);
    border: 1px solid rgba(255, 255, 255, 0.2);
    padding: 24px;
    transition: all var(--transition-normal);
    position: relative;
    overflow: hidden;
    min-height: 320px;
    display: flex;
    flex-direction: column;
}

.modern-stat-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-xl);
    border-color: rgba(255, 255, 255, 0.3);
}

.modern-stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--card-gradient);
    opacity: 0.8;
}

.modern-stat-card:hover::before {
    opacity: 1;
}

/* Couleurs thématiques */
.users-card {
    --card-gradient: linear-gradient(90deg, var(--info-500), #60a5fa);
}

.spaces-card {
    --card-gradient: linear-gradient(90deg, var(--success-500), #34d399);
}

.attributions-card {
    --card-gradient: linear-gradient(90deg, var(--warning-500), #fbbf24);
}

.performance-card {
    --card-gradient: linear-gradient(90deg, #8b5cf6, #a78bfa);
}

/* En-tête des cartes */
.stat-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: var(--radius-lg);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: white;
    background: var(--card-gradient);
    box-shadow: var(--shadow);
}

.stat-trend {
    display: flex;
    align-items: center;
    gap: 4px;
    padding: 4px 8px;
    border-radius: var(--radius);
    font-size: 12px;
    font-weight: 600;
}

.stat-trend.positive {
    background: var(--success-100);
    color: var(--success-500);
}

.stat-trend.neutral {
    background: var(--gray-100);
    color: var(--gray-500);
}

.stat-trend.negative {
    background: var(--danger-100);
    color: var(--danger-500);
}

/* Contenu des cartes */
.stat-content {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.stat-title {
    font-size: 14px;
    font-weight: 600;
    color: var(--gray-600);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 8px;
}

.stat-value {
    font-size: 36px;
    font-weight: 700;
    color: var(--gray-800);
    margin-bottom: 20px;
    line-height: 1;
}

.stat-summary {
    margin-top: auto;
    font-size: 12px;
    color: var(--gray-500);
    text-align: center;
}

/* Graphique en anneau (donut) */
.donut-chart-container {
    display: flex;
    justify-content: center;
    margin: 20px 0;
}

.donut-chart {
    position: relative;
    width: 120px;
    height: 120px;
}

.donut {
    width: 100%;
    height: 100%;
    transform: rotate(-90deg);
}

.donut-segment {
    transition: stroke-dasharray 1s ease-in-out;
}

.donut-center {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
}

.donut-center .percentage {
    display: block;
    font-size: 20px;
    font-weight: 700;
    color: var(--gray-800);
}

.donut-center .label {
    display: block;
    font-size: 12px;
    color: var(--gray-500);
    text-transform: uppercase;
}

/* Détails des statistiques */
.stat-details {
    display: flex;
    justify-content: space-around;
    margin-top: 16px;
}

.detail-item {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    color: var(--gray-600);
}

.detail-item .dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
}

.detail-item.active .dot {
    background: var(--info-500);
}

.detail-item.inactive .dot {
    background: var(--gray-400);
}

/* Graphique à barres horizontales */
.bar-chart-container {
    margin: 20px 0;
}

.bar-item {
    margin-bottom: 12px;
}

.bar-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 4px;
}

.bar-label {
    font-size: 12px;
    color: var(--gray-600);
    font-weight: 500;
}

.bar-value {
    font-size: 12px;
    color: var(--gray-800);
    font-weight: 600;
}

.bar-track {
    height: 6px;
    background: var(--gray-200);
    border-radius: 3px;
    overflow: hidden;
}

.bar-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--success-500), var(--success-400));
    border-radius: 3px;
    transition: width 1s ease-in-out;
}

/* Graphique linéaire */
.line-chart-container {
    margin: 20px 0;
    height: 60px;
}

.line-chart {
    width: 100%;
    height: 100%;
}

.line-chart path {
    transition: all 1s ease-in-out;
}

.line-chart circle {
    transition: all 0.3s ease;
}

.line-chart circle:hover {
    r: 4;
    fill: var(--warning-600);
}

/* Gauge circulaire */
.gauge-container {
    display: flex;
    justify-content: center;
    margin: 20px 0;
}

.gauge {
    width: 140px;
    height: 80px;
}

.gauge-svg {
    width: 100%;
    height: 100%;
}

.gauge path:last-of-type {
    transition: stroke-dasharray 1.5s ease-in-out;
}

.gauge line {
    transition: transform 1.5s ease-in-out;
}

/* Animations d'entrée */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideInLeft {
    from {
        opacity: 0;
        transform: translateX(-30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes scaleIn {
    from {
        opacity: 0;
        transform: scale(0.8);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

.modern-stat-card:nth-child(1) {
    animation: fadeInUp 0.6s ease 0.1s both;
}

.modern-stat-card:nth-child(2) {
    animation: fadeInUp 0.6s ease 0.2s both;
}

.modern-stat-card:nth-child(3) {
    animation: fadeInUp 0.6s ease 0.3s both;
}

.modern-stat-card:nth-child(4) {
    animation: fadeInUp 0.6s ease 0.4s both;
}

/* Responsive design pour les nouvelles cartes */
@media (max-width: 768px) {
    .modern-stat-card {
        min-height: 280px;
        padding: 20px;
    }
    
    .stat-value {
        font-size: 28px;
    }
    
    .donut-chart {
        width: 100px;
        height: 100px;
    }
    
    .donut-center .percentage {
        font-size: 16px;
    }
    
    .gauge {
        width: 120px;
        height: 70px;
    }
}

/* Amélioration des cartes statistiques */
.stat-card {
    position: relative;
    overflow: hidden;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--card-color), var(--card-color-light));
}

.stat-card.users {
    --card-color: #3b82f6;
    --card-color-light: #60a5fa;
}

.stat-card.espaces {
    --card-color: #10b981;
    --card-color-light: #34d399;
}

.stat-card.attributions {
    --card-color: #f59e0b;
    --card-color-light: #fbbf24;
}

.stat-card.performance {
    --card-color: #8b5cf6;
    --card-color-light: #a78bfa;
}

.stat-content {
    display: flex;
    align-items: flex-start;
    gap: 16px;
    margin-bottom: 16px;
}

.stat-details {
    flex: 1;
}

.stat-breakdown {
    display: flex;
    gap: 12px;
    margin-top: 8px;
}

.stat-item {
    font-size: 12px;
    color: #6b7280;
    padding: 2px 6px;
    background: #f3f4f6;
    border-radius: 4px;
}

.stat-item.active {
    color: #059669;
    background: #ecfdf5;
}

.stat-item.inactive {
    color: #d97706;
    background: #fffbeb;
}

.stat-progress {
    margin-top: 12px;
}

.progress-bar {
    height: 4px;
    background: #e5e7eb;
    border-radius: 2px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background: var(--card-color);
    transition: width 0.3s ease;
}

.stat-trend {
    position: absolute;
    top: 16px;
    right: 16px;
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 12px;
    font-weight: 500;
}

.stat-trend.positive {
    color: #059669;
}

.stat-trend.neutral {
    color: #6b7280;
}

.stat-chart {
    position: absolute;
    bottom: 16px;
    right: 16px;
}

.mini-chart {
    display: flex;
    align-items: end;
    gap: 2px;
    height: 24px;
}

.mini-chart .bar {
    width: 3px;
    background: var(--card-color);
    opacity: 0.6;
    border-radius: 1px;
}

/* Actions rapides améliorées */
.quick-actions {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 16px;
}

.quick-action-card {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 20px;
    background: white;
    border-radius: 12px;
    border: 2px solid #e5e7eb;
    text-decoration: none;
    color: inherit;
    transition: all 0.2s ease;
    position: relative;
    overflow: hidden;
}

.quick-action-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--action-color);
    transform: translateX(-100%);
    transition: transform 0.3s ease;
}

.quick-action-card:hover::before {
    transform: translateX(0);
}

.quick-action-card:hover {
    border-color: var(--action-color);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.quick-action-card.primary {
    --action-color: #3b82f6;
}

.quick-action-card.success {
    --action-color: #10b981;
}

.quick-action-card.warning {
    --action-color: #f59e0b;
}

.quick-action-card.info {
    --action-color: #8b5cf6;
}

.action-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: var(--action-color);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
}

.action-content {
    flex: 1;
}

.action-content h3 {
    margin: 0 0 4px 0;
    font-size: 16px;
    font-weight: 600;
    color: #1f2937;
}

.action-content p {
    margin: 0;
    font-size: 14px;
    color: #6b7280;
}

.action-arrow {
    color: var(--action-color);
    font-size: 18px;
    opacity: 0;
    transform: translateX(-10px);
    transition: all 0.2s ease;
}

.quick-action-card:hover .action-arrow {
    opacity: 1;
    transform: translateX(0);
}

/* Listes récentes */
.recent-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.recent-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    background: #f9fafb;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
}

.recent-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #0f766e;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    flex-shrink: 0;
    overflow: hidden;
}

.recent-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
}

.recent-avatar .avatar-placeholder {
    width: 100%;
    height: 100%;
    background: rgba(15, 118, 110, 0.8);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    font-weight: 600;
    border-radius: 50%;
}

.recent-info {
    flex: 1;
}

.recent-name {
    font-weight: 500;
    color: #1f2937;
    font-size: 14px;
}

.recent-details {
    color: #6b7280;
    font-size: 12px;
    margin-top: 2px;
}

.recent-time {
    color: #9ca3af;
    font-size: 11px;
    margin-top: 2px;
}

.recent-status {
    flex-shrink: 0;
}

/* Statistiques des espaces */
.space-stats {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.space-stat-item {
    display: flex;
    align-items: center;
    gap: 16px;
}

.space-info {
    flex: 1;
}

.space-name {
    font-weight: 500;
    color: #1f2937;
    font-size: 14px;
}

.space-count {
    color: #6b7280;
    font-size: 12px;
    margin-top: 2px;
}

.space-progress {
    display: flex;
    align-items: center;
    gap: 8px;
    min-width: 120px;
}

.space-bar {
    flex: 1;
    height: 6px;
    background: #e5e7eb;
    border-radius: 3px;
    overflow: hidden;
}

.space-fill {
    height: 100%;
    background: #10b981;
    border-radius: 3px;
    transition: width 0.3s ease;
}

.space-percentage {
    font-size: 12px;
    font-weight: 500;
    color: #6b7280;
    min-width: 32px;
    text-align: right;
}

.empty-state {
    text-align: center;
    padding: 32px;
    color: #9ca3af;
}

.empty-state i {
    font-size: 32px;
    margin-bottom: 12px;
    display: block;
}

.empty-state p {
    margin: 0;
    font-size: 14px;
}

/* Enhanced mobile responsiveness */
@media (max-width: 768px) {
    .stats-grid-improved {
        grid-template-columns: 1fr !important;
        gap: 16px;
    }
    
    .stats-overview {
        margin: var(--space-3) !important;
        padding: var(--space-4) !important;
    }
    
    .stats-header {
        margin-bottom: 20px;
    }
    
    .stats-title {
        font-size: 18px;
    }
    
    .view-controls {
        flex-wrap: wrap;
        gap: 6px;
    }
    
    .view-btn {
        padding: 8px 12px;
        font-size: 13px;
    }
    
    .stat-main-number {
        font-size: 32px !important;
    }
    
    .quick-actions {
        grid-template-columns: 1fr !important;
        gap: 12px;
    }
    
    .quick-action-card {
        padding: 16px;
        gap: 12px;
    }
    
    .action-icon {
        width: 40px;
        height: 40px;
        font-size: 18px;
    }
    
    .action-content h3 {
        font-size: 14px;
    }
    
    .action-content p {
        font-size: 12px;
    }
    
    /* Two column layout becomes single column */
    div[style*="grid-template-columns: 1fr 1fr"] {
        display: block !important;
    }
    
    div[style*="grid-template-columns: 1fr 1fr"] > * {
        margin-bottom: 16px;
    }
    
    .section-title {
        font-size: 16px;
    }
    
    .recent-item {
        padding: 10px;
        gap: 10px;
    }
    
    .recent-name {
        font-size: 13px;
    }
    
    .recent-details,
    .recent-time {
        font-size: 11px;
    }
    
    .space-stat-item {
        gap: 12px;
    }
    
    .space-name {
        font-size: 13px;
    }
    
    .space-count {
        font-size: 11px;
    }
}

@media (max-width: 480px) {
    .stats-overview {
        margin: var(--space-2) !important;
        padding: var(--space-3) !important;
    }
    
    .stats-title {
        font-size: 16px;
        gap: 8px;
    }
    
    .stats-header {
        flex-direction: column;
        gap: 12px;
        align-items: stretch;
    }
    
    .view-controls {
        justify-content: center;
    }
    
    .stat-card-improved {
        padding: 16px;
        min-height: auto;
    }
    
    .stat-icon-large {
        width: 48px;
        height: 48px;
        font-size: 22px;
    }
    
    .stat-main-number {
        font-size: 28px !important;
        margin-bottom: 12px;
    }
    
    .detail-row {
        padding: 6px 0;
    }
    
    .detail-label {
        font-size: 12px;
    }
    
    .detail-value {
        font-size: 14px;
    }
    
    .chart-container-simple {
        padding: 16px;
    }
    
    .simple-chart {
        height: 200px;
        padding: 12px;
    }
    
    .bar {
        width: 30px;
    }
    
    .bar-group {
        min-width: 45px;
        gap: 6px;
    }
    
    .bar-label {
        font-size: 10px;
    }
    
    .bar-value {
        font-size: 12px;
        padding: 2px 6px;
    }
    
    .content-section {
        margin: var(--space-3) var(--space-2);
    }
    
    .form-container {
        padding: var(--space-3);
    }
}
</style>
@endsection