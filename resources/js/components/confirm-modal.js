document.addEventListener('alpine:init', () => {
    Alpine.data('confirmModal', () => ({
        open: false,
        title: '',
        body: '',
        danger: true,
        onConfirm: null,

        show({ title, body, danger = true, onConfirm }) {
            this.title = title;
            this.body = body;
            this.danger = danger;
            this.onConfirm = onConfirm;
            this.open = true;
        },

        close() {
            this.open = false;
            this.onConfirm = null;
        },

        async confirm() {
            if (this.onConfirm) {
                await this.onConfirm();
            }
            this.close();
        }
    }));
});

window.confirmModal = {
    open(payload) {
        const el = document.querySelector('[x-data="confirmModal()"]');
        if (!el) return;
        el.__x.$data.show(payload);
    }
};
