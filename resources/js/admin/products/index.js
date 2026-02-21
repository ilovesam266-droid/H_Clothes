const ProductPage = {
    state: {
        products: [],
        currentPage: 1,
        perPage: 10,
        search: '',
        statusFilter: '',
        creatorFilter: '',
        stockFilter: '',
        currentTab: 'active',
        selectedProducts: new Set(),
        currentProductId: null,
        currentVariantId: null,
        currentExpandedProduct: null,
        loading: false,
    },

    init() {
        this.bindEvents();
        this.fetchProducts();
    },

    bindEvents() {
        const searchInput = document.getElementById('searchInput');
        const statusFilter = document.getElementById('statusFilter');
        const creatorFilter = document.getElementById('creatorFilter');
        const stockFilter = document.getElementById('stockFilter');
        const perPage = document.getElementById('perPage');

        // debounce cho search
        this.debounceSearch = this.debounce((e) => {
            this.state.search = e.target.value;
            this.state.currentPage = 1;
            this.fetchProducts();
        }, 400);

        if (searchInput) {
            searchInput.addEventListener('input', this.debounceSearch);
        }

        if (statusFilter) {
            statusFilter.addEventListener('change', (e) => {
                this.state.statusFilter = e.target.value || '';
                this.state.currentPage = 1;
                this.fetchProducts();
            });
        }

        if (creatorFilter) {
            creatorFilter.addEventListener('change', (e) => {
                this.state.creatorFilter = e.target.value || '';
                this.state.currentPage = 1;
                this.fetchProducts();
            });
        }

        if (stockFilter) {
            stockFilter.addEventListener('change', (e) => {
                this.state.stockFilter = e.target.value || '';
                this.state.currentPage = 1;
                this.fetchProducts();
            });
        }

        if (perPage) {
            perPage.addEventListener('change', (e) => {
                this.state.perPage = parseInt(e.target.value, 10);
                this.state.currentPage = 1;
                this.fetchProducts();
            });
        }
    },


    async fetchProducts() {
        this.state.loading = true;

        const response = await window.axios.get('/api/admin/products', {
            params: {
                trashed: this.state.currentTab,
                search: this.state.search,
                perPage: this.state.perPage,
                filters: {
                    status: this.state.statusFilter,
                },
                ...this.getQueryParams(),
            }
        });

        this.state.products = response.data.data;
        this.state.pagination = response.data.meta;

        this.renderProducts();
        this.renderPagination();
        this.state.loading = false;
    },

    renderProducts() {
        const tbody = document.getElementById('productTableBody');

        if (this.state.products.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="10" class="text-center py-4">
                        <i class="bx bx-package" style="font-size: 48px; color: #ccc;"></i>
                        <p class="text-muted mt-2">No products found</p>
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = this.state.products.map((product, index) => `
            <tr class="${this.state.currentExpandedProduct === product.id ? 'table-active' : ''}">
                <td>
                    <input type="checkbox"
                           class="checkbox-custom product-checkbox"
                           data-id="${product.id}"
                           ${this.state.selectedProducts.has(product.id) ? 'checked' : ''}
                           onchange="productPageApp.toggleSelectProduct(${product.id})">
                </td>
                <td>${(this.state.currentPage - 1) * this.state.perPage + index + 1}</td>
                <td>
                    <div class="d-flex align-items-center">
                        <img src="${product.images?.length ? '/' + product.images[0].url : '/images/placeholder.png'}"
                            class="product-thumb me-2"
                            alt="${product.name}">

                        <div>
                            <div class="fw-bold">${product.name}</div>
                            <small class="text-muted">${product.slug}</small>
                        </div>
                    </div>
                </td>
                <td><span class="badge bg-secondary">${product.total_variants || 0} SKUs</span></td>
                <td>
                    <div class="stock-info">
                        <div class="fw-bold">${product.total_stock || 0}</div>
                        <small class="text-muted">Sold: ${product.total_sold || 0}</small>
                    </div>
                </td>
                <td>
                    ${product.min_price && product.max_price ?
                `<div class="price-range">
                            <div>${product.min_price}</div>
                            ${product.min_price !== product.max_price ?
                    `<small class="text-muted">to ${product.max_price}</small>` : ''}
                        </div>` :
                '<span class="text-muted">N/A</span>'}
                </td>
                <td>
                    <span class="badge bg-${product.status ? 'success' : 'secondary'}">
                        ${product.status ? 'Active' : 'Inactive'}
                    </span>
                </td>
                <td>
                    <div class="creator-info">
                        <div class="fw-bold">${product.creator?.name || 'N/A'}</div>
                        <small class="text-muted">${product.creator?.email || ''}</small>
                    </div>
                </td>
                <td>
                    <div class="date-info">
                        <div>${product.created_at.date}</div>
                        <small class="text-muted">${product.created_at.time}</small>
                    </div>
                </td>
                <td class="text-center">
                    <div class="btn-group btn-group-sm">
                        ${this.state.currentTab === 'active' ? `
                            <button class="btn btn-info"
                                    onclick="productPageApp.viewProduct(${product.id})"
                                    title="View Details">
                                <i class='bx bx-scan'></i>
                            </button>

                            <button class="btn btn-warning"
                                    onclick="productPageApp.editProduct(${product.id})"
                                    title="Edit">
                                <i class="bx bx-edit"></i>
                            </button>
                            <button class="btn btn-danger"
                                    onclick="productPageApp.deleteProduct(${product.id})"
                                    title="Delete">
                                <i class="bx bx-trash"></i>
                            </button>
                        ` : `
                            <button class="btn btn-success"
                                    onclick="productPageApp.restoreProduct(${product.id})"
                                    title="Restore">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </button>
                            <button class="btn btn-danger"
                                    onclick="productPageApp.forceDeleteProduct(${product.id})"
                                    title="Delete Permanently">
                                <i class="bi bi-trash-fill"></i>
                            </button>
                        `}
                    </div>
                </td>
            </tr>
        `).join('');
    },

    async confirmDelete() {
        try {
            const response = await fetch(`${window.routes.productDelete}/${this.currentProductId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();

            if (data.success) {
                this.showToast('Product deleted successfully', 'success');
                this.loadProducts();
                bootstrap.Modal.getInstance(document.getElementById('confirmDeleteModal')).hide();
            } else {
                this.showToast(data.message || 'Error deleting product', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            this.showToast('Error deleting product', 'error');
        }
    },

    async restoreProduct(id) {
        try {
            const response = await fetch(`${window.routes.productRestore}/${id}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();

            if (data.success) {
                this.showToast('Product restored successfully', 'success');
                this.loadProducts();
            }
        } catch (error) {
            console.error('Error:', error);
            this.showToast('Error restoring product', 'error');
        }
    },

    updateBulkActionBar() {
        const bar = document.getElementById('bulkActionsBar');
        const count = this.state.selectedProducts.size;
        const countSpan = document.getElementById('selectedCount');
        const deleteBtn = document.getElementById('bulkDeleteBtn');
        const restoreBtn = document.getElementById('bulkRestoreBtn');

        countSpan.textContent = count;

        if (count > 0) {
            bar.classList.add('show');
            if (this.state.currentTab === 'active') {
                deleteBtn.style.display = 'inline-block';
                restoreBtn.style.display = 'none';
            } else {
                deleteBtn.style.display = 'none';
                restoreBtn.style.display = 'inline-block';
            }
        } else {
            bar.classList.remove('show');
        }

        this.updateSelectAllCheckbox();
    },

    toggleSelectAll() {
        const selectAllCheckbox = document.getElementById('selectAll');
        const currentData = this.state.products;

        if (selectAllCheckbox.checked) {
            currentData.forEach(product => this.state.selectedProducts.add(product.id));
        } else {
            currentData.forEach(product => this.state.selectedProducts.delete(product.id));
        }

        this.updateBulkActionBar();
        this.updateSelectAllCheckbox();
        this.renderProducts();
    },

    toggleSelectProduct(productId) {
        if (this.state.selectedProducts.has(productId)) {
            this.state.selectedProducts.delete(productId);
        } else {
            this.state.selectedProducts.add(productId);
        }

        this.updateBulkActionBar();
        this.updateSelectAllCheckbox();
    },

    updateSelectAllCheckbox() {
        const selectAllCheckbox = document.getElementById('selectAll');
        const currentData = this.state.products;
        const allSelected = currentData.length > 0 && currentData.every(user => this.state.selectedProducts.has(user.id));

        selectAllCheckbox.checked = allSelected;
    },

    // Bulk Operations
    openBulkDeleteModal() {
        document.getElementById('bulkDeleteCount').textContent = this.state.selectedProducts.size;
        const modal = new bootstrap.Modal(document.getElementById('confirmBulkDeleteModal'));
        modal.show();
    },

    openBulkRestoreModal() {
        document.getElementById('bulkRestoreCount').textContent = this.state.selectedProducts.size;
        const modal = new bootstrap.Modal(document.getElementById('confirmBulkRestoreModal'));
        modal.show();
    },

    async confirmBulkDelete() {
        try {
            const el = document.getElementById('confirmBulkDeleteModal');
            const modal = el ? bootstrap.Modal.getInstance(el) : null;

            if (modal) modal.hide();

            await axios.post('/api/admin/products',
                Array.from(this.state.selectedProducts)
            );

            this.state.selectedProducts.clear();
            this.updateBulkActionBar();

            this.fetchProducts();
        } catch (e) {
            alert('Bulk delete failed');
            console.error(e);
        }
    },


    async confirmBulkRestore() {
        try {
            const el = document.getElementById('confirmBulkRestoreModal');
            const modal = el ? bootstrap.Modal.getInstance(el) : null;

            if (modal) modal.hide();

            await axios.patch('/api/admin/products/restore', Array.from(this.state.selectedProducts));

            this.state.selectedProducts.clear();
            this.updateBulkActionBar();

            this.fetchProducts();
        } catch (e) {
            alert('Bulk delete failed');
            console.error(e);
        }
    },

    //Component: Switch tab
    switchTab(tab) {
        this.state.currentTab = tab;
        this.state.selectedProducts.clear();
        this.state.currentPage = 1;

        document.querySelectorAll('.nav-tabs-custom .nav-link').forEach(link => {
            link.classList.remove('active');
        });
        event.target.classList.add('active');

        const cleanUrl = window.location.origin + window.location.pathname;
        history.replaceState({}, '', cleanUrl);

        this.fetchProducts();
        this.renderProducts();
        this.renderPagination();
    },

    renderPagination() {
        const container = document.getElementById('pagination');
        if (!container) return;

        const meta = this.state.pagination;
        if (!meta || !meta.links) {
            container.innerHTML = '';
            return;
        }

        container.innerHTML = '';

        meta.links.forEach(link => {
            const li = document.createElement('li');
            li.classList.add('page-item');

            if (link.active) li.classList.add('active');
            if (!link.url) li.classList.add('disabled');

            const a = document.createElement('a');
            a.classList.add('page-link');
            a.href = 'javascript:void(0)';
            a.innerHTML = link.label;

            if (link.url) {
                a.addEventListener('click', () => {
                    const url = new URL(link.url);
                    const page = url.searchParams.get('ProductDashboard');

                    if (!page) return;

                    // cập nhật query string
                    this.setQuery('ProductDashboard', page);

                    // fetch lại data
                    this.fetchProducts();
                });
            }

            li.appendChild(a);
            container.appendChild(li);
        });
    },

    getQueryParams() {
        return Object.fromEntries(new URLSearchParams(location.search));
    },

    setQuery(key, value) {
        const params = new URLSearchParams(location.search);
        value ? params.set(key, value) : params.delete(key);
        history.replaceState(null, '', '?' + params.toString());
    },

    debounce(fn, delay) {
        let t;
        return (...args) => {
            clearTimeout(t);
            t = setTimeout(() => fn.apply(this, args), delay);
        };
    }
};

window.productPageApp = ProductPage;
document.addEventListener('DOMContentLoaded', () => ProductPage.init());
