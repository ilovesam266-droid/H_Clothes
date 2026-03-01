import { ImagePage } from "../images/index.js";
import imagePicker from "../images/image-picker.js";
import CategoryPicker from "../categories/category-picker.js";

const ProductEdit = {

    state: {
        selectedImages: [],
        dragIndex: null,
    },

    dom: {},

    init() {
        this.dom = {
            input: document.getElementById('categoryIds'),
        };

        this.bindForm();
        this.bindEvents();

        CategoryPicker.init({
            modalId: 'categoryModal',
            listId: 'categoryList',
            paginationId: 'categoryPagination',
            containerId: 'categoriesContainer',
            inputId: 'categoryIds',
        });

        this.loadProduct();
    },

    bindEvents() {
        document
            .getElementById('categoriesContainer')
            ?.addEventListener('click', () => CategoryPicker.open());
    },

    confirmSelectCategories() {
        CategoryPicker.confirm();
    },

    clearSelection() {
        CategoryPicker.clearSelection?.();
    },

    /**
     * Load existing product data from API and populate the form.
     */
    async loadProduct() {
        try {
            const res = await fetch(`/api/admin/products/${productId}`, {
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem('auth_token'),
                    'Accept': 'application/json',
                },
            });

            const data = await res.json();

            if (!res.ok) {
                console.error('Failed to load product', data);
                return;
            }

            const product = data.data ?? data;

            // Populate text fields
            const nameEl = document.getElementById('name');
            const statusEl = document.getElementById('status');
            const descEl = document.getElementById('description');

            if (nameEl) { nameEl.value = product.name ?? ''; }
            if (statusEl) { statusEl.value = product.status ?? 1; }
            if (descEl) { descEl.value = product.description ?? ''; }

            // Populate details
            if (product.detail && typeof product.detail === 'object') {
                this.renderDetailFields(product.detail);
            }

            // Populate images
            if (Array.isArray(product.images)) {
                this.state.selectedImages = product.images
                    .sort((a, b) => (a.position ?? 0) - (b.position ?? 0))
                    .map(img => ({
                        id: img.id ?? null,
                        preview: img.url,
                        file: null,
                    }));

                this.renderImages();
            }

            // Populate categories
            if (Array.isArray(product.categories) && product.categories.length > 0) {
                CategoryPicker.setSelected?.(product.categories);
            }

        } catch (e) {
            console.error('loadProduct error:', e);
        }
    },

    // ─── Images ────────────────────────────────────────────────────────────

    openImagePicker() {
        imagePicker.open({
            multiple: true,

            selectedIds: this.state.selectedImages
                .filter(img => img.id)
                .map(img => img.id),

            onConfirm: (images) => {
                images.forEach(img => {
                    // Prevent duplicates
                    const exists = this.state.selectedImages.some(s => s.id === img.id);
                    if (!exists) {
                        this.state.selectedImages.push({
                            id: img.id,
                            preview: img.url,
                            file: null,
                        });
                    }
                });

                this.renderImages();
            },
        });
    },

    renderImages() {
        const container = document.getElementById('imagePreview');
        if (!container) { return; }

        container.innerHTML = this.state.selectedImages.map((img, index) => `
            <div class="preview-item"
                 draggable="true"
                 data-index="${index}"
                 ondragstart="productEditApp.dragStart(event)"
                 ondragover="productEditApp.dragOver(event)"
                 ondrop="productEditApp.drop(event)">

                <img src="/${img.preview}" />

                <button type="button"
                        onclick="productEditApp.removeImage(${index})">
                    ✕
                </button>
            </div>
        `).join('');
    },

    dragStart(event) {
        this.state.dragIndex = Number(event.currentTarget.dataset.index);
    },

    dragOver(event) {
        event.preventDefault();
    },

    drop(event) {
        event.preventDefault();

        const targetIndex = Number(event.currentTarget.dataset.index);
        const dragIndex = this.state.dragIndex;

        if (dragIndex === null || dragIndex === targetIndex) { return; }

        const images = [...this.state.selectedImages];
        [images[dragIndex], images[targetIndex]] = [images[targetIndex], images[dragIndex]];

        this.state.selectedImages = images;
        this.state.dragIndex = null;

        this.renderImages();
    },

    removeImage(index) {
        this.state.selectedImages.splice(index, 1);
        this.renderImages();
    },

    // ─── Details ───────────────────────────────────────────────────────────

    renderDetailFields(detail) {
        const container = document.getElementById('detailFields');
        if (!container) { return; }

        container.innerHTML = '';

        const entries = Object.entries(detail);

        if (entries.length === 0) {
            this.addDetailField();
            return;
        }

        entries.forEach(([key, value]) => {
            const div = document.createElement('div');
            div.className = 'detail-field row g-2 mt-2';
            div.innerHTML = `
                <div class="col-md-5">
                    <input type="text" placeholder="Name (vd: Color, size...)"
                           class="form-control detail-key" value="${this.escapeHtml(key)}">
                </div>
                <div class="col-md-5">
                    <input type="text" placeholder="Value"
                           class="form-control detail-value" value="${this.escapeHtml(String(value))}">
                </div>
                <div class="col-md-2">
                    <button type="button" onclick="productEditApp.removeDetailField(this)"
                            class="btn btn-danger w-100">
                        <i class="bx bx-trash"></i>
                    </button>
                </div>
            `;
            container.appendChild(div);
        });
    },

    escapeHtml(str) {
        return str
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    },

    collectDetails() {
        const details = {};
        const rows = document.querySelectorAll('#detailFields .detail-field');

        rows.forEach(row => {
            const key = row.querySelector('.detail-key').value.trim();
            const value = row.querySelector('.detail-value').value.trim();

            if (key !== '') {
                details[key] = value;
            }
        });

        return details;
    },

    addDetailField() {
        const container = document.getElementById('detailFields');

        const newField = document.createElement('div');
        newField.className = 'detail-field row g-2 mt-2';

        newField.innerHTML = `
            <div class="col-md-5">
                <input type="text" placeholder="Name (vd: Color, size...)"
                       class="form-control detail-key">
            </div>
            <div class="col-md-5">
                <input type="text" placeholder="Value"
                       class="form-control detail-value">
            </div>
            <div class="col-md-2">
                <button type="button" onclick="productEditApp.removeDetailField(this)"
                        class="btn btn-danger w-100">
                    <i class="bx bx-trash"></i>
                </button>
            </div>
        `;

        container.appendChild(newField);
    },

    removeDetailField(button) {
        const field = button.closest('.detail-field');
        const allFields = document.querySelectorAll('#detailFields .detail-field');

        if (allFields.length === 1) {
            alert('Phải có ít nhất 1 detail!');
            return;
        }

        field.remove();
    },

    // ─── Form ──────────────────────────────────────────────────────────────

    bindForm() {
        const form = document.getElementById('productForm');
        if (!form) { return; }

        form.addEventListener('submit', (e) => this.handleSubmit(e));
    },

    async handleSubmit(event) {
        event.preventDefault();

        const form = document.getElementById('productForm');
        const formData = new FormData(form);

        // Attach images in order (by position index)
        this.state.selectedImages.forEach((img, index) => {
            if (img.id) {
                formData.append(`images[${index}]`, img.id);
            }
        });

        // Attach details as JSON string
        const details = this.collectDetails();
        formData.append('detail', JSON.stringify(details));

        try {
            const res = await fetch(`/api/admin/products/${productId}/edit`, {
                method: 'POST',
                body: formData,
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem('auth_token'),
                    'Accept': 'application/json',
                },
            });

            const data = await res.json();

            const alertBox = document.getElementById('formErrorAlert');
            alertBox.classList.add('d-none');
            alertBox.innerHTML = '';

            if (res.status === 422 && data.errors) {

                // Clear old errors
                document.querySelectorAll('.is-invalid').forEach(el => {
                    el.classList.remove('is-invalid');
                });

                document.querySelectorAll('.invalid-feedback').forEach(el => {
                    el.remove();
                });

                Object.entries(data.errors).forEach(([field, messages]) => {
                    const cleanField = field.replace(/\.\d+/g, '');
                    const input = document.querySelector(`[name="${cleanField}"]`);

                    if (!input) {
                        console.warn('⚠ Field not found in DOM:', field);
                        return;
                    }

                    input.classList.add('is-invalid');

                    const feedback = document.createElement('div');
                    feedback.className = 'invalid-feedback';
                    feedback.innerText = messages[0];

                    input.parentNode?.appendChild(feedback);
                });

                return;
            }

            if (res.ok) {
                console.log('SUCCESS', data);
                window.location.href = productIndexRoute;
            } else {
                alertBox.classList.remove('d-none');
                alertBox.innerHTML = data.message ?? 'An error occurred.';
            }

        } catch (e) {
            console.error('handleSubmit error:', e);
        }
    },
};

window.productEditApp = ProductEdit;
document.addEventListener('DOMContentLoaded', () => ProductEdit.init());
document.addEventListener('DOMContentLoaded', () => {
    const btn = document.querySelector('.btn-add');
    if (btn) {
        btn.addEventListener('click', () => { ImagePage.openUploadModal(); });
    }
});
