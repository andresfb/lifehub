import Alpine from 'alpinejs'
window.Alpine = Alpine

Alpine.store('theme', {
    isDark: false,

    init() {
        this.isDark = localStorage.getItem('lh_theme') === 'dark'
        this.apply()
    },

    toggle() {
        this.isDark = !this.isDark
        localStorage.setItem('lh_theme', this.isDark ? 'dark' : 'light')
        this.apply()
    },

    apply() {
        document.documentElement.classList.toggle('dark', this.isDark)
    },
})

Alpine.data('layoutShell', () => ({
    isProfileMenuOpen: false,
    isSidebarOpen: false,

    init() {
        this.$watch('isSidebarOpen', (isOpen) => {
            document.body.style.overflow = isOpen ? 'hidden' : ''
        })
    },

    get isDark() {
        return this.$store.theme.isDark
    },

    toggleTheme() {
        this.$store.theme.toggle()
    },

    closeOpenMenus() {
        this.isSidebarOpen = false
        this.isProfileMenuOpen = false
    },
}))

Alpine.data('navigationGroup', (initialExpanded = false) => ({
    isExpanded: initialExpanded,

    toggle() {
        this.isExpanded = !this.isExpanded
    },
}))

Alpine.data('webSearch', (engines = []) => ({
    engines,
    isEngineDropdownOpen: false,
    query: '',
    selectedEngine: engines[0] ?? {
        name: 'DuckDuckGo',
        url: 'https://noai.duckduckgo.com/?ia=web&origin=lifehub&q=',
    },

    selectEngine(engine) {
        this.selectedEngine = engine
        this.isEngineDropdownOpen = false
    },

    doSearch() {
        const query = this.query.trim()

        if (!query) {
            return
        }

        window.open(this.selectedEngine.url + encodeURIComponent(query), '_blank', 'noopener,noreferrer')
    },
}))

Alpine.data('twoFactorCountdown', (totalSeconds) => ({
    circumference: 213.6,
    interval: null,
    remaining: totalSeconds,
    total: totalSeconds,

    init() {
        this.interval = setInterval(() => {
            if (this.remaining <= 0) {
                clearInterval(this.interval)

                return
            }

            this.remaining--
        }, 1000)
    },

    destroy() {
        clearInterval(this.interval)
    },

    get arcOffset() {
        return this.circumference * (1 - this.remaining / this.total)
    },

    get hasExpired() {
        return this.remaining <= 0
    },

    get label() {
        return this.remaining < 60 ? 'seconds remaining' : 'remaining'
    },

    get text() {
        if (this.remaining < 60) {
            return String(this.remaining)
        }

        const minutes = Math.floor(this.remaining / 60)
        const seconds = this.remaining % 60

        return `${minutes}:${String(seconds).padStart(2, '0')}`
    },
}))

Alpine.start()
