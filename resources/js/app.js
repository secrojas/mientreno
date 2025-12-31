import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Mobile sidebar component para toggle en mobile
Alpine.data('mobileSidebar', () => ({
    open: false,
    toggle() {
        this.open = !this.open;
    },
    close() {
        this.open = false;
    }
}));

Alpine.start();
