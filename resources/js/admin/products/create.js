import Pagination from "../pagination.js";
import { ImagePage } from "../images/index.js";
import imagePicker from "../images/image-picker.js";
import CategoryPicker from "../categories/category-picker.js";

const ProductCreate = {

    state: {
        images: [],
        dragIndex: null,
    },

    dom: {},

    init() {
        this.dom = {
            input: document.getElementById(config.inputId ?? 'categoryIds')
        };
        // this.bindFileInput();
        this.bindForm();
        this.bindEvents();

        CategoryPicker.init({
            modalId: 'categoryModal',
            listId: 'categoryList',
            paginationId: 'categoryPagination',
            containerId: 'categoriesContainer',
            inputId: 'categoryIds',
        });
    },

    bindEvents() {
        document
            .getElementById('categoriesContainer')
            ?.addEventListener('click', () => CategoryPicker.open());
    },

    confirmSelectCategories() {
        CategoryPicker.confirm();
    },

    openImagePicker() {
        imagePicker.open({
            multiple: true,

            selectedIds: this.state.images
                .filter(img => img.id)
                .map(img => img.id),

            onConfirm: (images) => {

                images.forEach(img => {
                    this.state.images.push({
                        id: img.id,
                        preview: img.url,
                        file: null,
                    });
                });

                this.renderImages();
            }
        });
    },

    renderImages() {

        const container = document.getElementById('imagePreview');
        if (!container) return;

        container.innerHTML = this.state.images.map((img, index) => `
            <div class="preview-item"
                 draggable="true"
                 data-index="${index}"

                 ondragstart="productCreateApp.dragStart(event)"
                 ondragover="productCreateApp.dragOver(event)"
                 ondrop="productCreateApp.drop(event)">

                <img src="/${img.preview}" />

                <button type="button"
                        onclick="productCreateApp.removeImage(${index})">
                    ✕
                </button>
            </div>
        `).join('');
    },

    dragStart(event) {
        this.state.dragIndex =
            Number(event.currentTarget.dataset.index);
    },

    dragOver(event) {
        event.preventDefault();
    },

    drop(event) {
        event.preventDefault();

        const targetIndex =
            Number(event.currentTarget.dataset.index);

        const dragIndex = this.state.dragIndex;

        if (dragIndex === null || dragIndex === targetIndex) return;

        const images = [...this.state.images];

        [images[dragIndex], images[targetIndex]] =
            [images[targetIndex], images[dragIndex]];

        this.state.images = images;
        this.state.dragIndex = null;

        this.renderImages();
    },

    removeImage(index) {
        this.state.images.splice(index, 1);
        this.renderImages();
    },

    bindForm() {
        const form = document.getElementById('productForm');

        if (!form) return;

        form.addEventListener('submit', (e) =>
            this.handleSubmit(e));
    },

    async handleSubmit(event) {
        event.preventDefault();

        const form = document.getElementById('productForm');
        const formData = new FormData(form);

        this.state.images.forEach((img, index) => {
            if (img.id) {
                formData.append(`images[${index}]`, img.id);
            }
        });

        try {
            const res = await fetch('/api/admin/products/create', {
                method: 'POST',
                body: formData,
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem("auth_token"),
                    'Accept': 'application/json'
                }
            });

            const data = await res.json();

            // Validation error (Laravel 422)
            if (res.status === 422 && data.errors) {
                Object.entries(data.errors).forEach(([field, messages]) => {
                    const input = document.querySelector(`[name="${field}"]`);
                    if (!input) return;

                    input.classList.add('is-invalid');

                    const feedback = document.createElement('div');
                    feedback.className = 'invalid-feedback';
                    feedback.innerText = messages[0];

                    input.parentNode.appendChild(feedback);
                });
                return;
            }

            if (!res.ok) {
                throw new Error(data.message || 'Request failed');
            }


            console.log('SUCCESS', data);

        } catch (e) {
            console.error(e);
        }
    }
};

window.productCreateApp = ProductCreate;
document.addEventListener('DOMContentLoaded', () => ProductCreate.init());
document.addEventListener('DOMContentLoaded', () => { const btn = document.querySelector('.btn-add'); if (btn) { btn.addEventListener('click', () => { ImagePage.openUploadModal(); }); } });
