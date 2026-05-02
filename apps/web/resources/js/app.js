import 'htmx.org';
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
        document.documentElement.dataset.theme = this.isDark ? 'forest' : 'emerald'
    },
})

Alpine.data('layoutShell', () => ({
    isProfileMenuOpen: false,
    isSidebarProfileMenuOpen: false,
    isSidebarOpen: false,
    isCommandOpen: false,

    init() {
        this.$watch('isSidebarOpen', (isOpen) => {
            document.body.style.overflow = isOpen ? 'hidden' : ''
        })
        this.$watch('isCommandOpen', (isOpen) => {
            document.body.style.overflow = isOpen ? 'hidden' : ''
            if (!isOpen) return
            this.$nextTick(() => {
                const isDesktop = window.matchMedia('(min-width: 768px)').matches
                const ref = isDesktop ? this.$refs.commandInputDesktop : this.$refs.commandInputMobile
                ref?.focus()
            })
        })
    },

    get isDark() {
        return this.$store.theme.isDark
    },

    toggleTheme() {
        this.$store.theme.toggle()
    },

    toggleSidebarFromShortcut(event) {
        if (!event.metaKey || event.key !== '1' || this.isEditableTarget(event.target)) {
            return
        }

        event.preventDefault()

        this.isSidebarOpen = !this.isSidebarOpen
        this.closeProfileMenus()
    },

    togglePageActionsFromShortcut(event) {
        if (!event.metaKey || event.key !== '2' || this.isEditableTarget(event.target)) {
            return
        }

        const pageActionsRoot = this.findVisiblePageActionsRoot()
        if (!pageActionsRoot) {
            return
        }

        event.preventDefault()

        pageActionsRoot.dispatchEvent(new CustomEvent('page-actions:toggle'))
        this.closeSidebar()
        this.closeProfileMenus()
        this.isCommandOpen = false
    },

    toggleCommand(event) {
        const hasModifier = event.metaKey || event.ctrlKey
        if (!hasModifier || event.key !== '/') {
            return
        }

        if (!this.isCommandOpen && this.isEditableTarget(event.target)) {
            return
        }

        event.preventDefault()

        if (this.isCommandOpen) {
            this.isCommandOpen = false

            return
        }

        this.openCommand()
    },

    openCommand() {
        this.isCommandOpen = true
        this.closeSidebar()
        this.closeProfileMenus()
    },

    toggleHeaderProfileMenu() {
        this.isProfileMenuOpen = !this.isProfileMenuOpen
        this.isSidebarProfileMenuOpen = false
    },

    toggleSidebarProfileMenu() {
        this.isSidebarProfileMenuOpen = !this.isSidebarProfileMenuOpen
        this.isProfileMenuOpen = false
    },

    closeProfileMenus() {
        this.isProfileMenuOpen = false
        this.isSidebarProfileMenuOpen = false
    },

    closeSidebar() {
        this.isSidebarOpen = false
        this.isSidebarProfileMenuOpen = false
    },

    isEditableTarget(target) {
        return target instanceof HTMLElement
            && (target.isContentEditable || target.closest('input, textarea, select, [contenteditable="true"]') !== null)
    },

    findVisiblePageActionsRoot() {
        return Array.from(document.querySelectorAll('[data-page-actions-root]'))
            .find((element) => element instanceof HTMLElement && element.getClientRects().length > 0)
    },

    closeOpenMenus() {
        this.closeSidebar()
        this.closeProfileMenus()
        this.isCommandOpen = false
    },
}))

Alpine.data('pageActionsMenu', () => ({
    isOpen: false,
    activeIndex: -1,

    toggle() {
        if (this.isOpen) {
            this.close()

            return
        }

        this.open()
    },

    toggleFromShortcut() {
        if (this.isOpen) {
            this.close()

            return
        }

        this.open(true)
    },

    open(shouldFocusFirstAction = false) {
        this.isOpen = true
        this.activeIndex = shouldFocusFirstAction ? this.firstActionIndex() : this.activeIndex

        this.$nextTick(() => {
            if (shouldFocusFirstAction) {
                this.focusActiveAction()
            }
        })
    },

    close() {
        this.isOpen = false
        this.activeIndex = -1
    },

    handleArrowKey(event, step) {
        if (!this.isOpen) {
            return
        }

        const items = this.actionItems()
        if (!items.length) {
            return
        }

        event.preventDefault()

        if (this.activeIndex === -1) {
            this.activeIndex = step > 0 ? 0 : items.length - 1
            this.focusActiveAction()

            return
        }

        this.activeIndex = (this.activeIndex + step + items.length) % items.length
        this.focusActiveAction()
    },

    handleEnterKey(event) {
        if (!this.isOpen) {
            return
        }

        const item = this.actionItems()[this.activeIndex] ?? this.actionItems()[0]
        if (!item) {
            return
        }

        event.preventDefault()
        item.click()
    },

    actionItems() {
        return Array.from(this.$root.querySelectorAll('[data-page-action-item="enabled"]'))
    },

    firstActionIndex() {
        return this.actionItems().length ? 0 : -1
    },

    focusActiveAction() {
        const activeAction = this.actionItems()[this.activeIndex]
        activeAction?.focus()
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
        url: 'https://noai.duckduckgo.com/?ia=web&origin=lifehub&q=%s',
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

        const searchUrl = this.selectedEngine.url.replace('%s', encodeURIComponent(query))

        window.open(searchUrl, '_blank', 'noopener,noreferrer')
    },
}))

Alpine.data('pinsModal', (config = {}) => ({
    createRouteName: config.createRouteName ?? '',
    updateRouteName: config.updateRouteName ?? '',
    isOpen: false,
    mode: 'create',
    sections: Object.entries(config.sections ?? {}).map(([slug, name]) => ({ slug, name })),
    form: {
        routeName: '',
        slug: '',
        sectionSlug: '',
        sectionName: '',
        title: '',
        url: '',
        order: '',
        icon: '',
        iconColor: '',
        description: '',
        tagsText: '',
    },

    init() {
        this.$watch('isOpen', (isOpen) => {
            document.body.style.overflow = isOpen ? 'hidden' : ''
            if (!isOpen) {
                return
            }

            this.$nextTick(() => {
                const selector = window.matchMedia('(min-width: 768px)').matches
                    ? '#pin-title-desktop'
                    : '#pin-title-mobile'

                this.$root.querySelector(selector)?.focus()
            })
        })
    },

    get modalTitle() {
        return this.mode === 'edit' ? 'Edit Pin' : 'New Pin'
    },

    get modalDescription() {
        if (this.form.sectionName) {
            return `Section: ${this.form.sectionName}`
        }

        return 'Choose a section and fill in the pin details.'
    },

    openCreatePin() {
        const firstSection = this.sections[0] ?? { slug: '', name: '' }

        this.mode = 'create'
        this.form = {
            routeName: this.createRouteName,
            slug: '',
            sectionSlug: firstSection.slug,
            sectionName: firstSection.name,
            title: '',
            url: '',
            order: '',
            icon: '',
            iconColor: '',
            description: '',
            tagsText: '',
        }
        this.isOpen = true
    },

    openEditPin(pin) {
        this.mode = 'edit'
        this.form = {
            routeName: this.updateRouteName,
            slug: pin.slug ?? '',
            sectionSlug: pin.sectionSlug ?? '',
            sectionName: pin.sectionName ?? '',
            title: pin.title ?? '',
            url: pin.url ?? '',
            order: pin.order ?? '',
            icon: pin.icon ?? '',
            iconColor: pin.iconColor ?? '',
            description: pin.description ?? '',
            tagsText: pin.tagsText ?? '',
        }
        this.isOpen = true
    },

    syncSectionName() {
        const activeSection = this.sections.find((section) => section.slug === this.form.sectionSlug)

        this.form.sectionName = activeSection?.name ?? ''
    },

    close() {
        this.isOpen = false
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
