import Pagination from "../pagination.js";

const CategoryPage = {
    /* ===================== STATE ===================== */
    state: {
        categories: [],
        categoryShow: null,
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
        this.fetchCategories();
    },

    cacheDom() {
        this.dom = {
            searchInput: document.getElementById('searchInput'),
            createdByFilter: document.getElementById('createdByFilter'),
            dateFilter: document.getElementById('dateFilter'),
            perPage: document.getElementById('perPage'),

            tbody: document.getElementById('categoryTableBody'),
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

    resetAndFetch() {
        this.state.currentPage = 1;
        this.fetchCategories();
    },

    /* ===================== API ===================== */
    async fetchCategories() {
        this.state.loading = true;

        const res = await axios.get('/api/admin/categories', {
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

        this.state.categories = res.data.data;
        this.state.pagination = res.data.meta;

        this.renderTable();
        this.renderPagination();
        this.updateBulkBar();

        this.state.loading = false;
    },

    async loadCategories(params = {}) {
        const res = await axios.get('/api/admin/categories', {
            params: {
                trashed: 'active',
                perPage: 1000,
                ...params
            }
        });

        return res.data.data;
    },

    /* ===================== SELECT ===================== */
    toggleSelectAll() {
        const checked = this.dom.selectAll.checked;

        this.state.categories.forEach(c =>
            checked
                ? this.state.selected.add(c.id)
                : this.state.selected.delete(c.id)
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
            this.state.categories.length &&
            this.state.categories.every(c => this.state.selected.has(c.id));

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
            const { data } = await axios.get(`/api/admin/categories/${id}`);
            this.state.categoryShow = data.data ?? data;
            this.renderSidebar(this.state.categoryShow);
        } catch (e) {
            console.error('Load category failed', e);
        }
    },

    async openEditModal(id) {
        if (!id) return;

        try {
            const { data } = await axios.get(`/api/admin/categories/${id}`);
            this.state.categoryShow = data.data ?? data;
            this.openModal(this.state.categoryShow);
        } catch (e) {
            console.error('Load category failed', e);
        }
    },

    /* ===================== DELETE / RESTORE ===================== */
    async confirmBulkDelete() {
        try {
            bootstrap.Modal.getInstance(
                document.getElementById('confirmBulkDeleteModal')
            )?.hide();

            await axios.post('/api/admin/categories', [...this.state.selected]);

            this.clearSelection();
            this.fetchCategories();
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

            await axios.patch('/api/admin/categories', [...this.state.selected]);
            this.clearSelection();
            this.fetchCategories();
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

            await axios.delete(`/api/admin/categories/${this.state.deleteId}/delete`);
            this.state.deleteId = null;
            this.fetchCategories();
        } catch (e) {
            console.error(e);
            alert('Delete failed');
        }
    },

    async confirmEditCategory() {
        const categoryId = document.getElementById('editCategoryId').value;

        const form = document.getElementById('editCategoryForm');
        const formData = new FormData(form);

        try {
            const res = await fetch(`/api/admin/categories/${categoryId}/edit`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                },
                body: formData
            });

            const data = await res.json();

            // Validation error
            if (res.status === 422 && data.errors) {
                Object.entries(data.errors).forEach(([field, messages]) => {
                    const input = this.form.querySelector(`[name="${field}"]`);
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
                throw new Error(data.message || 'Update failed');
            }

            console.log('SUCCESS', data);
            const modalEl = document.getElementById('editCategoryModal');
            bootstrap.Modal.getInstance(modalEl)?.hide();

            this.fetchCategories();
        } catch (e) {
            console.error(e);
            alert('Update failed');
        }
    },

    async confirmCreateCategory() {
        const form = document.getElementById('createCategoryForm');
        const formData = new FormData(form);
        // console.log(localStorage.getItem("token"))
        try {
            const res = await fetch(`/api/admin/categories/create`, {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem("auth_token"),
                    'Accept': 'application/json',
                },
                body: formData
            });

            const data = await res.json();

            // Validation error
            if (res.status === 422 && data.errors) {
                Object.entries(data.errors).forEach(([field, messages]) => {
                    const input = this.form.querySelector(`[name="${field}"]`);
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
                throw new Error(data.message || 'Update failed');
            }

            console.log('SUCCESS', data);
            const modalEl = document.getElementById('createCategoryModal');
            bootstrap.Modal.getInstance(modalEl)?.hide();

            this.fetchCategories();
        } catch (e) {
            console.error(e);
            alert('Create failed');
        }
    },

    openCreateModal() {
        const modalEl = document.getElementById('createCategoryModal');
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
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

    clearSelection() {
        this.state.selected.clear();

        if (this.dom.selectAll) {
            this.dom.selectAll.checked = false;
        }

        this.renderTable();

        this.updateBulkBar();
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
        this.fetchCategories();
    },

    /* ===================== RENDER ===================== */
    renderTable() {
        const tbody = this.dom.tbody;
        tbody.innerHTML = '';

        if (!this.state.categories.length) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="9" class="text-center text-muted">
                        No categories found
                    </td>
                </tr>`;
            return;
        }

        this.state.categories.forEach((c, i) => {
            const checked = this.state.selected.has(c.id);

            tbody.innerHTML += `
            <tr class="${checked ? 'table-active' : ''}">
                <td>
                    <input type="checkbox"
                        ${checked ? 'checked' : ''}
                        onchange="categoryPageApp.toggleSelect(${c.id})">
                </td>
                <td>${i + 1}</td>
                <td><strong>${c.name}</strong></td>
                <td><code>${c.slug}</code></td>
                <td>
                    <span class="text-nowrap">${c.created_by.name ?? 'N/A'}</span>
                    <small class="text-muted d-block">${c.created_by.email ?? 'n/a'}</small>
                </td>
                <td>
                    <span class="badge bg-info">${c.usage_count}</span>
                </td>
                <td>
                        <span class="text-nowrap">${c.created_at.date}</span>
                        <small class="text-muted d-block">${c.created_at.time}</small>
                </td>
                <td>
                    <span class="text-nowrap">${c.updated_at.date}</span>
                        <small class="text-muted d-block">${c.updated_at.time}</small>
                </td>
                <td class="text-nowrap text-center">
                    <button class="btn btn-info btn-action"
                        onclick="categoryPageApp.openSidebar(${c.id})">
                        <i class="bx bx-scan"></i>
                    </button>
                    <button
                        class="btn btn-warning btn-action"
                        onclick="categoryPageApp.openEditModal(${c.id})">
                        <i class="bx bx-edit"></i>
                    </button>
                    <button class="btn btn-danger btn-action"
                        onclick="categoryPageApp.openDeleteModal(${c.id})">
                        <i class="bx bx-trash"></i>
                    </button>
                </td>
            </tr>`;
        });

        this.updateSelectAllCheckbox();
    },

    renderSidebar(category) {
        const sidebar = document.getElementById('categorySidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const content = document.getElementById('sidebarContent');

        if (!sidebar || !overlay || !content) return;

        const initials = category.name
            ?.split(' ')
            .map(w => w[0])
            .join('')
            .substring(0, 2)
            .toUpperCase();

        content.innerHTML = `


        <div class="info-group">
            <div class="info-group-title">Category Information</div>

            <div class="info-row">
                <span class="info-label">
                    <i class="bi bi-hash"></i> ID
                </span>
                <span class="info-value">${category.id}</span>
            </div>

            <div class="info-row">
                <span class="info-label">
                    <i class="bi bi-tag"></i> Slug
                </span>
                <span class="info-value">${category.slug}</span>
            </div>
        </div>

        <div class="info-group">
            <div class="info-group-title">Created By</div>

            <div class="info-row">
                <span class="info-label">
                    <i class="bi bi-person"></i> Name
                </span>
                <span class="info-value">${category.created_by?.name || 'N/A'}</span>
            </div>

            <div class="info-row">
                <span class="info-label">
                    <i class="bi bi-envelope"></i> Email
                </span>
                <span class="info-value">${category.created_by?.email || 'N/A'}</span>
            </div>
        </div>

        <div class="info-group">
            <div class="info-group-title">Timestamps</div>

            <div class="info-row">
                <span class="info-label">
                    <i class="bi bi-clock-history"></i> Created At
                </span>
                <span class="info-value">
                    ${category.created_at?.date || ''} ${category.created_at?.time || ''}
                </span>
            </div>

            <div class="info-row">
                <span class="info-label">
                    <i class="bi bi-arrow-repeat"></i> Updated At
                </span>
                <span class="info-value">
                    ${category.updated_at?.date || ''} ${category.updated_at?.time || ''}
                </span>
            </div>

            ${category.deleted_at
                ? `
                    <div class="info-row">
                        <span class="info-label text-danger">
                            <i class="bi bi-trash"></i> Deleted At
                        </span>
                        <span class="info-value text-danger">
                            ${category.deleted_at}
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
        const sidebar = document.getElementById('categorySidebar');
        const overlay = document.getElementById('sidebarOverlay');

        sidebar?.classList.remove('show');
        overlay?.classList.remove('show');
    },

    openModal(category) {
        const modalEl = document.getElementById('editCategoryModal');
        const modal = new bootstrap.Modal(modalEl);

        document.getElementById('editCategoryId').value = category.id;
        document.getElementById('editCategoryName').value = category.name;

        modal.show();
    },

    renderPagination() {
        this.pagination = new Pagination(this.dom.pagination, (url) => this.goPage(url));
        this.pagination.render(this.state.pagination);
    },

    goPage(url) {
        const page = new URL(url).searchParams.get('CategoryDashboard');
        if (!page) return;

        this.setQuery('CategoryDashboard', page);
        this.fetchCategories();
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

window.categoryPageApp = CategoryPage;
document.addEventListener('DOMContentLoaded', () => CategoryPage.init());
