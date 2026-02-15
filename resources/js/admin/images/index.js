import Pagination from "../pagination.js";

const ImagePage = {
    /* ===================== STATE ===================== */
    state: {
        images: [],
        imageShow: null,
        selected: new Set(),
        deleteId: null,
        editId: null,

        currentTab: 'active',
        search: '',
        createdBy: '',
        date: '',
        perPage: 10,
        currentPage: 1,

        pagination: {},
        loading: false,
    },

    dom: {},

    /* ===================== INIT ===================== */
    init() {
        this.cacheDom();
        this.bindEvents();
        this.initializeImagePreview();
        this.fetchImages();
    },

    cacheDom() {
        this.dom = {
            searchInput: document.getElementById('searchInput'),
            createdByFilter: document.getElementById('createdByFilter'),
            dateFilter: document.getElementById('dateFilter'),
            perPage: document.getElementById('perPage'),

            tbody: document.getElementById('imageTableBody'),
            pagination: document.getElementById('pagination'),

            bulkBar: document.getElementById('bulkActionsBar'),
            selectedCount: document.getElementById('selectedCount'),
            selectAll: document.getElementById('selectAll'),
        };
    },

    /* ===================== EVENTS ===================== */
    bindEvents() {
        this.debounceSearch = this.debounce(e => {
            this.state.search = e.target.value;
            this.resetAndFetch();
        }, 400);

        this.dom.searchInput?.addEventListener('input', this.debounceSearch);

        this.dom.createdByFilter?.addEventListener('change', e => {
            this.state.createdBy = e.target.value;
            this.resetAndFetch();
        });

        this.dom.dateFilter?.addEventListener('change', e => {
            this.state.date = e.target.value;
            this.resetAndFetch();
        });

        this.dom.perPage?.addEventListener('change', e => {
            this.state.perPage = e.target.value;
            this.resetAndFetch();
        });
    },

    initializeImagePreview() {
        // Preview for multiple upload modal
        const imageFile = document.getElementById('imageFile');
        if (imageFile) {
            imageFile.addEventListener('change', (e) => {
                const files = e.target.files;
                const previewContainer = document.getElementById('imagePreviewContainer');
                const previewGrid = document.getElementById('imagePreviewGrid');
                const imageCount = document.getElementById('imageCount');

                if (files.length > 0) {
                    previewContainer.style.display = 'block';
                    previewGrid.innerHTML = '';
                    imageCount.textContent = files.length;

                    Array.from(files).forEach((file, index) => {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            const previewItem = document.createElement('div');
                            previewItem.className = 'preview-item';
                            previewItem.innerHTML = `
                                <img src="${e.target.result}" alt="Preview ${index + 1}">
                                <button type="button" class="btn-remove-preview" onclick="imagePageApp.removePreview(${index})" title="Remove">
                                    <i class="bx bx-x"></i>
                                </button>
                                <div class="preview-info">
                                    <small>${file.name}</small>
                                    <small class="text-muted">${(file.size / 1024).toFixed(2)} KB</small>
                                </div>
                            `;
                            previewGrid.appendChild(previewItem);
                        };
                        reader.readAsDataURL(file);
                    });
                } else {
                    previewContainer.style.display = 'none';
                    previewGrid.innerHTML = '';
                    imageCount.textContent = '0';
                }
            });
        }

        // Preview for edit modal (single image)
        const editImageFile = document.getElementById('editImageFile');
        if (editImageFile) {
            editImageFile.addEventListener('change', (e) => {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        document.getElementById('editImagePreview').src = e.target.result;
                        document.getElementById('editImagePreviewContainer').style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                }
            });
        }
    },

    resetAndFetch() {
        this.state.currentPage = 1;
        this.fetchImages();
    },

    /* ===================== API ===================== */
    async fetchImages() {
        this.state.loading = true;

        const res = await axios.get('/api/admin/images', {
            params: {
                trashed: this.state.currentTab,
                search: this.state.search,
                perPage: this.state.perPage,
                filters: {
                    created_by: this.state.createdBy,
                    date: this.state.date,
                },
                ...this.getQueryParams(),
            }
        });

        this.state.images = res.data.data;
        this.state.pagination = res.data.meta;

        this.renderTable();
        this.renderPagination();
        this.updateBulkBar();

        this.state.loading = false;
    },

    /* ===================== SELECT ===================== */
    toggleSelectAll() {
        const checked = this.dom.selectAll.checked;

        this.state.images.forEach(img =>
            checked
                ? this.state.selected.add(img.id)
                : this.state.selected.delete(img.id)
        );

        this.renderTable();
        this.updateBulkBar();
    },

    toggleSelect(id) {
        this.state.selected.has(id)
            ? this.state.selected.delete(id)
            : this.state.selected.add(id);

        this.updateBulkBar();
        this.updateSelectAllCheckbox();
    },

    updateSelectAllCheckbox() {
        const all =
            this.state.images.length &&
            this.state.images.every(img => this.state.selected.has(img.id));

        this.dom.selectAll.checked = all;
    },

    updateBulkBar() {
        const count = this.state.selected.size;
        this.dom.selectedCount.textContent = count;

        this.dom.bulkBar.classList.toggle('show', count > 0);

        document.getElementById('bulkDeleteBtn').style.display =
            this.state.currentTab === 'active' ? 'inline-block' : 'none';

        document.getElementById('bulkRestoreBtn').style.display =
            this.state.currentTab !== 'active' ? 'inline-block' : 'none';
    },

    clearSelection() {
        this.state.selected.clear();
        this.dom.selectAll.checked = false;
        this.renderTable();
        this.updateBulkBar();
    },

    /* ===================== SIDEBAR ===================== */
    async openSidebar(id) {
        if (!id) return;

        try {
            const { data } = await axios.get(`/api/admin/images/${id}`);
            this.state.imageShow = data.data ?? data;
            this.renderSidebar(this.state.imageShow);
        } catch (e) {
            console.error('Load image failed', e);
        }
    },

    async openEditModal(id) {
        if (!id) return;

        try {
            const { data } = await axios.get(`/api/admin/images/${id}`);
            const image = data.data ?? data;

            this.state.editId = id;
            document.getElementById('editImageId').value = image.id;
            document.getElementById('currentImage').src = image.url;
            document.getElementById('editImagePreviewContainer').style.display = 'none';
            document.getElementById('editImageFile').value = '';

            const modalEl = document.getElementById('editImageModal');
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        } catch (e) {
            console.error('Load image failed', e);
        }
    },

    /* ===================== UPLOAD / CREATE ===================== */
    openUploadModal() {
        document.getElementById('uploadImageForm').reset();
        document.getElementById('imagePreviewContainer').style.display = 'none';
        document.getElementById('imagePreviewGrid').innerHTML = '';
        document.getElementById('imageCount').textContent = '0';
        const modalEl = document.getElementById('uploadImageModal');
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    },

    removePreview(index) {
        const fileInput = document.getElementById('imageFile');
        const dt = new DataTransfer();
        const files = fileInput.files;

        // Copy all files except the removed one
        Array.from(files).forEach((file, i) => {
            if (i !== index) {
                dt.items.add(file);
            }
        });

        // Update the file input
        fileInput.files = dt.files;

        // Trigger change event to refresh preview
        fileInput.dispatchEvent(new Event('change'));
    },

    async confirmUploadImage() {
        const form = document.getElementById('uploadImageForm');
        const formData = new FormData(form);
        const fileInput = document.getElementById('imageFile');
        const files = fileInput.files;

        if (files.length === 0) {
            alert('Please select at least one image');
            return;
        }

        try {
            // Show loading state
            const modalEl = document.getElementById('uploadImageModal');
            const uploadBtn = modalEl.querySelector('[onclick*="confirmUploadImage"]');
            const originalText = uploadBtn.textContent;
            uploadBtn.disabled = true;
            uploadBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Uploading...';

            const res = await fetch(`/api/admin/images/upload`, {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem("auth_token"),
                    'Accept': 'application/json',
                },
                body: formData
            });

            const data = await res.json();

            if (!res.ok) {
                throw new Error(data.message || 'Upload failed');
            }

            console.log('SUCCESS', data);

            // Show success message
            const successMsg = data.uploaded_count
                ? `Successfully uploaded ${data.uploaded_count} image(s)`
                : 'Images uploaded successfully';

            alert(successMsg);

            bootstrap.Modal.getInstance(modalEl)?.hide();
            this.fetchImages();

            // Reset button state
            uploadBtn.disabled = false;
            uploadBtn.textContent = originalText;
        } catch (e) {
            console.error(e);
            alert('Upload failed: ' + e.message);

            // Reset button state
            const modalEl = document.getElementById('uploadImageModal');
            const uploadBtn = modalEl.querySelector('[onclick*="confirmUploadImage"]');
            uploadBtn.disabled = false;
            uploadBtn.textContent = 'Upload All';
        }
    },

    async confirmEditImage() {
        const imageId = document.getElementById('editImageId').value;
        const form = document.getElementById('editImageForm');
        const formData = new FormData(form);

        try {
            const res = await fetch(`/api/admin/images/${imageId}/edit`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                },
                body: formData
            });

            const data = await res.json();

            if (!res.ok) {
                throw new Error(data.message || 'Update failed');
            }

            console.log('SUCCESS', data);
            const modalEl = document.getElementById('editImageModal');
            bootstrap.Modal.getInstance(modalEl)?.hide();

            this.fetchImages();
        } catch (e) {
            console.error(e);
            alert('Update failed');
        }
    },

    /* ===================== DELETE / RESTORE ===================== */
    async confirmBulkDelete() {
        try {
            bootstrap.Modal.getInstance(
                document.getElementById('confirmBulkDeleteModal')
            )?.hide();

            await axios.post('/api/admin/images', [...this.state.selected]);

            this.clearSelection();
            this.fetchImages();
        } catch (e) {
            console.error(e);
            alert('Bulk delete failed');
        }
    },

    async confirmBulkRestore() {
        try {
            bootstrap.Modal.getInstance(
                document.getElementById('confirmBulkRestoreModal')
            )?.hide();

            await axios.patch('/api/admin/images', [...this.state.selected]);
            this.clearSelection();
            this.fetchImages();
        } catch (e) {
            console.error(e);
            alert('Bulk restore failed');
        }
    },

    async confirmDelete() {
        try {
            bootstrap.Modal.getInstance(
                document.getElementById('confirmDeleteModal')
            )?.hide();

            await axios.post(`/api/admin/images`, [...this.state.selected]);
            this.state.deleteId = null;
            this.fetchImages();
            this.closeSidebar();
        } catch (e) {
            console.error(e);
            alert('Delete failed');
        }
    },

    async restoreImage(id) {
        try {
            await axios.patch(`/api/admin/images`, [id]);
            this.fetchImages();
            this.closeSidebar();
        } catch (e) {
            console.error(e);
            alert('Restore failed');
        }
    },

    async forceDelete(id) {
        if (!confirm('Are you sure? This action cannot be undone!')) return;

        try {
            await axios.post(`/api/admin/images`, [id]);
            this.fetchImages();
            this.closeSidebar();
        } catch (e) {
            console.error(e);
            alert('Force delete failed');
        }
    },

    openDeleteModal(id) {
        this.state.deleteId = id;
        new bootstrap.Modal(document.getElementById('confirmDeleteModal')).show();
    },

    openBulkDeleteModal() {
        const count = this.state.selected.size;

        if (!count) return;

        document.getElementById('bulkDeleteCount').innerText = count;

        const modalEl = document.getElementById('confirmBulkDeleteModal');
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    },

    openBulkRestoreModal() {
        const count = this.state.selected.size;

        if (!count) return;

        document.getElementById('bulkRestoreCount').innerText = count;

        const modalEl = document.getElementById('confirmBulkRestoreModal');
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    },

    /* ===================== TAB ===================== */
    switchTab(tab, el) {
        this.state.currentTab = tab;
        this.clearSelection();

        document
            .querySelectorAll('.nav-tabs-custom .nav-link')
            .forEach(l => l.classList.remove('active'));

        el.classList.add('active');

        history.replaceState({}, '', location.pathname);
        this.fetchImages();
    },

    /* ===================== RENDER ===================== */
    renderTable() {
        const tbody = this.dom.tbody;
        tbody.innerHTML = '';

        if (!this.state.images.length) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="9" class="text-center text-muted">
                        No images found
                    </td>
                </tr>`;
            return;
        }

        this.state.images.forEach((img, i) => {
            const checked = this.state.selected.has(img.id);
            const isDeleted = img.deleted_at !== null;

            tbody.innerHTML += `
            <tr>
                <td>
                    <input type="checkbox"
                        ${checked ? 'checked' : ''}
                        onchange="imagePageApp.toggleSelect(${img.id})">
                </td>
                <td>${i + 1}</td>
                <td>
                    <img src="/${img.url}"
                         alt="Image"
                         class="img-thumbnail cursor-pointer"
                         style="width: 60px; height: 60px; object-fit: cover;"
                         onclick="imagePageApp.openSidebar(${img.id})">
                </td>
                <td>
                    <a href="${img.url}" target="_blank" class="text-truncate d-inline-block" style="max-width: 300px;">
                        ${img.url}
                    </a>
                </td>
                <td>
                    <span class="text-nowrap">${img.created_by?.name ?? 'N/A'}</span>
                    <small class="text-muted d-block">${img.created_by?.email ?? 'n/a'}</small>
                </td>
                <td>
                    <span class="badge bg-info">${img.imageables_count || 0}</span>
                </td>
                <td>
                    <span class="text-nowrap">${img.created_at?.date}</span>
                    <small class="text-muted d-block">${img.created_at?.time}</small>
                </td>
                <td>
                    <span class="text-nowrap">${img.updated_at?.date}</span>
                    <small class="text-muted d-block">${img.updated_at?.time}</small>
                </td>
                <td class="text-nowrap text-center">
                    <button class="btn btn-info btn-action"
                        onclick="imagePageApp.openSidebar(${img.id})">
                        <i class="bx bx-scan"></i>
                    </button>
                    ${!isDeleted ? `
                        <button class="btn btn-warning btn-action"
                            onclick="imagePageApp.openEditModal(${img.id})">
                            <i class="bx bx-edit"></i>
                        </button>
                        <button class="btn btn-danger btn-action"
                            onclick="imagePageApp.openDeleteModal(${img.id})">
                            <i class="bx bx-trash"></i>
                        </button>
                    ` : `
                        <button class="btn btn-success btn-action"
                            onclick="imagePageApp.restoreImage(${img.id})"
                            title="Restore">
                            <i class="bx bx-undo"></i>
                        </button>
                        <button class="btn btn-danger btn-action"
                            onclick="imagePageApp.forceDelete(${img.id})"
                            title="Permanent Delete">
                            <i class="bx bx-trash-alt"></i>
                        </button>
                    `}
                </td>
            </tr>`;
        });

        this.updateSelectAllCheckbox();
    },

    renderSidebar(image) {
        const sidebar = document.getElementById('imageSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const content = document.getElementById('sidebarContent');

        if (!sidebar || !overlay || !content) return;

        let usageHtml = '';
        if (image.imageables && image.imageables.length > 0) {
            usageHtml = `
                <div class="info-group">
                    <div class="info-group-title">Used In</div>
                    ${image.imageables.map(rel => `
                        <div class="info-row">
                            <span class="info-label">
                                <i class="bi bi-link"></i> ${rel.imageable_type}
                            </span>
                            <span class="info-value">ID: ${rel.imageable_id}</span>
                        </div>
                    `).join('')}
                </div>
            `;
        }

        content.innerHTML = `
            <div class="mb-3 text-center">
                <img src="/${image.url}" alt="Image" class="img-fluid rounded" style="max-height: 300px;">
            </div>

            <div class="info-group">
                <div class="info-group-title">Image Information</div>

                <div class="info-row">
                    <span class="info-label">
                        <i class="bi bi-hash"></i> ID
                    </span>
                    <span class="info-value">${image.id}</span>
                </div>

                <div class="info-row">
                    <span class="info-label">
                        <i class="bi bi-link"></i> URL
                    </span>
                    <span class="info-value">
                        <a href="${image.url}" target="_blank" class="text-break">${image.url}</a>
                    </span>
                </div>

                <div class="info-row">
                    <span class="info-label">
                        <i class="bi bi-graph-up"></i> Usage Count
                    </span>
                    <span class="info-value">
                        <span class="badge bg-info">${image.imageables_count || 0}</span>
                    </span>
                </div>
            </div>

            <div class="info-group">
                <div class="info-group-title">Created By</div>

                <div class="info-row">
                    <span class="info-label">
                        <i class="bi bi-person"></i> Name
                    </span>
                    <span class="info-value">${image.created_by?.name || 'N/A'}</span>
                </div>

                <div class="info-row">
                    <span class="info-label">
                        <i class="bi bi-envelope"></i> Email
                    </span>
                    <span class="info-value">${image.created_by?.email || 'N/A'}</span>
                </div>
            </div>

            ${usageHtml}

            <div class="info-group">
                <div class="info-group-title">Timestamps</div>

                <div class="info-row">
                    <span class="info-label">
                        <i class="bi bi-clock-history"></i> Created At
                    </span>
                    <span class="info-value">
                        ${image.created_at?.date || ''} ${image.created_at?.time || ''}
                    </span>
                </div>

                <div class="info-row">
                    <span class="info-label">
                        <i class="bi bi-arrow-repeat"></i> Updated At
                    </span>
                    <span class="info-value">
                        ${image.updated_at?.date || ''} ${image.updated_at?.time || ''}
                    </span>
                </div>

                ${image.deleted_at
                    ? `
                        <div class="info-row">
                            <span class="info-label text-danger">
                                <i class="bi bi-trash"></i> Deleted At
                            </span>
                            <span class="info-value text-danger">
                                ${image.deleted_at}
                            </span>
                        </div>
                    `
                    : ''
                }
            </div>
        `;

        sidebar.classList.add('show');
        overlay.classList.add('show');
    },

    closeSidebar() {
        const sidebar = document.getElementById('imageSidebar');
        const overlay = document.getElementById('sidebarOverlay');

        sidebar?.classList.remove('show');
        overlay?.classList.remove('show');
    },

    renderPagination() {
        this.pagination = new Pagination(this.dom.pagination, (url) => this.goPage(url));
        this.pagination.render(this.state.pagination);
    },

    goPage(url) {
        const page = new URL(url).searchParams.get('ImageDashboard');
        if (!page) return;

        this.setQuery('ImageDashboard', page);
        this.fetchImages();
    },

    /* ===================== UTIL ===================== */
    debounce(fn, delay) {
        let t;
        return (...args) => {
            clearTimeout(t);
            t = setTimeout(() => fn.apply(this, args), delay);
        };
    },

    getQueryParams() {
        return Object.fromEntries(new URLSearchParams(location.search));
    },

    setQuery(key, value) {
        const params = new URLSearchParams(location.search);
        params.set(key, value);
        history.replaceState({}, '', '?' + params.toString());
    }
};

window.imagePageApp = ImagePage;
document.addEventListener('DOMContentLoaded', () => ImagePage.init());
