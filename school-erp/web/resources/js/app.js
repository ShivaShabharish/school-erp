import './bootstrap';
import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';

window.Alpine = Alpine;
window.Chart = Chart;

// Global Toast Event Dispatcher Helper
window.showToast = function (message, type = 'success') {
    window.dispatchEvent(new CustomEvent('toast', {
        detail: { message, type }
    }));
};

Alpine.start();
