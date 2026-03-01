const Toast = {
    init() {
        this.el = document.getElementById('adminToast');
        if (!this.el) return;

        this.bsToast = new bootstrap.Toast(this.el, {
            delay: 3000
        });

        this.title = this.el.querySelector('.toast-title');
        this.body = this.el.querySelector('.toast-body');
        this.icon = this.el.querySelector('.toast-icon');
        this.header = this.el.querySelector('.toast-header');
    },

    show(message, type = 'success', title = 'Notification') {
        if (!this.el) this.init();
        if (!this.el) return;

        // Reset classes
        this.el.className = 'toast show';
        this.icon.className = 'bi me-2 toast-icon';

        // Apply types
        switch (type) {
            case 'success':
                this.icon.classList.add('bi-check-circle-fill', 'text-success');
                this.title.className = 'me-auto toast-title text-success';
                break;
            case 'error':
            case 'danger':
                this.icon.classList.add('bi-x-circle-fill', 'text-danger');
                this.title.className = 'me-auto toast-title text-danger';
                break;
            case 'warning':
                this.icon.classList.add('bi-exclamation-triangle-fill', 'text-warning');
                this.title.className = 'me-auto toast-title text-warning';
                break;
            case 'info':
            case 'processing':
                this.icon.classList.add('bi-info-circle-fill', 'text-info');
                this.title.className = 'me-auto toast-title text-info';
                break;
        }

        this.title.innerText = title;
        this.body.innerText = message;

        this.bsToast.show();
    },

    success(message) {
        this.show(message, 'success', 'Success');
    },

    error(message) {
        this.show(message, 'error', 'Error');
    },

    warning(message) {
        this.show(message, 'warning', 'Warning');
    },

    info(message) {
        this.show(message, 'info', 'Info');
    },

    processing(message = 'Processing your request...') {
        this.show(message, 'processing', 'Processing');
    }
};

window.Toast = Toast;
document.addEventListener('DOMContentLoaded', () => Toast.init());
