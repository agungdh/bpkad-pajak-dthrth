import { OverlayScrollbars } from 'overlayscrollbars';
import * as bootstrap from 'bootstrap';
import 'admin-lte/dist/js/adminlte.js';

// Make OverlayScrollbars globally available
window.OverlayScrollbarsGlobal = { OverlayScrollbars };

// Initialize OverlayScrollbars
const SELECTOR_SIDEBAR_WRAPPER = '.sidebar-wrapper';
const Default = {
    scrollbarTheme: 'os-theme-light',
    scrollbarAutoHide: 'leave',
    scrollbarClickScroll: true,
};

document.addEventListener('DOMContentLoaded', function () {
    const sidebarWrapper = document.querySelector(SELECTOR_SIDEBAR_WRAPPER);

    // Disable OverlayScrollbars on mobile devices to prevent touch interference
    const isMobile = window.innerWidth <= 992;

    if (sidebarWrapper && OverlayScrollbars !== undefined && !isMobile) {
        OverlayScrollbars(sidebarWrapper, {
            scrollbars: {
                theme: Default.scrollbarTheme,
                autoHide: Default.scrollbarAutoHide,
                clickScroll: Default.scrollbarClickScroll,
            },
        });
    }
});
