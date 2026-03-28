/**
 * DASHBOARD JAVASCRIPT
 * Gestion du dashboard administrateur avec graphiques et statistiques
 */

class Dashboard {
    constructor() {
        this.charts = {};
        this.currentPeriod = 'month';
        this.init();
    }

    init() {
        this.initStatistics();
        this.initCharts();
        this.initPeriodSelector();
        this.initRefreshButton();
        this.startAutoRefresh();
    }

    /**
     * Initialiser les statistiques
     */
    initStatistics() {
        this.loadStatistics();
        
        // Animation des compteurs
        this.animateCounters();
    }

    async loadStatistics() {
        try {
            const response = await fetch('/admin/dashboard/stats');
            const data = await response.json();
            
            this.updateStatistics(data);
        } catch (error) {
            console.error('Erreur lors du chargement des statistiques:', error);
        }
    }

    updateStatistics(data) {
        const statElements = {
            totalUsers: document.getElementById('total-users'),
            totalSpaces: document.getElementById('total-spaces'),
            totalAttributions: document.getElementById('total-attributions'),
            activeUsers: document.getElementById('active-users')
        };

        Object.keys(statElements).forEach(key => {
            if (statElements[key] && data[key] !== undefined) {
                statElements[key].textContent = data[key];
            }
        });
    }

    animateCounters() {
        const counters = document.querySelectorAll('.stat-number');
        
        counters.forEach(counter => {
            const target = parseInt(counter.textContent);
            const duration = 1000; // 1 seconde
            const increment = target / (duration / 16); // 60 FPS
            let current = 0;

            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    counter.textContent = target;
                    clearInterval(timer);
                } else {
                    counter.textContent = Math.floor(current);
                }
            }, 16);
        });
    }

    /**
     * Initialiser les graphiques
     */
    initCharts() {
        this.initUserChart();
        this.initSpaceChart();
        this.initAttributionChart();
    }

    initUserChart() {
        const canvas = document.getElementById('users-chart');
        if (!canvas) return;

        const ctx = canvas.getContext('2d');
        
        // Données de démonstration - à remplacer par des vraies données
        const data = {
            labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun'],
            datasets: [{
                label: 'Utilisateurs créés',
                data: [12, 19, 8, 15, 22, 18],
                borderColor: '#0f766e',
                backgroundColor: 'rgba(15, 118, 110, 0.1)',
                tension: 0.4,
                fill: true
            }]
        };

        this.charts.users = this.createChart(ctx, 'line', data);
    }

    initSpaceChart() {
        const canvas = document.getElementById('spaces-chart');
        if (!canvas) return;

        const ctx = canvas.getContext('2d');
        
        const data = {
            labels: ['Scolarité', 'Comptabilité', 'RH', 'Administration'],
            datasets: [{
                label: 'Utilisateurs par espace',
                data: [45, 23, 18, 12],
                backgroundColor: [
                    '#0f766e',
                    '#14b8a6',
                    '#06d6a0',
                    '#67e8f9'
                ],
                borderWidth: 0
            }]
        };

        this.charts.spaces = this.createChart(ctx, 'doughnut', data);
    }

    initAttributionChart() {
        const canvas = document.getElementById('attributions-chart');
        if (!canvas) return;

        const ctx = canvas.getContext('2d');
        
        const data = {
            labels: ['Permanente', 'Temporaire', 'Ponctuelle'],
            datasets: [{
                label: 'Types d\'attribution',
                data: [65, 25, 10],
                backgroundColor: [
                    '#10b981',
                    '#f59e0b',
                    '#ef4444'
                ]
            }]
        };

        this.charts.attributions = this.createChart(ctx, 'bar', data);
    }

    createChart(ctx, type, data) {
        // Note: Nécessite Chart.js
        // return new Chart(ctx, {
        //     type: type,
        //     data: data,
        //     options: {
        //         responsive: true,
        //         plugins: {
        //             legend: {
        //                 position: 'bottom'
        //             }
        //         }
        //     }
        // });

        // Fallback sans Chart.js - créer un graphique simple
        return this.createSimpleChart(ctx, type, data);
    }

    createSimpleChart(ctx, type, data) {
        const canvas = ctx.canvas;
        const width = canvas.width;
        const height = canvas.height;
        
        ctx.clearRect(0, 0, width, height);
        
        if (type === 'line') {
            this.drawLineChart(ctx, data, width, height);
        } else if (type === 'doughnut') {
            this.drawDoughnutChart(ctx, data, width, height);
        } else if (type === 'bar') {
            this.drawBarChart(ctx, data, width, height);
        }
        
        return { ctx, data, type };
    }

    drawLineChart(ctx, data, width, height) {
        const margin = 40;
        const chartWidth = width - 2 * margin;
        const chartHeight = height - 2 * margin;
        
        const maxValue = Math.max(...data.datasets[0].data);
        const points = data.datasets[0].data.map((value, index) => ({
            x: margin + (index * chartWidth) / (data.labels.length - 1),
            y: margin + chartHeight - (value / maxValue) * chartHeight
        }));

        // Dessiner la ligne
        ctx.strokeStyle = data.datasets[0].borderColor;
        ctx.lineWidth = 3;
        ctx.beginPath();
        ctx.moveTo(points[0].x, points[0].y);
        points.forEach(point => ctx.lineTo(point.x, point.y));
        ctx.stroke();

        // Dessiner les points
        ctx.fillStyle = data.datasets[0].borderColor;
        points.forEach(point => {
            ctx.beginPath();
            ctx.arc(point.x, point.y, 5, 0, 2 * Math.PI);
            ctx.fill();
        });
    }

    drawDoughnutChart(ctx, data, width, height) {
        const centerX = width / 2;
        const centerY = height / 2;
        const radius = Math.min(width, height) / 2 - 20;
        
        const total = data.datasets[0].data.reduce((sum, value) => sum + value, 0);
        let currentAngle = -Math.PI / 2;

        data.datasets[0].data.forEach((value, index) => {
            const sliceAngle = (value / total) * 2 * Math.PI;
            
            ctx.beginPath();
            ctx.arc(centerX, centerY, radius, currentAngle, currentAngle + sliceAngle);
            ctx.arc(centerX, centerY, radius * 0.6, currentAngle + sliceAngle, currentAngle, true);
            ctx.closePath();
            
            ctx.fillStyle = data.datasets[0].backgroundColor[index];
            ctx.fill();
            
            currentAngle += sliceAngle;
        });
    }

    drawBarChart(ctx, data, width, height) {
        const margin = 40;
        const chartWidth = width - 2 * margin;
        const chartHeight = height - 2 * margin;
        
        const maxValue = Math.max(...data.datasets[0].data);
        const barWidth = chartWidth / data.labels.length * 0.8;
        const barSpacing = chartWidth / data.labels.length * 0.2;

        data.datasets[0].data.forEach((value, index) => {
            const barHeight = (value / maxValue) * chartHeight;
            const x = margin + index * (barWidth + barSpacing) + barSpacing / 2;
            const y = margin + chartHeight - barHeight;
            
            ctx.fillStyle = data.datasets[0].backgroundColor[index];
            ctx.fillRect(x, y, barWidth, barHeight);
        });
    }

    /**
     * Sélecteur de période
     */
    initPeriodSelector() {
        const periodButtons = document.querySelectorAll('.period-btn');
        
        periodButtons.forEach(button => {
            button.addEventListener('click', () => {
                this.changePeriod(button.dataset.period);
                
                // Mise à jour des classes actives
                periodButtons.forEach(btn => btn.classList.remove('active'));
                button.classList.add('active');
            });
        });
    }

    changePeriod(period) {
        this.currentPeriod = period;
        this.refreshData();
    }

    /**
     * Bouton de rafraîchissement
     */
    initRefreshButton() {
        const refreshBtn = document.getElementById('refresh-dashboard');
        if (refreshBtn) {
            refreshBtn.addEventListener('click', () => {
                this.refreshData();
            });
        }
    }

    async refreshData() {
        const refreshBtn = document.getElementById('refresh-dashboard');
        if (refreshBtn) {
            refreshBtn.disabled = true;
            refreshBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        }

        try {
            await Promise.all([
                this.loadStatistics(),
                this.refreshCharts()
            ]);
        } catch (error) {
            console.error('Erreur lors du rafraîchissement:', error);
        } finally {
            if (refreshBtn) {
                refreshBtn.disabled = false;
                refreshBtn.innerHTML = '<i class="fas fa-sync-alt"></i>';
            }
        }
    }

    async refreshCharts() {
        // Recharger les données des graphiques
        Object.keys(this.charts).forEach(chartKey => {
            // Simuler le rechargement des données
            setTimeout(() => {
                // this.charts[chartKey].update();
            }, 100);
        });
    }

    /**
     * Auto-rafraîchissement
     */
    startAutoRefresh() {
        // Rafraîchir toutes les 5 minutes
        setInterval(() => {
            this.refreshData();
        }, 5 * 60 * 1000);
    }

    /**
     * Responsive handling
     */
    handleResize() {
        Object.keys(this.charts).forEach(chartKey => {
            if (this.charts[chartKey].resize) {
                this.charts[chartKey].resize();
            }
        });
    }
}

// Initialiser le dashboard
document.addEventListener('DOMContentLoaded', () => {
    window.dashboard = new Dashboard();
    
    // Gérer le redimensionnement
    window.addEventListener('resize', () => {
        if (window.dashboard) {
            window.dashboard.handleResize();
        }
    });
});

export default Dashboard;