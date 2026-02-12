/**
 * Login Form Handler
 * Xử lý đăng nhập và lưu token vào localStorage
 */

document.addEventListener('DOMContentLoaded', function () {
    const loginForm = document.getElementById('formAuthentication');

    if (loginForm) {
        loginForm.addEventListener('submit', handleLogin);
    }

    // Toggle password visibility
    const passwordToggle = document.querySelector('.input-group-text.cursor-pointer');
    if (passwordToggle) {
        passwordToggle.addEventListener('click', togglePasswordVisibility);
    }
});

/**
 * Xử lý submit form đăng nhập
 */
async function handleLogin(e) {

    e.preventDefault();

    const form = e.target;
    const submitButton = form.querySelector('button[type="submit"]');
    const formData = new FormData(form);

    // Disable submit button
    submitButton.disabled = true;
    submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Signing in...';

    // Clear previous errors
    clearErrors();

    try {
        const response = await fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': formData.get('_token'),
                'Accept': 'application/json',
            },
            body: formData
        });

        const data = await response.json();

        if (response.ok && data.success === true) {
            // Lưu token vào localStorage
            if (data.token) {
                localStorage.setItem('auth_token', data.token);
            }

            // Lưu user info nếu có
            if (data.user) {
                localStorage.setItem('user_info', JSON.stringify(data.user));
            }

            // Lưu remember me preference
            const rememberMe = document.getElementById('remember_me');
            if (rememberMe && rememberMe.checked) {
                localStorage.setItem('remember_me', 'true');
            }

            // Show success message
            showSuccessMessage(data.message || 'Login successful!');

            // Redirect sau 1 giây
            setTimeout(() => {
                window.location.href = data.redirect || '/admin/dashboard';
            }, 1000);

        } else {
            // Xử lý lỗi từ server
            handleErrors(data);
        }

    } catch (error) {
        console.error('Login error:', error);
        showErrorMessage('An error occurred. Please try again.');
    } finally {
        // Re-enable submit button
        submitButton.disabled = false;
        submitButton.innerHTML = 'Sign in';
    }
}

/**
 * Toggle password visibility
 */
function togglePasswordVisibility() {
    const passwordInput = document.getElementById('password');
    const icon = this.querySelector('i');

    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.remove('bx-hide');
        icon.classList.add('bx-show');
    } else {
        passwordInput.type = 'password';
        icon.classList.remove('bx-show');
        icon.classList.add('bx-hide');
    }
}

/**
 * Hiển thị lỗi từ validation
 */
function handleErrors(data) {
    if (data.errors) {
        // Validation errors
        Object.keys(data.errors).forEach(key => {
            const input = document.querySelector(`[name="${key}"]`);
            if (input) {
                const errorSpan = document.createElement('span');
                errorSpan.className = 'text-danger ml-1';
                errorSpan.textContent = data.errors[key][0];

                // Remove existing error if any
                const existingError = input.parentElement.querySelector('.text-danger');
                if (existingError) {
                    existingError.remove();
                }

                input.parentElement.appendChild(errorSpan);
                input.classList.add('is-invalid');
            }
        });
    } else if (data.message) {
        // General error message
        showErrorMessage(data.message);
    }
}

/**
 * Xóa tất cả lỗi hiện tại
 */
function clearErrors() {
    document.querySelectorAll('.text-danger').forEach(el => el.remove());
    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

    // Clear any alert messages
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => alert.remove());
}

/**
 * Hiển thị thông báo lỗi
 */
function showErrorMessage(message) {
    const alert = createAlert(message, 'danger');
    insertAlert(alert);
}

/**
 * Hiển thị thông báo thành công
 */
function showSuccessMessage(message) {
    const alert = createAlert(message, 'success');
    insertAlert(alert);
}

/**
 * Tạo alert element
 */
function createAlert(message, type) {
    const alert = document.createElement('div');
    alert.className = `alert alert-${type} alert-dismissible fade show`;
    alert.role = 'alert';
    alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    return alert;
}

/**
 * Insert alert vào form
 */
function insertAlert(alert) {
    const form = document.getElementById('formAuthentication');
    form.insertBefore(alert, form.firstChild);

    // Auto dismiss after 5 seconds
    setTimeout(() => {
        alert.remove();
    }, 5000);
}

/**
 * Check nếu user đã đăng nhập (có token)
 */
function isAuthenticated() {
    return localStorage.getItem('auth_token') !== null;
}

/**
 * Lấy auth token
 */
function getAuthToken() {
    return localStorage.getItem('auth_token');
}

/**
 * Logout - xóa token và user info
 */
function logout() {
    localStorage.removeItem('auth_token');
    localStorage.removeItem('user_info');
    localStorage.removeItem('remember_me');
    window.location.href = '/admin/login';
}

// Export functions nếu cần sử dụng ở nơi khác
window.authUtils = {
    isAuthenticated,
    getAuthToken,
    logout
};
