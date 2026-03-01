import Pagination from "../pagination.js";

const ImagePicker = {

    state: {
        images: [],
        selected: new Set(),
        modal: null,
        paginationInstance: null,

        multiple: true,
        onConfirm: null,
        loading: false,
    },

    dom: {
        modalEl: null,
        grid: null,
        pagination: null,
        confirmBtn: null,
    },

    /* ================= INIT ================= */

    init() {

        this.cacheDom();

        if (!this.dom.modalEl) return;

        this.state.modal = new bootstrap.Modal(this.dom.modalEl);

        this.state.paginationInstance =
            new Pagination(this.dom.pagination, (url) => {
                this.handlePageUrl(url);
            });

        this.bindEvents();
    },

    cacheDom() {
        this.dom.modalEl = document.getElementById('imagePickerModal');
        this.dom.grid = document.getElementById('imagePickerGrid');
        this.dom.pagination = document.getElementById('imagePickerPagination');
        this.dom.confirmBtn = document.getElementById('confirmImagePicker');
    },

    bindEvents() {

        /* CONFIRM BUTTON */
        if (this.dom.confirmBtn) {
            this.dom.confirmBtn.addEventListener('click', () =>
                this.confirmSelection()
            );
        }

        /* IMAGE CLICK (EVENT DELEGATION) */
        document.addEventListener('click', (e) => {

            const imageItem = e.target.closest('.image-picker-item');

            if (!imageItem) return;

            const id = Number(imageItem.dataset.id);

            if (id) this.toggle(id, imageItem);
        });
    },

    /* ================= OPEN ================= */

    async open(options = {}) {

        if (!this.state.modal) return;

        this.state.multiple = options.multiple ?? true;
        this.state.onConfirm = options.onConfirm ?? null;

        this.state.selected = new Set(options.selectedIds ?? []);

        this.renderLoading();
        this.state.modal.show();

        await this.fetchImages();
    },

    /* ================= FETCH ================= */

    async fetchImages(url = '/api/admin/images') {

        if (this.state.loading) return;

        this.state.loading = true;

        try {
            const res = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
                }
            });

            const data = await res.json();

            if (!res.ok) throw new Error();

            this.state.images = data.data || [];

            this.renderImages();

            this.state.paginationInstance.render(data.meta);

        } catch (e) {
            console.error(e);
            alert('Cant load images');
        }

        this.state.loading = false;
    },

    async handlePageUrl(url) {

        if (!url) return;

        this.renderLoading();

        await this.fetchImages(url);
    },

    /* ================= RENDER ================= */

    renderLoading() {

        if (this.dom.grid) {
            this.dom.grid.innerHTML = `<p>Đang tải ảnh...</p>`;
        }

        if (this.dom.pagination) {
            this.dom.pagination.innerHTML = '';
        }
    },

    renderImages() {

        if (!this.dom.grid) return;

        this.dom.grid.innerHTML = this.state.images.map(img => {

            const active = this.state.selected.has(img.id)
                ? 'selected'
                : '';

            return `
                <div class="image-picker-item ${active}"
                     data-id="${img.id}">
                    <img src="/${img.url}" alt="">
                </div>
            `;
        }).join('');
    },

    /* ================= SELECT ================= */

    toggle(id, el) {

        if (!this.state.multiple) {

            this.state.selected.clear();

            document
                .querySelectorAll('.image-picker-item.selected')
                .forEach(item => item.classList.remove('selected'));
        }

        if (this.state.selected.has(id)) {
            this.state.selected.delete(id);
            el.classList.remove('selected');
        } else {
            this.state.selected.add(id);
            el.classList.add('selected');
        }
    },

    confirmSelection() {
        if (!this.state.selected.size) {
            alert('Pls select image');
            return;
        }

        const selectedImages = this.state.images
            .filter(img => this.state.selected.has(img.id));

        if (typeof this.state.onConfirm === 'function') {
            this.state.onConfirm(selectedImages);
        }

        this.state.modal.hide();
    }
};

window.imagePicker = ImagePicker;
document.addEventListener('DOMContentLoaded', () => ImagePicker.init());

export default ImagePicker;
