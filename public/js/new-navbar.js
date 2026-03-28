/**
 * SUPMTI - Modern Navbar JavaScript v2.0
 * Clean, performant navigation system
 */

class SupmtiNavbar {
    constructor() {
        this.sidebar = null;
        this.mobileToggle = null;
        this.mobileOverlay = null;
        this.isOpen = false;
        this.isMobile = false;
        this.resizeTimer = null;
        
        this.init();
    }

    init() {
        this.cacheDOMElements();
        this.checkMobileState();
        this.createMobileOverlay();
        this.bindEvents();
        this.setupAccessibility();
        this.updateBadges();
        
        // Add initialization class to prevent flash
        document.body.classList.add('supmti-navbar-ready');
        
    }

    cacheDOMElements() {
        this.sidebar = document.querySelector('.supmti-sidebar');
        this.mobileToggle = document.querySelector('.supmti-mobile-toggle');
        this.navLinks = document.querySelectorAll('.supmti-nav-link');
        this.header = document.querySelector('.supmti-header');
        this.body = document.body;
    }

    createMobileOverlay() {
        if (document.querySelector('.supmti-mobile-overlay')) return;
        
        const overlay = document.createElement('div');
        overlay.className = 'supmti-mobile-overlay';
        overlay.setAttribute('aria-hidden', 'true');
        document.body.appendChild(overlay);
        
        this.mobileOverlay = overlay;
    }

    bindEvents() {
        // Mobile toggle
        if (this.mobileToggle) {
            this.mobileToggle.addEventListener('click', (e) => {
                e.preventDefault();
                this.toggleMobileMenu();
            });
        }

        // Mobile overlay click to close
        if (this.mobileOverlay) {
            this.mobileOverlay.addEventListener('click', () => {
                this.closeMobileMenu();
            });
        }

        // Window resize
        window.addEventListener('resize', this.debounce(() => {
            this.handleResize();
        }, 150));

        // Simplified scroll handler for header effects
        this.boundScrollHandler = this.handleScroll.bind(this);
        window.addEventListener('scroll', this.boundScrollHandler, { passive: true });

        // Keyboard navigation
        document.addEventListener('keydown', (e) => {
            this.handleKeyboard(e);
        });

        // Navigation links
        this.navLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                // Close mobile menu when navigating
                if (this.isMobile && this.isOpen && !link.classList.contains('supmti-logout-btn')) {
                    setTimeout(() => {
                        this.closeMobileMenu();
                    }, 150);
                }
            });
        });

        // Forms (logout)
        const forms = document.querySelectorAll('.supmti-logout-form');
        forms.forEach(form => {
            form.addEventListener('submit', () => {
                this.showLoadingState();
            });
        });
    }

    checkMobileState() {
        const wasMobile = this.isMobile;
        this.isMobile = window.innerWidth <= 768;
        
        if (wasMobile && !this.isMobile && this.isOpen) {
            this.closeMobileMenu();
        }
    }

    toggleMobileMenu() {
        if (!this.isMobile) return;
        
        if (this.isOpen) {
            this.closeMobileMenu();
        } else {
            this.openMobileMenu();
        }
    }

    openMobileMenu() {
        if (!this.sidebar || !this.mobileOverlay) return;
        
        this.isOpen = true;
        
        // Add classes
        this.sidebar.classList.add('mobile-open');
        this.mobileOverlay.classList.add('active');
        this.body.classList.add('supmti-no-scroll');
        
        // Update ARIA states
        this.mobileToggle?.setAttribute('aria-expanded', 'true');
        this.sidebar?.setAttribute('aria-hidden', 'false');
        this.mobileOverlay?.setAttribute('aria-hidden', 'false');
        
        // Focus first nav link
        const firstLink = this.sidebar.querySelector('.supmti-nav-link');
        if (firstLink) {
            setTimeout(() => firstLink.focus(), 100);
        }
        
        this.announceToScreenReader('Menu ouvert');
    }

    closeMobileMenu() {
        if (!this.sidebar || !this.mobileOverlay) return;
        
        this.isOpen = false;
        
        // Remove classes
        this.sidebar.classList.remove('mobile-open');
        this.mobileOverlay.classList.remove('active');
        this.body.classList.remove('supmti-no-scroll');
        
        // Update ARIA states
        this.mobileToggle?.setAttribute('aria-expanded', 'false');
        this.sidebar?.setAttribute('aria-hidden', 'true');
        this.mobileOverlay?.setAttribute('aria-hidden', 'true');
        
        // Return focus to toggle
        if (this.mobileToggle) {
            setTimeout(() => this.mobileToggle.focus(), 100);
        }
        
        this.announceToScreenReader('Menu fermé');
    }

    handleResize() {
        this.checkMobileState();
        
        if (this.resizeTimer) {
            clearTimeout(this.resizeTimer);
        }
        
        this.resizeTimer = setTimeout(() => {
            this.updateLayout();
        }, 100);
    }

    handleScroll() {
        if (!this.header) {
            this.header = document.querySelector('.supmti-header');
        }
        
        if (this.header) {
            const scrollY = window.pageYOffset || document.documentElement.scrollTop;
            const shouldAddScrolled = scrollY > 10;
            
            if (shouldAddScrolled && !this.header.classList.contains('scrolled')) {
                this.header.classList.add('scrolled');
            } else if (!shouldAddScrolled && this.header.classList.contains('scrolled')) {
                this.header.classList.remove('scrolled');
            }
        }
    }

    updateLayout() {
        // Any layout updates needed after resize
        this.updateBadges();
    }

    handleKeyboard(event) {
        // ESC key closes mobile menu
        if (event.key === 'Escape' && this.isOpen) {
            this.closeMobileMenu();
            return;
        }
        
        // Alt + M toggles mobile menu
        if (event.altKey && event.key.toLowerCase() === 'm' && this.isMobile) {
            event.preventDefault();
            this.toggleMobileMenu();
            return;
        }
        
        // Navigation shortcuts (Alt + key)
        if (event.altKey && !event.ctrlKey && !event.shiftKey) {
            const shortcuts = {
                'd': this.getRouteUrl('dashboard'),
                'u': this.getRouteUrl('users'),
                'e': this.getRouteUrl('espaces'),
                't': this.getRouteUrl('attributions')
            };
            
            const key = event.key.toLowerCase();
            if (shortcuts[key]) {
                event.preventDefault();
                window.location.href = shortcuts[key];
                this.announceToScreenReader(`Navigation vers ${key.toUpperCase()}`);
            }
        }
    }

    getRouteUrl(route) {
        const baseUrl = window.location.origin;
        const userType = this.detectUserType();
        return `${baseUrl}/${userType}/${route}`;
    }

    detectUserType() {
        // Detect if admin or worker based on current URL or page context
        const currentPath = window.location.pathname;
        if (currentPath.includes('/admin/')) return 'admin';
        if (currentPath.includes('/worker/')) return 'worker';
        
        // Fallback based on page elements
        if (document.querySelector('[data-user-type]')) {
            return document.querySelector('[data-user-type]').dataset.userType;
        }
        
        return 'admin'; // Default fallback
    }

    updateBadges() {
        const badges = document.querySelectorAll('.supmti-badge');
        badges.forEach(badge => {
            // Clean badge content - keep only numbers
            const text = badge.textContent.trim();
            const cleanNumber = text.replace(/[^\d]/g, '');
            
            if (cleanNumber !== text && cleanNumber !== '') {
                badge.textContent = cleanNumber;
            }
            
            // Hide badge if zero
            if (cleanNumber === '0' || cleanNumber === '') {
                badge.style.display = 'none';
            } else {
                badge.style.display = 'inline-flex';
            }
        });
    }

    showLoadingState() {
        const logoutBtn = document.querySelector('.supmti-logout-btn');
        if (logoutBtn) {
            logoutBtn.style.opacity = '0.6';
            logoutBtn.style.pointerEvents = 'none';
            
            const icon = logoutBtn.querySelector('i');
            if (icon) {
                icon.className = 'fas fa-spinner fa-spin';
            }
        }
    }

    setupAccessibility() {
        // Set initial ARIA states
        if (this.mobileToggle) {
            this.mobileToggle.setAttribute('aria-expanded', 'false');
            this.mobileToggle.setAttribute('aria-label', 'Ouvrir le menu de navigation');
        }
        
        if (this.sidebar) {
            this.sidebar.setAttribute('role', 'navigation');
            this.sidebar.setAttribute('aria-label', 'Navigation principale');
            this.sidebar.setAttribute('aria-hidden', this.isMobile ? 'true' : 'false');
        }
        
        // Add keyboard navigation hints
        this.navLinks.forEach((link, index) => {
            if (!link.classList.contains('supmti-logout-btn')) {
                const shortcut = ['D', 'U', 'E', 'T'][index];
                if (shortcut) {
                    link.setAttribute('title', `Raccourci: Alt+${shortcut}`);
                }
            }
        });
        
        this.createSkipLink();
    }

    createSkipLink() {
        if (document.querySelector('.supmti-skip-link')) return;
        
        const skipLink = document.createElement('a');
        skipLink.href = '#supmti-main-content';
        skipLink.className = 'supmti-skip-link supmti-sr-only';
        skipLink.textContent = 'Passer au contenu principal';
        skipLink.style.cssText = `
            position: absolute;
            left: -9999px;
            z-index: 9999;
            padding: 8px 16px;
            background: var(--supmti-primary);
            color: white;
            text-decoration: none;
            border-radius: 4px;
        `;
        
        skipLink.addEventListener('focus', function() {
            this.style.left = '10px';
            this.style.top = '10px';
            this.classList.remove('supmti-sr-only');
        });
        
        skipLink.addEventListener('blur', function() {
            this.style.left = '-9999px';
            this.classList.add('supmti-sr-only');
        });
        
        document.body.insertBefore(skipLink, document.body.firstChild);
    }

    announceToScreenReader(message) {
        const announcement = document.createElement('div');
        announcement.setAttribute('aria-live', 'polite');
        announcement.setAttribute('aria-atomic', 'true');
        announcement.className = 'supmti-sr-only';
        announcement.textContent = message;
        
        document.body.appendChild(announcement);
        
        setTimeout(() => {
            if (announcement.parentNode) {
                announcement.remove();
            }
        }, 1000);
    }

    // Utility functions
    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    throttle(func, limit) {
        let inThrottle;
        return function(...args) {
            if (!inThrottle) {
                func.apply(this, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    }

    // Public API
    refresh() {
        this.updateBadges();
        this.checkMobileState();
    }

    destroy() {
        // Cleanup event listeners
        window.removeEventListener('resize', this.handleResize);
        window.removeEventListener('scroll', this.boundScrollHandler);
        document.removeEventListener('keydown', this.handleKeyboard);
        
        // Remove mobile overlay
        if (this.mobileOverlay && this.mobileOverlay.parentNode) {
            this.mobileOverlay.remove();
        }
        
        // Clear timers
        if (this.resizeTimer) {
            clearTimeout(this.resizeTimer);
        }
        
        // Remove body classes
        this.body.classList.remove('supmti-navbar-ready', 'supmti-no-scroll');
    }
}

// Auto-initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.supmtiNavbar = new SupmtiNavbar();
    });
} else {
    window.supmtiNavbar = new SupmtiNavbar();
}

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = SupmtiNavbar;
}