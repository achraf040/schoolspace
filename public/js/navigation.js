/**
 * SUPMTI Navigation System - Optimized & Mobile-First
 * Handles sidebar navigation, mobile menu, and responsive behavior
 */

class NavigationManager {
    constructor() {
        this.sidebar = null;
        this.mobileToggle = null;
        this.mobileOverlay = null;
        this.sidebarToggle = null;
        this.isOpen = false;
        this.isMobile = false;
        this.resizeTimer = null;
        
        this.init();
    }

    init() {
        this.cacheDOMElements();
        this.bindEvents();
        this.checkMobileState();
        this.createMobileOverlay();
        this.setupAccessibility();
    }

    cacheDOMElements() {
        this.sidebar = document.getElementById('sidebar');
        this.mobileToggle = document.getElementById('mobileMenuToggle');
        this.sidebarToggle = document.getElementById('sidebarToggle');
        
        // Cache frequently accessed elements
        this.body = document.body;
        this.navLinks = document.querySelectorAll('.nav-link') || [];
        
    }

    createMobileOverlay() {
        if (this.mobileOverlay) return;
        
        this.mobileOverlay = document.createElement('div');
        this.mobileOverlay.className = 'mobile-overlay';
        this.mobileOverlay.setAttribute('aria-hidden', 'true');
        document.body.appendChild(this.mobileOverlay);
    }

    bindEvents() {
        // Mobile menu toggle
        if (this.mobileToggle) {
            this.mobileToggle.addEventListener('click', 
                this.throttle(this.toggleMobileMenu.bind(this), 300)
            );
        }

        // Sidebar toggle (desktop)
        if (this.sidebarToggle) {
            this.sidebarToggle.addEventListener('click',
                this.throttle(this.toggleSidebar.bind(this), 300)
            );
        }

        // Mobile overlay click to close
        if (this.mobileOverlay) {
            this.mobileOverlay.addEventListener('click', () => {
                this.closeMobileMenu();
            });
        }

        // Window resize with debouncing
        window.addEventListener('resize', 
            this.debounce(this.handleResize.bind(this), 150)
        );

        // Keyboard navigation
        document.addEventListener('keydown', this.handleKeyboard.bind(this));

        // Close mobile menu when clicking nav links (but allow normal navigation)
        this.navLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                // Only prevent default for logout button, not navigation links
                if (link.classList.contains('logout-btn')) {
                    // Let form submission handle logout
                    return;
                }
                
                // For mobile: close menu after clicking nav link (but allow navigation)
                if (this.isMobile && this.isOpen) {
                    // Small delay to allow link navigation to start
                    setTimeout(() => {
                        this.closeMobileMenu();
                    }, 100);
                }
                
                // Don't prevent default - allow normal navigation
            });
        });

        // Focus trap for mobile menu
        this.sidebar?.addEventListener('keydown', this.handleFocusTrap.bind(this));
    }

    checkMobileState() {
        const wasMobile = this.isMobile;
        this.isMobile = window.innerWidth <= 768;
        
        // If switching from mobile to desktop, ensure proper state
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
        
        // Add classes with RAF for smooth animation
        requestAnimationFrame(() => {
            this.sidebar.classList.add('mobile-open');
            this.mobileOverlay.classList.add('show');
            this.body.classList.add('no-scroll');
        });
        
        // Update ARIA states
        this.mobileToggle?.setAttribute('aria-expanded', 'true');
        this.sidebar?.setAttribute('aria-hidden', 'false');
        this.mobileOverlay?.setAttribute('aria-hidden', 'false');
        
        // Focus first nav link
        const firstLink = this.sidebar.querySelector('.nav-link');
        firstLink?.focus();
        
        // Announce to screen readers
        this.announceToScreenReader('Menu ouvert');
    }

    closeMobileMenu() {
        if (!this.sidebar || !this.mobileOverlay) return;
        
        this.isOpen = false;
        
        // Remove classes
        this.sidebar.classList.remove('mobile-open');
        this.mobileOverlay.classList.remove('show');
        this.body.classList.remove('no-scroll');
        
        // Update ARIA states
        this.mobileToggle?.setAttribute('aria-expanded', 'false');
        this.sidebar?.setAttribute('aria-hidden', 'true');
        this.mobileOverlay?.setAttribute('aria-hidden', 'true');
        
        // Return focus to toggle button
        this.mobileToggle?.focus();
        
        // Announce to screen readers
        this.announceToScreenReader('Menu fermé');
    }

    toggleSidebar() {
        if (this.isMobile) return;
        
        this.body.classList.toggle('sidebar-collapsed');
        const isCollapsed = this.body.classList.contains('sidebar-collapsed');
        
        // Update ARIA state
        this.sidebarToggle?.setAttribute('aria-expanded', (!isCollapsed).toString());
        
        // Store preference
        try {
            localStorage.setItem('sidebarCollapsed', isCollapsed.toString());
        } catch (e) {
            // Ignore localStorage errors
        }
    }

    handleResize() {
        const oldMobileState = this.isMobile;
        this.checkMobileState();
        
        // If transitioning from mobile to desktop
        if (oldMobileState && !this.isMobile) {
            this.closeMobileMenu();
        }
        
        // Clear resize timer
        if (this.resizeTimer) {
            clearTimeout(this.resizeTimer);
        }
        
        // Debounced resize actions
        this.resizeTimer = setTimeout(() => {
            this.updateLayout();
        }, 100);
    }

    updateLayout() {
        // Recalculate layout if needed
        if (!this.isMobile) {
            this.restoreSidebarState();
        }
    }

    restoreSidebarState() {
        try {
            const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            if (isCollapsed) {
                this.body.classList.add('sidebar-collapsed');
                this.sidebarToggle?.setAttribute('aria-expanded', 'false');
            }
        } catch (e) {
            // Ignore localStorage errors
        }
    }

    handleKeyboard(event) {
        // ESC key closes mobile menu
        if (event.key === 'Escape' && this.isOpen) {
            this.closeMobileMenu();
            return;
        }
        
        // Alt + M toggles mobile menu (accessibility shortcut)
        if (event.altKey && event.key === 'm' && this.isMobile) {
            event.preventDefault();
            this.toggleMobileMenu();
            return;
        }
        
        // Navigation shortcuts (Alt + key)
        if (event.altKey && !event.ctrlKey && !event.shiftKey) {
            const shortcuts = {
                'd': '/admin/dashboard',
                'u': '/admin/users',
                'e': '/admin/espaces', 
                't': '/admin/attributions'
            };
            
            const key = event.key.toLowerCase();
            if (shortcuts[key]) {
                event.preventDefault();
                window.location.href = shortcuts[key];
                this.announceToScreenReader(`Navigation vers ${key.toUpperCase()}`);
            }
        }
    }

    handleFocusTrap(event) {
        if (!this.isOpen || !this.isMobile) return;
        
        const focusableElements = this.sidebar.querySelectorAll(
            'a, button, [tabindex]:not([tabindex="-1"])'
        );
        
        const firstElement = focusableElements[0];
        const lastElement = focusableElements[focusableElements.length - 1];
        
        if (event.key === 'Tab') {
            if (event.shiftKey) {
                if (document.activeElement === firstElement) {
                    event.preventDefault();
                    lastElement?.focus();
                }
            } else {
                if (document.activeElement === lastElement) {
                    event.preventDefault();
                    firstElement?.focus();
                }
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
            this.sidebar.setAttribute('aria-label', 'Navigation principale');
            this.sidebar.setAttribute('aria-hidden', this.isMobile ? 'true' : 'false');
        }
        
        // Add skip link for keyboard users
        this.createSkipLink();
    }

    createSkipLink() {
        const skipLink = document.createElement('a');
        skipLink.href = '#main-content';
        skipLink.className = 'sr-only';
        skipLink.textContent = 'Passer au contenu principal';
        skipLink.style.cssText = `
            position: absolute;
            left: -9999px;
            width: 1px;
            height: 1px;
            overflow: hidden;
        `;
        
        skipLink.addEventListener('focus', function() {
            this.style.left = '10px';
            this.style.top = '10px';
            this.style.width = 'auto';
            this.style.height = 'auto';
            this.style.overflow = 'visible';
            this.style.zIndex = '9999';
            this.style.background = 'white';
            this.style.padding = '8px';
            this.style.border = '2px solid #059669';
        });
        
        skipLink.addEventListener('blur', function() {
            this.style.left = '-9999px';
            this.style.width = '1px';
            this.style.height = '1px';
            this.style.overflow = 'hidden';
        });
        
        document.body.insertBefore(skipLink, document.body.firstChild);
    }

    announceToScreenReader(message) {
        const announcement = document.createElement('div');
        announcement.setAttribute('aria-live', 'polite');
        announcement.setAttribute('aria-atomic', 'true');
        announcement.className = 'sr-only';
        announcement.textContent = message;
        
        document.body.appendChild(announcement);
        
        setTimeout(() => {
            announcement.remove();
        }, 1000);
    }

    // Utility functions for performance
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

    // Public API for external use
    destroy() {
        // Remove event listeners
        window.removeEventListener('resize', this.handleResize);
        document.removeEventListener('keydown', this.handleKeyboard);
        
        // Remove created elements
        this.mobileOverlay?.remove();
        
        // Clear timers
        if (this.resizeTimer) {
            clearTimeout(this.resizeTimer);
        }
    }
}

// Enhanced preloader to prevent layout shifts
class UIPreloader {
    constructor() {
        this.init();
    }

    init() {
        // Add minimal critical CSS to prevent FOUC
        this.injectCriticalCSS();
        
        // Initialize navigation when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', this.initializeNavigation);
        } else {
            this.initializeNavigation();
        }
    }

    injectCriticalCSS() {
        const criticalCSS = `
            .sidebar { transform: translateX(-100%); transition: transform 0.3s ease; }
            @media (min-width: 769px) { .sidebar { transform: translateX(0); } }
            .mobile-overlay { opacity: 0; visibility: hidden; pointer-events: none; }
            .no-scroll { overflow: hidden; }
        `;
        
        const style = document.createElement('style');
        style.textContent = criticalCSS;
        document.head.appendChild(style);
    }

    initializeNavigation() {
        // Initialize navigation manager
        window.navigationManager = new NavigationManager();
        
        // Add loading class removal after initialization
        document.body.classList.add('navigation-ready');
    }
}

// Auto-initialize when script loads
if (typeof window !== 'undefined') {
    new UIPreloader();
}

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { NavigationManager, UIPreloader };
}