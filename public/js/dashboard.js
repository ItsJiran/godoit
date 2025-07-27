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
        this.toggleBtn.addEventListener('click', () => this.toggleSidebar());
        this.overlay.addEventListener('click', () => this.closeSidebar());
        window.addEventListener('resize', () => this.handleResize());
        
        // Initialize sidebar state based on screen size
        this.handleResize();
    }
    
    toggleSidebar() {
        if (this.isMobile) {
            this.sidebar.classList.toggle('open');
            this.overlay.classList.toggle('active');
        } else {
            this.isCollapsed = !this.isCollapsed;
            this.sidebar.classList.toggle('collapsed', this.isCollapsed);
            
            // Update toggle button icon
            const icon = this.toggleBtn.querySelector('.toggle-icon');
            icon.textContent = this.isCollapsed ? '☰' : '✕';
        }
    }
    
    closeSidebar() {
        if (this.isMobile) {
            this.sidebar.classList.remove('open');
            this.overlay.classList.remove('active');
        }
    }
    
    handleResize() {
        const wasMobile = this.isMobile;
        this.isMobile = window.innerWidth <= 768;
        
        if (wasMobile !== this.isMobile) {
            // Reset sidebar state when switching between mobile/desktop
            this.sidebar.classList.remove('open', 'collapsed');
            this.overlay.classList.remove('active');
            this.isCollapsed = false;
            
            const icon = this.toggleBtn.querySelector('.toggle-icon');
            icon.textContent = '☰';
        }
    }
}

// Initialize dashboard when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new Dashboard();
    
    // Add smooth scrolling and other enhancements
    document.querySelectorAll('.menu-item').forEach(item => {
        item.addEventListener('click', (e) => {
            // Remove active class from all items
            document.querySelectorAll('.menu-item').forEach(i => i.classList.remove('active'));
            // Add active class to clicked item
            item.classList.add('active');
        });
    });
});

// Add some interactive animations
document.addEventListener('DOMContentLoaded', () => {
    const statCards = document.querySelectorAll('.stat-card');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animationPlayState = 'running';
            }
        });
    });
    
    statCards.forEach(card => {
        observer.observe(card);
    });
});