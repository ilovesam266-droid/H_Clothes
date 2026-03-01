import Pagination from "../pagination.js";
import { ImagePage } from "../images/index.js";
import imagePicker from "../images/image-picker.js";
import CategoryPicker from "../categories/category-picker.js";

const ProductCreate = {

    state: {
        selectedImages: [],
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

            selectedIds: this.state.selectedImages
                .filter(img => img.id)
                .map(img => img.id),

            onConfirm: (images) => {

                images.forEach(img => {
                    this.state.selectedImages.push({
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

        container.innerHTML = this.state.selectedImages.map((img, index) => `
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

        const images = [...this.state.selectedImages];

        [images[dragIndex], images[targetIndex]] =
            [images[targetIndex], images[dragIndex]];

        this.state.selectedImages = images;
        this.state.dragIndex = null;

        this.renderImages();
    },

    removeImage(index) {
        this.state.selectedImages.splice(index, 1);
        this.renderImages();
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
            <button type="button" onclick="productCreateApp.removeDetailField(this)" 
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

        this.state.selectedImages.forEach((img, index) => {
            if (img.id) {
                formData.append(`images[${index}]`, img.id);
            }
        });

        const details = this.collectDetails();
        formData.append('detail', JSON.stringify(details));

        console.log('FORM DATA', formData);

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

                    // Xử lý array field (category_ids.0 → category_ids)
                    const cleanField = field.replace(/\.\d+/g, '');

                    const input = document.querySelector(`[name="${cleanField}"]`);

                    if (!input) {
                        console.warn('⚠ Field not found in DOM:', field);
                        return; // QUAN TRỌNG
                    }

                    input.classList.add('is-invalid');

                    const feedback = document.createElement('div');
                    feedback.className = 'invalid-feedback';
                    feedback.innerText = messages[0];

                    input.parentNode?.appendChild(feedback);
                });

                return window.location.href = productIndexRoute;
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
