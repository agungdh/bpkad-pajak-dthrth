import { OverlayScrollbars } from 'overlayscrollbars';
import * as bootstrap from 'bootstrap';
import 'admin-lte/dist/js/adminlte.js';

// jQuery
import jQuery from 'jquery';
window.$ = window.jQuery = jQuery;

// Axios
import axios from 'axios';
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Alpine.js
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

// Select2 with Bootstrap 5 theme
import select2 from 'select2';
select2();
$.fn.select2.defaults.set('theme', 'bootstrap-5');

// DataTables
import 'datatables.net-bs5';
import 'datatables.net-select-bs5';

// SweetAlert2
import Swal from 'sweetalert2';
window.Swal = Swal;

// Toastr
import toastr from 'toastr';
window.toastr = toastr;

// Configure toastr defaults
toastr.options = {
    closeButton: true,
    debug: false,
    newestOnTop: true,
    progressBar: true,
    positionClass: 'toast-top-right',
    preventDuplicates: false,
    onclick: null,
    showDuration: '300',
    hideDuration: '1000',
    timeOut: '5000',
    extendedTimeOut: '1000',
    showEasing: 'swing',
    hideEasing: 'linear',
    showMethod: 'fadeIn',
    hideMethod: 'fadeOut',
};

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
