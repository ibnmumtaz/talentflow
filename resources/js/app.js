import './bootstrap';
import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';

window.Alpine = Alpine;
window.Chart = Chart;
Alpine.start();

if (window.Chart) {
    Chart.defaults.color = '#94a3b8';
    Chart.defaults.borderColor = 'rgba(255,255,255,0.08)';
    Chart.defaults.font.family = 'Inter, system-ui, sans-serif';
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('canvas[data-chart]').forEach((canvas) => {
        try {
            const config = JSON.parse(canvas.dataset.chart);
            new Chart(canvas, config);
        } catch (e) {
            console.error('Chart failed', e);
        }
    });
});

document.addEventListener('submit', (e) => {
    const form = e.target;
    const msg = form?.dataset?.confirm;
    if (msg && !confirm(msg)) e.preventDefault();
});
