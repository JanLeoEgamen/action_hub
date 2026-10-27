import './bootstrap';
import './crud-modals';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

const COLLAPSED_CLASS = 'is-collapsed';

/**
 * App shell.
 * - collapsed: desktop icon-only rail (persisted; mirrored onto html.is-collapsed)
 * - open:      mobile off-canvas drawer (never persisted)
 *
 * No viewport state is kept in JS: the rail/label behaviour is pure CSS, so a
 * resize can never leave the sidebar out of sync with the layout.
 */
Alpine.data('appShell', () => ({
    collapsed: document.documentElement.classList.contains(COLLAPSED_CLASS),
    open: false,

    toggleRail() {
        this.collapsed = !this.collapsed;
        document.documentElement.classList.toggle(COLLAPSED_CLASS, this.collapsed);
        localStorage.setItem('sidebar-collapsed', this.collapsed ? 'true' : 'false');
    },
}));

Alpine.start();
