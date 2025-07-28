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

// Copy Konten Marketing Kit
$(document).ready(function(){
    $('.copy-konten').on('click', function(){
        var konten = $(this).closest('.program-card').find('.this-konten').val();
        navigator.clipboard.writeText(konten).then(() => {
            $(this).text('Tersalin!');
            var button = $(this);
            setTimeout(function(){
                button.html(`<svg class="copy-icon" viewBox="0 0 24 24">
                    <path d="M16 1H4C2.9 1 2 1.9 2 3V17H4V3H16V1ZM19 5H8C6.9 5 6 5.9 6 7V21C6 22.1 6.9 23 8 23H19C20.1 23 21 22.1 21 21V7C21 5.9 20.1 5 19 5ZM19 21H8V7H19V21Z"></path>
                </svg> Copy`);
            }, 2000);
        }).catch(err => {
            alert('Gagal menyalin teks: ' + err);
        });
    });
});