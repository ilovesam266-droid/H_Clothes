const CategoryPage = {
    /* ===================== STATE ===================== */
    state: {
        categories: [],
        categoryShow: null,
        selected: new Set(),
        deleteId: null,

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
                page: this.state.currentPage
            }
        });

        this.state.categories = res.data.data;
        this.state.pagination = res.data.meta;

        this.renderTable();
        this.renderPagination();
        this.updateBulkBar();

        this.state.loading = false;
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

    /* ===================== DELETE / RESTORE ===================== */
    async confirmBulkDelete() {
        await axios.post('/api/admin/categories/delete', [...this.state.selected]);
        this.clearSelection();
        this.fetchCategories();
    },

    async confirmBulkRestore() {
        await axios.patch('/api/admin/categories/restore', [...this.state.selected]);
        this.clearSelection();
        this.fetchCategories();
    },

    async confirmDelete() {
        await axios.delete(`/api/admin/categories/${this.state.deleteId}`);
        this.state.deleteId = null;
        this.fetchCategories();
    },

    openDeleteModal(id) {
        this.state.deleteId = id;
        new bootstrap.Modal(document.getElementById('confirmDeleteModal')).show();
    },

    /* ===================== TAB ===================== */
    switchTab(tab) {
        this.state.currentTab = tab;
        this.clearSelection();
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
                    <a href="${window.routes.categoryEdit}/${c.id}/edit"
                        class="btn btn-warning btn-action">
                        <i class="bx bx-edit"></i>
                    </a>
                    <button class="btn btn-danger btn-action"
                        onclick="categoryPageApp.openDeleteModal(${c.id})">
                        <i class="bx bx-trash"></i>
                    </button>
                </td>
            </tr>`;
        });

        this.updateSelectAllCheckbox();
    },

    renderPagination() {
        const meta = this.state.pagination;
        if (!meta?.links) return;

        this.dom.pagination.innerHTML = meta.links.map(l => `
            <li class="page-item ${l.active ? 'active' : ''} ${!l.url ? 'disabled' : ''}">
                <a class="page-link"
                    href="javascript:void(0)"
                    onclick="${l.url ? `categoryPageApp.goPage('${l.url}')` : ''}">
                    ${l.label}
                </a>
            </li>
        `).join('');
    },

    goPage(url) {
        const page = new URL(url).searchParams.get('page');
        this.state.currentPage = page;
        this.fetchCategories();
    },

    /* ===================== UTIL ===================== */
    debounce(fn, delay) {
        let t;
        return (...args) => {
            clearTimeout(t);
            t = setTimeout(() => fn.apply(this, args), delay);
        };
    }
};

window.categoryPageApp = CategoryPage;
document.addEventListener('DOMContentLoaded', () => CategoryPage.init());
