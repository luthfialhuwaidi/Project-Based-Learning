/**
 * Sistem MBG - JavaScript Entry Point
 *
 * Note: Pusher & Leaflet dimuat via CDN di blade layout.
 * File ini untuk logika JavaScript global tambahan.
 */

// Auto-hide flash messages setelah 5 detik
document.addEventListener('DOMContentLoaded', function () {
    const alerts = document.querySelectorAll('[data-auto-dismiss]');
    alerts.forEach(function (alert) {
        setTimeout(function () {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });
});

// Konfirmasi sebelum submit form berbahaya
document.querySelectorAll('[data-confirm]').forEach(function (el) {
    el.addEventListener('submit', function (e) {
        const msg = el.dataset.confirm || 'Apakah Anda yakin?';
        if (!confirm(msg)) {
            e.preventDefault();
        }
    });
});
