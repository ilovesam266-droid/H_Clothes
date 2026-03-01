import Pagination from "../pagination.js";

const CategoryPicker = {

    state: {
        categories: [],
        selected: new Set(),

        pagination: {},
        currentPage: 1,
        perPage: 10,

        loading: false,
        paginationInstance: null,

        categoryMap: new Map(), // ⭐ chống mất badge khi đổi page
    },

    dom: {},
    modalInstance: null,

    /* ================= INIT ================= */

    init(config = {}) {

        this.dom = {
            modal: document.getElementById(config.modalId ?? 'categoryModal'),
            list: document.getElementById(config.listId ?? 'categoryList'),
            loading: document.getElementById(config.loadingId ?? 'categoryLoading'),
            pagination: document.getElementById(config.paginationId ?? 'categoryPagination'),
            container: document.getElementById(config.containerId ?? 'categoriesContainer'),
            input: document.getElementById(config.inputId ?? 'categoryIds')
        };

        if (!this.dom.modal) return;

        this.modalInstance = new bootstrap.Modal(this.dom.modal);

        // ⭐ Pagination tạo đúng 1 lần
        if (this.dom.pagination) {
            this.state.paginationInstance = new Pagination(
                this.dom.pagination,
                (url) => this.handlePageUrl(url)
            );
        }
    },

    /* ================= OPEN ================= */

    async open() {
        await this.fetchCategories();
        this.modalInstance.show();
    },

    /* ================= FETCH ================= */

    async fetchCategories(page = this.state.currentPage) {

        if (this.state.loading) return;

        this.state.loading = true;

        try {
            this.dom.loading?.classList.remove('d-none');

            const res = await axios.get('/api/admin/categories', {
                params: {
                    page,
                    perPage: this.state.perPage
                },
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
                }
            });

            const rows = res.data.data || [];

            this.state.categories = rows;
            this.state.pagination = res.data.meta || {};
            this.state.currentPage = Number(page);

            // ⭐ cache để badge không mất tên
            rows.forEach(cat => {
                this.state.categoryMap.set(cat.id, cat);
            });

            this.renderList();
            this.state.paginationInstance?.render(this.state.pagination);

        } catch (e) {
            console.error(e);
            alert('Cannot load categories');
        } finally {
            this.dom.loading?.classList.add('d-none');
            this.state.loading = false;
        }
    },

    handlePageUrl(url) {
        if (!url) return;

        const page = new URL(url).searchParams.get('page');
        if (!page) return;

        this.fetchCategories(page);
    },

    /* ================= RENDER ================= */

    renderList() {

        if (!this.dom.list) return;

        this.dom.list.innerHTML = '';

        this.state.categories.forEach(cat => {

            const item = document.createElement('label');
            item.className = 'list-group-item d-flex align-items-center gap-2';

            const checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.className = 'form-check-input';
            checkbox.checked = this.state.selected.has(cat.id);

            checkbox.onchange = () => this.toggle(cat.id);

            const text = document.createElement('span');
            text.textContent = cat.name;

            item.appendChild(checkbox);
            item.appendChild(text);

            this.dom.list.appendChild(item);
        });
    },

    /* ================= SELECT ================= */

    toggle(id) {
        this.state.selected.has(id)
            ? this.state.selected.delete(id)
            : this.state.selected.add(id);
    },

    /* ================= CONFIRM ================= */

    confirm() {
        this.renderBadges();
        this.updateInput();
        this.modalInstance.hide();
    },

    renderBadges() {

        if (!this.dom.container) return;

        if (!this.state.selected.size) {
            this.dom.container.innerHTML =
                `<span class="text-muted">Click để chọn category</span>`;
            return;
        }

        const html = [...this.state.selected]
            .map(id => {
                const cat = this.state.categoryMap.get(id);
                return cat
                    ? `<span class="badge bg-primary me-1">${cat.name}</span>`
                    : '';
            })
            .join('');

        this.dom.container.innerHTML = html;
    },

    updateInput() {
        if (!this.dom.input) return;

        this.dom.input.innerHTML = '';
        // this.dom.input.value = [...this.state.selected];
        this.state.selected.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'categories[]';
            input.value = id;

            this.dom.input.appendChild(input);
        });
    }
};

export default CategoryPicker;
