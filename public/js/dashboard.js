class Dashboard {
    constructor() {
        this.sidebar = document.getElementById('sidebar');
        this.toggleBtn = document.getElementById('toggleBtn');
        this.overlay = document.getElementById('overlay');
        this.isCollapsed = false;
        this.isMobile = window.innerWidth <= 768;

        this.init();
    }

    init() {
        if (this.toggleBtn) {
            this.toggleBtn.addEventListener('click', () => this.toggleSidebar());
        }

        if (this.overlay) {
            this.overlay.addEventListener('click', () => this.closeSidebar());
        }

        window.addEventListener('resize', () => this.handleResize());
        this.handleResize();
    }

    toggleSidebar() {
        if (!this.sidebar || !this.toggleBtn) return;

        if (this.isMobile) {
            this.sidebar.classList.toggle('open');
            this.overlay?.classList.toggle('active');
        } else {
            this.isCollapsed = !this.isCollapsed;
            this.sidebar.classList.toggle('collapsed', this.isCollapsed);
            const icon = this.toggleBtn.querySelector('.toggle-icon');
            if (icon) icon.textContent = this.isCollapsed ? '☰' : '✕';
        }
    }

    closeSidebar() {
        if (!this.sidebar || !this.overlay) return;

        if (this.isMobile) {
            this.sidebar.classList.remove('open');
            this.overlay.classList.remove('active');
        }
    }

    handleResize() {
        const wasMobile = this.isMobile;
        this.isMobile = window.innerWidth <= 768;

        if (!this.sidebar || !this.toggleBtn || wasMobile === this.isMobile) return;

        this.sidebar.classList.remove('open', 'collapsed');
        this.overlay?.classList.remove('active');
        this.isCollapsed = false;

        const icon = this.toggleBtn.querySelector('.toggle-icon');
        if (icon) icon.textContent = '☰';
    }
}

document.addEventListener('DOMContentLoaded', () => {
    // Inisialisasi dashboard hanya jika elemen sidebar ditemukan
    if (document.getElementById('sidebar')) {
        new Dashboard();
    }

    // Menu aktif
    const menuItems = document.querySelectorAll('.menu-item');
    if (menuItems.length > 0) {
        menuItems.forEach(item => {
            item.addEventListener('click', () => {
                document.querySelectorAll('.menu-item').forEach(i => i.classList.remove('active'));
                item.classList.add('active');
            });
        });
    }

    // Stat-card animasi jika terlihat
    const statCards = document.querySelectorAll('.stat-card');
    if (statCards.length > 0) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.animationPlayState = 'running';
                }
            });
        });

        statCards.forEach(card => observer.observe(card));
    }

    // Copy button handler
    const copyBtn = document.querySelector('.copy-button');
    if (copyBtn) {
        if (navigator.clipboard && navigator.clipboard.writeText) {
            copyBtn.addEventListener('click', copyLinkModern);
        } else {
            copyBtn.addEventListener('click', copyLink);
        }
    }
});

// Fungsi salin link (fallback)
function copyLink() {
    const linkInput = document.querySelector('.link-input');
    const notification = document.getElementById('notification');
    if (!linkInput || !notification) return;

    linkInput.select();
    linkInput.setSelectionRange(0, 99999);
    try {
        document.execCommand('copy');
        notification.classList.add('show');
        setTimeout(() => notification.classList.remove('show'), 3000);
    } catch (err) {
        console.error('Gagal menyalin link: ', err);
    }
    window.getSelection().removeAllRanges();
}

// Versi modern async
async function copyLinkModern() {
    const linkInput = document.querySelector('.link-input');
    const notification = document.getElementById('notification');
    if (!linkInput || !notification) return;

    try {
        await navigator.clipboard.writeText(linkInput.value);
        notification.classList.add('show');
        setTimeout(() => notification.classList.remove('show'), 3000);
    } catch (err) {
        copyLink(); // fallback ke versi lama
    }
}

// jQuery: Notifikasi Modal
function showNotificationModal() {
    if (window.jQuery) {
        $('#notificationModal').addClass('nm-active');
        $('body').css('overflow', 'hidden');
    }
}

function hideNotificationModal() {
    if (window.jQuery) {
        $('#notificationModal').removeClass('nm-active');
        $('body').css('overflow', 'auto');
    }
}

// jQuery: ESC untuk tutup notifikasi
$(document).keydown(function (e) {
    if (e.key === 'Escape') {
        hideNotificationModal();
    }
});

// jQuery: klik di dalam modal tidak menutup
$('.notification-modal-container').on('click', function (e) {
    e.stopPropagation();
});