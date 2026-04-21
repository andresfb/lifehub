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
        if (sun)  sun.style.display  = dark ? 'block' : 'none';
        if (moon) moon.style.display = dark ? 'none'  : 'block';
    },

    // ── Sidebar ─────────────────────────────────────────────
    openSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        if (!sidebar || !overlay) return;
        sidebar.style.translate = '0 0';
        sidebar.style.boxShadow = 'var(--lh-shadow-lg)';
        overlay.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    },

    closeSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        if (!sidebar || !overlay) return;
        sidebar.style.translate = '-100% 0';
        sidebar.style.boxShadow = 'none';
        overlay.classList.add('hidden');
        document.body.style.overflow = '';
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
