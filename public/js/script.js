// Mobile menu toggle
function toggleMobileMenu() {
    const navLinks = document.querySelector('.nav-links');
    navLinks.classList.toggle('active');
}

// Profile dropdown toggle
function toggleDropdown() {
    const dropdown = document.querySelector('.profile-dropdown');
    dropdown.classList.toggle('active');
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const dropdown = document.querySelector('.profile-dropdown');
    const profileBtn = document.querySelector('.profile-btn');
    if (!dropdown.contains(event.target) && !profileBtn.contains(event.target)) {
        dropdown.classList.remove('active');
    }
});

// Simulate login/logout functionality
let isLoggedIn = false;

function toggleAuth() {
    const guestButtons = document.querySelector('.guest-buttons');
    const profileDropdown = document.querySelector('.profile-dropdown');
    if (isLoggedIn) {
        guestButtons.style.display = 'flex';
        profileDropdown.classList.remove('show');
    } else {
        guestButtons.style.display = 'none';
        profileDropdown.classList.add('show');
    }
}

function logout() {
    isLoggedIn = false;
    toggleAuth();
    alert('Logged out successfully!');
}

// Simulate login (you can call this function when user logs in)
function simulateLogin() {
    isLoggedIn = true;
    toggleAuth();
}

function copyLink() {
    const linkInput = document.querySelector('.link-input');
    const notification = document.getElementById('notification');
    linkInput.select();
    linkInput.setSelectionRange(0, 99999);
    try {
        document.execCommand('copy');
        notification.classList.add('show');
        setTimeout(() => {
            notification.classList.remove('show');
        }, 3000);
    } catch (err) {
        console.error('Gagal menyalin link: ', err);
    }
    window.getSelection().removeAllRanges();
}

// Alternative modern copy method
async function copyLinkModern() {
    const linkInput = document.querySelector('.link-input');
    const notification = document.getElementById('notification');
    try {
        await navigator.clipboard.writeText(linkInput.value);
        notification.classList.add('show');
        setTimeout(() => {
            notification.classList.remove('show');
        }, 3000);
    } catch (err) {
        copyLink();
    }
}

// Use modern method if available
if (navigator.clipboard && navigator.clipboard.writeText) {
    document.querySelector('.copy-button').setAttribute('onclick', 'copyLinkModern()');
}