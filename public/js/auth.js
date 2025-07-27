function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const button = input.parentElement.querySelector('.password-toggle');

    // SVG icons
    const eyeIcon = `<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#000000" viewBox="0 0 256 256"><path d="M247.31,124.76c-.35-.79-8.82-19.58-27.65-38.41C194.57,61.26,162.88,48,128,48S61.43,61.26,36.34,86.35C17.51,105.18,9,124,8.69,124.76a8,8,0,0,0,0,6.5c.35.79,8.82,19.57,27.65,38.4C61.43,194.74,93.12,208,128,208s66.57-13.26,91.66-38.34c18.83-18.83,27.3-37.61,27.65-38.4A8,8,0,0,0,247.31,124.76ZM128,192c-30.78,0-57.67-11.19-79.93-33.25A133.47,133.47,0,0,1,25,128,133.33,133.33,0,0,1,48.07,97.25C70.33,75.19,97.22,64,128,64s57.67,11.19,79.93,33.25A133.46,133.46,0,0,1,231.05,128C223.84,141.46,192.43,192,128,192Zm0-112a48,48,0,1,0,48,48A48.05,48.05,0,0,0,128,80Zm0,80a32,32,0,1,1,32-32A32,32,0,0,1,128,160Z"></path></svg>`;
    
    const eyeOffIcon = `<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#000000" viewBox="0 0 256 256"><path d="M53.92,34.62A8,8,0,1,0,42.08,45.38L61.32,66.55C25,88.84,9.38,123.2,8.69,124.76a8,8,0,0,0,0,6.5c.35.79,8.82,19.57,27.65,38.4C61.43,194.74,93.12,208,128,208a127.11,127.11,0,0,0,52.07-10.83l22,24.21a8,8,0,1,0,11.84-10.76Zm47.33,75.84,41.67,45.85a32,32,0,0,1-41.67-45.85ZM128,192c-30.78,0-57.67-11.19-79.93-33.25A133.16,133.16,0,0,1,25,128c4.69-8.79,19.66-33.39,47.35-49.38l18,19.75a48,48,0,0,0,63.66,70l14.73,16.2A112,112,0,0,1,128,192Zm6-95.43a8,8,0,0,1,3-15.72,48.16,48.16,0,0,1,38.77,42.64,8,8,0,0,1-7.22,8.71,6.39,6.39,0,0,1-.75,0,8,8,0,0,1-8-7.26A32.09,32.09,0,0,0,134,96.57Zm113.28,34.69c-.42.94-10.55,23.37-33.36,43.8a8,8,0,1,1-10.67-11.92A132.77,132.77,0,0,0,231.05,128a133.15,133.15,0,0,0-23.12-30.77C185.67,75.19,158.78,64,128,64a118.37,118.37,0,0,0-19.36,1.57A8,8,0,1,1,106,49.79,134,134,0,0,1,128,48c34.88,0,66.57,13.26,91.66,38.35,18.83,18.83,27.3,37.62,27.65,38.41A8,8,0,0,1,247.31,131.26Z"></path></svg>`;

    if (input.type === 'password') {
        input.type = 'text';
        button.innerHTML = eyeOffIcon;
    } else {
        input.type = 'password';
        button.innerHTML = eyeIcon;
    }
}


// Email change modal functions
function showEmailChangeForm() {
    const modal = document.getElementById('emailChangeModal');
    if (modal) {
        modal.style.display = 'flex';
        // Focus on the email input
        setTimeout(() => {
            const emailInput = document.getElementById('new_email');
            if (emailInput) {
                emailInput.focus();
            }
        }, 100);
    }
}

function hideEmailChangeForm() {
    const modal = document.getElementById('emailChangeModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    const modal = document.getElementById('emailChangeModal');
    if (modal && event.target === modal) {
        hideEmailChangeForm();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        hideEmailChangeForm();
    }
});

// Form submission loading states
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitButton = form.querySelector('button[type="submit"]');
            if (submitButton) {
                // Add loading state
                submitButton.disabled = true;
                submitButton.classList.add('loading');
                
                // Store original text
                const originalText = submitButton.textContent;
                
                // Update button text based on form action
                const action = form.action;
                if (action.includes('password.email')) {
                    submitButton.textContent = 'Sending...';
                } else if (action.includes('password.store')) {
                    submitButton.textContent = 'Resetting...';
                } else if (action.includes('password.confirm')) {
                    submitButton.textContent = 'Confirming...';
                } else if (action.includes('verification.send')) {
                    submitButton.textContent = 'Sending...';
                } else {
                    submitButton.textContent = 'Processing...';
                }
                
                // Reset if form submission fails (after 10 seconds)
                setTimeout(() => {
                    submitButton.disabled = false;
                    submitButton.classList.remove('loading');
                    submitButton.textContent = originalText;
                }, 10000);
            }
        });
    });
});

// Auto-hide status messages after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const statusMessages = document.querySelectorAll('.status-message');
    statusMessages.forEach(message => {
        setTimeout(() => {
            message.style.opacity = '0';
            message.style.transform = 'translateY(-10px)';
            setTimeout(() => {
                message.style.display = 'none';
            }, 300);
        }, 5000);
    });
});

// Enhanced form validation
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return true;
    
    let isValid = true;
    const inputs = form.querySelectorAll('input[required]');
    
    inputs.forEach(input => {
        const errorSpan = input.parentElement.querySelector('.error-message');
        
        if (!input.value.trim()) {
            if (errorSpan) {
                errorSpan.textContent = 'This field is required';
                errorSpan.style.display = 'block';
            }
            input.style.borderColor = '#e53e3e';
            isValid = false;
        } else {
            if (errorSpan) {
                errorSpan.style.display = 'none';
            }
            input.style.borderColor = '#e2e8f0';
        }
        
        // Email validation
        if (input.type === 'email' && input.value.trim()) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(input.value)) {
                if (errorSpan) {
                    errorSpan.textContent = 'Please enter a valid email address';
                    errorSpan.style.display = 'block';
                }
                input.style.borderColor = '#e53e3e';
                isValid = false;
            }
        }
        
        // Password confirmation validation
        if (input.name === 'password_confirmation') {
            const passwordInput = form.querySelector('input[name="password"]');
            if (passwordInput && input.value !== passwordInput.value) {
                if (errorSpan) {
                    errorSpan.textContent = 'Passwords do not match';
                    errorSpan.style.display = 'block';
                }
                input.style.borderColor = '#e53e3e';
                isValid = false;
            }
        }
    });
    
    return isValid;
}

// Copy email functionality for verification page
function copyEmailToClipboard() {
    const emailText = document.querySelector('.info-text strong');
    if (emailText) {
        navigator.clipboard.writeText(emailText.textContent).then(() => {
            // Show temporary success message
            const originalText = emailText.textContent;
            emailText.textContent = 'Copied!';
            emailText.style.color = '#10b981';
            
            setTimeout(() => {
                emailText.textContent = originalText;
                emailText.style.color = '#374151';
            }, 2000);
        });
    }
}

// Auto-refresh verification status (for verify-email page)
if (window.location.pathname.includes('verify')) {
    let refreshInterval = setInterval(() => {
        fetch(window.location.href, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (response.redirected) {
                window.location.href = response.url;
                clearInterval(refreshInterval);
            }
        })
        .catch(() => {
            // Silently handle errors
        });
    }, 30000); // Check every 30 seconds
    
    // Clear interval when page is about to unload
    window.addEventListener('beforeunload', () => {
        clearInterval(refreshInterval);
    });
}