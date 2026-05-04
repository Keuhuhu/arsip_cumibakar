/**
 * Arsip Digital Desa Cumibakar — Main JS
 * Enhanced with micro-animations & interactions
 */

// ===== SIDEBAR TOGGLE (MOBILE) =====
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    sidebar.classList.toggle('open');
}

// Close sidebar when clicking outside (mobile)
document.addEventListener('click', function(e) {
    const sidebar = document.getElementById('sidebar');
    const toggle  = document.querySelector('.sidebar-toggle');
    if (sidebar && toggle && sidebar.classList.contains('open')) {
        if (!sidebar.contains(e.target) && !toggle.contains(e.target)) {
            sidebar.classList.remove('open');
        }
    }
});

// ===== ON DOM READY =====
document.addEventListener('DOMContentLoaded', function() {

    // --- Auto-dismiss alerts after 5s ---
    document.querySelectorAll('.alert-dismissible').forEach(function(alert) {
        setTimeout(function() {
            alert.style.transition = 'opacity .4s ease, transform .4s ease';
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-8px)';
            setTimeout(function() { alert.remove(); }, 400);
        }, 5000);
    });

    // --- Stat value count-up animation ---
    document.querySelectorAll('.stat-value').forEach(function(el) {
        var target = parseInt(el.textContent.replace(/\D/g, ''), 10);
        if (isNaN(target) || target === 0) return;

        el.textContent = '0';
        var duration = 800; // ms
        var start = null;

        function step(timestamp) {
            if (!start) start = timestamp;
            var progress = Math.min((timestamp - start) / duration, 1);
            // Ease-out curve
            var eased = 1 - Math.pow(1 - progress, 3);
            el.textContent = Math.floor(eased * target);
            if (progress < 1) {
                requestAnimationFrame(step);
            } else {
                el.textContent = target;
            }
        }
        requestAnimationFrame(step);
    });

    // --- Staggered entrance for stat cards ---
    document.querySelectorAll('.stat-card').forEach(function(card, i) {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity .5s ease, transform .5s ease';
        setTimeout(function() {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, 80 * i);
    });

    // --- Staggered entrance for cards ---
    document.querySelectorAll('.card').forEach(function(card, i) {
        card.style.opacity = '0';
        card.style.transform = 'translateY(16px)';
        card.style.transition = 'opacity .45s ease, transform .45s ease';
        setTimeout(function() {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, 150 + (60 * i));
    });

    // --- Log items stagger ---
    document.querySelectorAll('.log-item').forEach(function(item, i) {
        item.style.opacity = '0';
        item.style.transform = 'translateX(-10px)';
        item.style.transition = 'opacity .35s ease, transform .35s ease';
        setTimeout(function() {
            item.style.opacity = '1';
            item.style.transform = 'translateX(0)';
        }, 200 + (50 * i));
    });

    // --- Table rows stagger ---
    document.querySelectorAll('.table tbody tr').forEach(function(row, i) {
        row.style.opacity = '0';
        row.style.transition = 'opacity .3s ease';
        setTimeout(function() {
            row.style.opacity = '1';
        }, 100 + (30 * i));
    });

    // --- Tooltip on stat cards (show sub text) ---
    document.querySelectorAll('.stat-card').forEach(function(card) {
        card.setAttribute('role', 'status');
    });

    // --- Active nav item subtle indicator glow ---
    document.querySelectorAll('.nav-item.active').forEach(function(item) {
        item.style.boxShadow = 'inset 3px 0 12px rgba(126,212,160,.15)';
    });

    // --- Upload area drag & drop visual ---
    document.querySelectorAll('.upload-area').forEach(function(area) {
        var input = area.querySelector('input[type="file"]') || area.parentElement.querySelector('input[type="file"]');

        area.addEventListener('dragover', function(e) {
            e.preventDefault();
            area.classList.add('drag-over');
        });
        area.addEventListener('dragleave', function() {
            area.classList.remove('drag-over');
        });
        area.addEventListener('drop', function(e) {
            e.preventDefault();
            area.classList.remove('drag-over');
            if (input && e.dataTransfer.files.length) {
                input.files = e.dataTransfer.files;
                input.dispatchEvent(new Event('change'));
            }
        });
    });

    // --- Time-ago live updater (updates every 60s) ---
    setInterval(function() {
        document.querySelectorAll('[data-time-ago]').forEach(function(el) {
            // Placeholder for future use
        });
    }, 60000);

    // --- Keyboard shortcut: Ctrl+K to focus search ---
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            var searchInput = document.querySelector('.filter-control[type="text"], .filter-control[name="q"], input[name="q"]');
            if (searchInput) {
                searchInput.focus();
                searchInput.select();
            }
        }
    });
});
