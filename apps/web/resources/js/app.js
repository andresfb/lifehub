const LifeHub = {
    // ── Theme ───────────────────────────────────────────────
    initTheme() {
        const dark = localStorage.getItem('lh_theme') === 'dark';
        this._applyTheme(dark);
    },

    toggleTheme() {
        const isDark = document.documentElement.classList.contains('dark');
        this._applyTheme(!isDark);
        localStorage.setItem('lh_theme', !isDark ? 'dark' : 'light');
    },

    _applyTheme(dark) {
        document.documentElement.classList.toggle('dark', dark);
        const sun = document.getElementById('icon-sun');
        const moon = document.getElementById('icon-moon');
        if (sun) {
            sun.classList.toggle('hidden', !dark);
        }
        if (moon) {
            moon.classList.toggle('hidden', dark);
        }
    },

    // ── Sidebar ─────────────────────────────────────────────
    openSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        if (!sidebar || !overlay) return;
        sidebar.classList.remove('-translate-x-full');
        sidebar.classList.add('shadow-[var(--lh-shadow-lg)]');
        overlay.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    },

    closeSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        if (!sidebar || !overlay) return;
        sidebar.classList.add('-translate-x-full');
        sidebar.classList.remove('shadow-[var(--lh-shadow-lg)]');
        overlay.classList.add('hidden');
        document.body.style.overflow = '';
    },

    toggleNavigationGroup(button) {
        const controls = button.getAttribute('aria-controls');
        if (!controls) return;

        const submenu = document.getElementById(controls);
        if (!submenu) return;

        const isExpanded = button.getAttribute('aria-expanded') === 'true';
        button.setAttribute('aria-expanded', String(!isExpanded));
        submenu.classList.toggle('hidden', isExpanded);
        button.querySelector('svg')?.classList.toggle('rotate-90', !isExpanded);
    },

    // ── Profile menu ─────────────────────────────────────────
    toggleProfileMenu() {
        const dropdown = document.getElementById('profile-dropdown');
        if (!dropdown) return;
        dropdown.classList.toggle('hidden');
    },

    _closeProfileMenu() {
        document.getElementById('profile-dropdown')?.classList.add('hidden');
    },

    // ── Search ───────────────────────────────────────────────
    _currentEngineUrl: null,

    toggleEngineDropdown() {
        document.getElementById('engine-dropdown')?.classList.toggle('hidden');
    },

    selectEngine(btn) {
        this._currentEngineUrl = btn.dataset.url;
        const nameEl = document.getElementById('engine-name');
        if (nameEl) nameEl.textContent = btn.dataset.name;
        document.getElementById('engine-dropdown')?.classList.add('hidden');
    },

    doSearch() {
        const input = document.getElementById('search-input');
        if (!input) return;
        const query = input.value.trim();
        if (!query) return;
        const url = (this._currentEngineUrl ?? 'https://www.google.com/search?q=') + encodeURIComponent(query);
        window.open(url, '_blank', 'noopener,noreferrer');
    },

    // ── Click-outside handler ────────────────────────────────
    _handleOutsideClick(e) {
        const profileWrap = document.getElementById('profile-menu-wrap');
        if (profileWrap && !profileWrap.contains(e.target)) {
            LifeHub._closeProfileMenu();
        }

        const engineWrap = document.getElementById('engine-wrap');
        if (engineWrap && !engineWrap.contains(e.target)) {
            document.getElementById('engine-dropdown')?.classList.add('hidden');
        }
    },

    // ── Init ─────────────────────────────────────────────────
    init() {
        this.initTheme();
        document.addEventListener('mousedown', (e) => this._handleOutsideClick(e));
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.closeSidebar();
                this._closeProfileMenu();
                document.getElementById('engine-dropdown')?.classList.add('hidden');
            }
        });
    },
};

window.LifeHub = LifeHub;
document.addEventListener('DOMContentLoaded', () => LifeHub.init());
