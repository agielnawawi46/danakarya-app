// Dana Karya — Frontend JS

// ─── Alpine.js loaded via CDN in layout ─────────────────────────────────────

// ─── Number Counter Animation ────────────────────────────────────────────────
function animateCounter(el) {
    const target = parseFloat(el.dataset.target || el.textContent.replace(/[^0-9.]/g, ''));
    const isRupiah = el.dataset.rupiah !== undefined;
    const duration = 1200;
    const steps = 60;
    const increment = target / steps;
    let current = 0;
    let step = 0;

    const timer = setInterval(() => {
        step++;
        current = Math.min(current + increment, target);

        if (isRupiah) {
            el.textContent = 'Rp ' + Math.round(current).toLocaleString('id-ID');
        } else {
            el.textContent = Math.round(current).toLocaleString('id-ID');
        }

        if (step >= steps) {
            clearInterval(timer);
            if (isRupiah) {
                el.textContent = 'Rp ' + target.toLocaleString('id-ID');
            } else {
                el.textContent = target.toLocaleString('id-ID');
            }
        }
    }, duration / steps);
}

// Observe stat cards and animate counters when visible
document.addEventListener('DOMContentLoaded', () => {
    const counters = document.querySelectorAll('[data-counter]');
    if (counters.length === 0) return;

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateCounter(entry.target);
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.3 });

    counters.forEach(el => observer.observe(el));
});

// ─── Loan Calculator (AJAX) ──────────────────────────────────────────────────
window.LoanCalc = {
    amount: '',
    tenor: '',
    result: null,
    loading: false,

    async calculate() {
        if (!this.amount || !this.tenor) return;
        this.loading = true;
        this.result = null;

        try {
            const url = new URL(window.location.href);
            const calcUrl = url.origin + '/member/loans/calculate';

            const res = await fetch(`${calcUrl}?amount=${this.amount}&tenor=${this.tenor}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            });

            this.result = await res.json();
        } catch (e) {
            console.error('Calculator error:', e);
        } finally {
            this.loading = false;
        }
    },

    formatRupiah(val) {
        if (!val) return 'Rp 0';
        return 'Rp ' + Math.round(val).toLocaleString('id-ID');
    }
};

// ─── Flash Dismiss ───────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.alert-dismiss').forEach(btn => {
        btn.addEventListener('click', () => {
            btn.closest('.alert').remove();
        });
    });

    // Auto-dismiss after 5 seconds
    document.querySelectorAll('.alert.auto-dismiss').forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-8px)';
            alert.style.transition = 'all .3s ease';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    });
});

// ─── Table row click-to-highlight ────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('table.clickable tbody tr').forEach(row => {
        row.style.cursor = 'pointer';
    });
});

// ─── Confirm dialogs ─────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-confirm]').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const msg = btn.dataset.confirm || 'Apakah Anda yakin?';
            if (!confirm(msg)) e.preventDefault();
        });
    });
});

// ─── Mobile Sidebar Toggle ───────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.getElementById('sidebar-toggle');
    const sidebar = document.querySelector('.sidebar');
    if (toggle && sidebar) {
        toggle.addEventListener('click', () => {
            sidebar.style.transform = sidebar.style.transform === 'translateX(0px)'
                ? 'translateX(-100%)'
                : 'translateX(0px)';
        });
    }
});

// ─── Number input formatting ─────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('input[data-format="currency"]').forEach(input => {
        input.addEventListener('input', function () {
            const raw = this.value.replace(/\D/g, '');
            this.dataset.raw = raw;
        });
    });
});

// Dana Karya helper exports
window.DanaKarya = {
    formatRupiah: (val) => {
        return 'Rp ' + Number(val).toLocaleString('id-ID', { minimumFractionDigits: 0 });
    },
    formatNumber: (val) => {
        return Number(val).toLocaleString('id-ID');
    }
};
