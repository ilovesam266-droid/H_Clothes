// const OrderPage = {
//     state: {
//         orders: [],
//         currentOrder: null,
//         statusFilter: '',
//         search: '',
//         perPage: 10,
//         currentPage: 1,
//         pagination: {},
//         loading: false,
//         updateId: null,
//     },

//     dom: {},

//     init() {
//         this.cacheDom();
//         this.bindEvents();
//         this.fetchOrders();
//     },

//     cacheDom() {
//         this.dom = {
//             searchInput: document.getElementById('searchInput'),
//             statusFilter: document.getElementById('statusFilter'),
//             perPage: document.getElementById('perPage'),
//             tbody: document.getElementById('orderTableBody'),
//             pagination: document.getElementById('pagination'),
//             updateStatusModal: document.getElementById('updateStatusModal'),
//             newStatusSelect: document.getElementById('newStatus'),
//             statusForm: document.getElementById('updateStatusForm'),
//             cancelReasonWrapper: document.getElementById('cancelReasonWrapper'),
//         };
//     },

//     bindEvents() {
//         this.dom.searchInput?.addEventListener('input', this.debounce(e => {
//             this.state.search = e.target.value;
//             this.state.currentPage = 1;
//             this.fetchOrders();
//         }, 400));

//         this.dom.statusFilter?.addEventListener('change', e => {
//             this.state.statusFilter = e.target.value;
//             this.state.currentPage = 1;
//             this.fetchOrders();
//         });

//         this.dom.perPage?.addEventListener('change', e => {
//             this.state.perPage = e.target.value;
//             this.state.currentPage = 1;
//             this.fetchOrders();
//         });

//         this.dom.newStatusSelect?.addEventListener('change', e => {
//             const val = e.target.value;
//             if (val === '5' || val === '6') {
//                 this.dom.cancelReasonWrapper.classList.remove('d-none');
//             } else {
//                 this.dom.cancelReasonWrapper.classList.add('d-none');
//             }
//         });
//     },

//     async fetchOrders() {
//         this.state.loading = true;
//         this.renderLoading();

//         try {
//             const token = localStorage.getItem('auth_token');
//             const response = await axios.get(window.routes.apiOrders, {
//                 headers: { Authorization: `Bearer ${token}` },
//                 params: {
//                     search: this.state.search,
//                     status: this.state.statusFilter,
//                     perPage: this.state.perPage,
//                     page: this.state.currentPage,
//                 }
//             });

//             this.state.orders = response.data.data;
//             this.state.pagination = response.data.meta;
//             this.renderTable();
//             this.renderPagination();
//         } catch (e) {
//             console.error('Fetch orders failed', e);
//             alert('Failed to load orders');
//         } finally {
//             this.state.loading = false;
//         }
//     },

//     renderTable() {
//         const tbody = this.dom.tbody;
//         if (!tbody) return;

//         if (this.state.orders.length === 0) {
//             tbody.innerHTML = '<tr><td colspan="7" class="text-center py-4">No orders found.</td></tr>';
//             return;
//         }

//         tbody.innerHTML = this.state.orders.map((order, index) => `
//             <tr>
//                 <td>${(this.state.pagination.current_page - 1) * this.state.perPage + index + 1}</td>
//                 <td><span class="order-id">#${order.id}</span></td>
//                 <td>
//                     <div class="customer-info">
//                         <span class="customer-name">${order.user?.full_name || 'Guest'}</span>
//                         <span class="customer-email">${order.user?.email || ''}</span>
//                     </div>
//                 </td>
//                 <td><span class="order-amount">${new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(order.total_amount)}</span></td>
//                 <td><span class="badge bg-${order.status.color}">${order.status.name}</span></td>
//                 <td>${order.created_at}</td>
//                 <td class="text-center">
//                     <div class="btn-group btn-group-sm">
//                         <button class="btn btn-info" onclick="orderPageApp.openOrderSidebar(${order.id})" title="View Details">
//                             <i class='bx bx-show'></i>
//                         </button>
//                         <button class="btn btn-primary" onclick="orderPageApp.openUpdateStatusModal(${order.id}, ${order.status.value})" title="Update Status">
//                             <i class='bx bx-edit-alt'></i>
//                         </button>
//                         <a href="${window.routes.orderShow}/${order.id}" class="btn btn-outline-secondary" title="Full Page View">
//                             <i class='bx bx-link-external'></i>
//                         </a>
//                     </div>
//                 </td>
//             </tr>
//         `).join('');
//     },

//     renderLoading() {
//         this.dom.tbody.innerHTML = '<tr><td colspan="7" class="text-center py-4"><div class="spinner-border text-primary" role="status"></div></td></tr>';
//     },

//     renderPagination() {
//         const container = this.dom.pagination;
//         if (!container || !this.state.pagination.links) return;

//         container.innerHTML = this.state.pagination.links.map(link => `
//             <li class="page-item ${link.active ? 'active' : ''} ${!link.url ? 'disabled' : ''}">
//                 <a class="page-link" href="javascript:void(0)" onclick="${link.url ? `orderPageApp.goToPage('${link.url}')` : ''}">
//                     ${link.label}
//                 </a>
//             </li>
//         `).join('');
//     },

//     goToPage(url) {
//         const urlParams = new URL(url).searchParams;
//         this.state.currentPage = urlParams.get('page');
//         this.fetchOrders();
//     },

//     switchTab(status, el) {
//         this.state.statusFilter = status;
//         this.state.currentPage = 1;

//         document.querySelectorAll('.nav-link').forEach(link => link.classList.remove('active'));
//         el.classList.add('active');

//         if (this.dom.statusFilter) {
//             this.dom.statusFilter.value = status;
//         }

//         this.fetchOrders();
//     },

//     async openOrderSidebar(id) {
//         try {
//             const token = localStorage.getItem('auth_token');
//             const response = await axios.get(`${window.routes.orderShow}/${id}`, {
//                 headers: {
//                     'Accept': 'application/json',
//                     'Authorization': `Bearer ${token}`
//                 }
//             });
//             const order = response.data.data;
//             this.renderSidebar(order);
//         } catch (e) {
//             console.error('Load order detail failed', e);
//             alert('Failed to load order details');
//         }
//     },

//     renderSidebar(order) {
//         const sidebar = document.getElementById('orderSidebar');
//         const overlay = document.getElementById('sidebarOverlay');
//         const content = document.getElementById('sidebarContent');

//         if (!sidebar || !content) return;

//         content.innerHTML = `
//             <div class="mb-4">
//                 <div class="order-section-title">Order Info</div>
//                 <div class="row mb-2">
//                     <div class="col-6 text-muted">Order ID:</div>
//                     <div class="col-6 fw-bold">#${order.id}</div>
//                 </div>
//                 <div class="row mb-2">
//                     <div class="col-6 text-muted">Status:</div>
//                     <div class="col-6"><span class="badge bg-${order.status.color}">${order.status.name}</span></div>
//                 </div>
//                 <div class="row mb-2">
//                     <div class="col-6 text-muted">Date:</div>
//                     <div class="col-6">${order.created_at}</div>
//                 </div>
//             </div>

//             <div class="mb-4">
//                 <div class="order-section-title">Customer Info</div>
//                 <p class="mb-1"><strong>Name:</strong> ${order.address?.recipient_name || order.user?.full_name}</p>
//                 <p class="mb-1"><strong>Phone:</strong> ${order.address?.recipient_phone || 'N/A'}</p>
//                 <p class="mb-1"><strong>Email:</strong> ${order.user?.email}</p>
//                 <p class="mb-0"><strong>Address:</strong> ${order.address?.full_address}</p>
//             </div>

//             <div class="mb-4">
//                 <div class="order-section-title">Items</div>
//                 <div class="order-sidebar-items">
//                     ${order.items.map(item => `
//                         <div class="order-item-row">
//                             <div class="order-item-info">
//                                 <span class="order-item-name">${item.product_name}</span>
//                                 <span class="fw-bold">${new Intl.NumberFormat('vi-VN').format(item.unit_price)} x ${item.quantity}</span>
//                             </div>
//                             <div class="order-item-meta">${item.variant_name}</div>
//                         </div>
//                     `).join('')}
//                 </div>
//                 <div class="d-flex justify-content-between mt-3 pt-2 border-top">
//                     <span class="fw-bold">Total:</span>
//                     <span class="fw-bold text-primary fs-5">${new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(order.total_amount)}</span>
//                 </div>
//             </div>

//             ${order.customer_note ? `
//                 <div class="mb-4">
//                     <div class="order-section-title">Customer Note</div>
//                     <div class="bg-light p-2 rounded small font-italic">${order.customer_note}</div>
//                 </div>
//             ` : ''}

//             ${order.admin_note ? `
//                 <div class="mb-4">
//                     <div class="order-section-title">Admin Note</div>
//                     <div class="bg-label-info p-2 rounded small">${order.admin_note}</div>
//                 </div>
//             ` : ''}

//             ${order.cancel_reason ? `
//                 <div class="mb-4 text-danger">
//                     <div class="order-section-title text-danger">Cancel Reason</div>
//                     <div class="bg-label-danger p-2 rounded small">${order.cancel_reason}</div>
//                 </div>
//             ` : ''}
//         `;

//         sidebar.classList.add('show');
//         overlay.classList.add('show');
//     },

//     closeSidebar() {
//         document.getElementById('orderSidebar')?.classList.remove('show');
//         document.getElementById('sidebarOverlay')?.classList.remove('show');
//     },

//     openUpdateStatusModal(id, currentStatus) {
//         this.state.updateId = id;
//         this.dom.newStatusSelect.value = currentStatus;
//         document.getElementById('adminNote').value = '';
//         document.getElementById('cancelReason').value = '';

//         if (currentStatus === 5 || currentStatus === 6) {
//             this.dom.cancelReasonWrapper.classList.remove('d-none');
//         } else {
//             this.dom.cancelReasonWrapper.classList.add('d-none');
//         }

//         const modal = new bootstrap.Modal(this.dom.updateStatusModal);
//         modal.show();
//     },

//     async confirmUpdateStatus() {
//         const id = this.state.updateId;
//         const status = this.dom.newStatusSelect.value;
//         const adminNote = document.getElementById('adminNote').value;
//         const cancelReason = document.getElementById('cancelReason').value;

//         try {
//             const token = localStorage.getItem('auth_token');
//             const response = await axios.patch(`${window.routes.apiOrders}/${id}/status`, {
//                 status: status,
//                 admin_note: adminNote,
//                 cancel_reason: cancelReason
//             }, {
//                 headers: {
//                     'Accept': 'application/json',
//                     'Authorization': `Bearer ${token}`
//                 }
//             });

//             bootstrap.Modal.getInstance(this.dom.updateStatusModal).hide();
//             this.fetchOrders();
//             // Show toast if available in globals, or alert
//             if (window.Toast) {
//                 window.Toast.success('Order status updated successfully');
//             } else {
//                 alert('Order status updated successfully');
//             }
//         } catch (e) {
//             console.error('Update status failed', e);
//             alert(e.response?.data?.message || 'Failed to update status');
//         }
//     },

//     debounce(fn, delay) {
//         let t;
//         return (...args) => {
//             clearTimeout(t);
//             t = setTimeout(() => fn.apply(this, args), delay);
//         };
//     }
// };

// window.orderPageApp = OrderPage;
// document.addEventListener('DOMContentLoaded', () => OrderPage.init());
const OrderPage = {
    state: {
        orders: [],
        currentOrder: null,
        statusFilter: '',
        search: '',
        perPage: 10,
        currentPage: 1,
        pagination: {},
        loading: false,
        updateId: null,
        selectedOrders: new Set(),
    },

    dom: {},

    init() {
        this.cacheDom();
        this.bindEvents();
        this.fetchOrders();
    },

    cacheDom() {
        this.dom = {
            searchInput: document.getElementById('searchInput'),
            statusFilter: document.getElementById('statusFilter'),
            perPage: document.getElementById('perPage'),
            tbody: document.getElementById('orderTableBody'),
            pagination: document.getElementById('pagination'),
            updateStatusModal: document.getElementById('updateStatusModal'),
            newStatusSelect: document.getElementById('newStatus'),
            statusForm: document.getElementById('updateStatusForm'),
            cancelReasonWrapper: document.getElementById('cancelReasonWrapper'),
            selectAll: document.getElementById('selectAll'),
            bulkActionsBar: document.getElementById('bulkActionsBar'),
            selectedCount: document.getElementById('selectedCount'),
            bulkDeleteBtn: document.getElementById('bulkDeleteBtn'),
        };
    },

    bindEvents() {
        this.dom.searchInput?.addEventListener('input', this.debounce(e => {
            this.state.search = e.target.value;
            this.state.currentPage = 1;
            this.fetchOrders();
        }, 400));

        this.dom.statusFilter?.addEventListener('change', e => {
            this.state.statusFilter = e.target.value;
            this.state.currentPage = 1;
            this.fetchOrders();
        });

        this.dom.perPage?.addEventListener('change', e => {
            this.state.perPage = e.target.value;
            this.state.currentPage = 1;
            this.fetchOrders();
        });

        this.dom.newStatusSelect?.addEventListener('change', e => {
            const val = e.target.value;
            this.dom.cancelReasonWrapper.classList.toggle('d-none', val !== '5' && val !== '6');
        });

        this.dom.selectAll?.addEventListener('change', () => this.toggleSelectAll());
    },

    async fetchOrders() {
        this.state.loading = true;
        this.renderLoading();

        try {
            const token = localStorage.getItem('auth_token');
            const response = await axios.get(window.routes.apiOrders, {
                headers: { Authorization: `Bearer ${token}` },
                params: {
                    search: this.state.search,
                    status: this.state.statusFilter,
                    perPage: this.state.perPage,
                    page: this.state.currentPage,
                }
            });

            this.state.orders = response.data.data;
            this.state.pagination = response.data.meta;
            this.renderTable();
            this.renderPagination();
        } catch (e) {
            console.error('Fetch orders failed', e);
            alert('Failed to load orders');
        } finally {
            this.state.loading = false;
        }
    },

    renderTable() {
        const tbody = this.dom.tbody;
        if (!tbody) return;

        if (this.state.orders.length === 0) {
            tbody.innerHTML = '<tr><td colspan="8" class="text-center py-4">No orders found.</td></tr>';
            return;
        }

        tbody.innerHTML = this.state.orders.map((order, index) => `
            <tr class="${this.state.selectedOrders.has(order.id) ? 'table-active' : ''}">
                <td>
                    <input type="checkbox"
                           class="checkbox-custom order-checkbox"
                           data-id="${order.id}"
                           ${this.state.selectedOrders.has(order.id) ? 'checked' : ''}
                           onchange="orderPageApp.toggleSelectOrder(${order.id})">
                </td>
                <td>${(this.state.pagination.current_page - 1) * this.state.perPage + index + 1}</td>
                <td><span class="order-id">#${order.id}</span></td>
                <td>
                    <div class="customer-info">
                        <span class="customer-name">${order.user?.full_name || 'Guest'}</span>
                        <span class="customer-email">${order.user?.email || ''}</span>
                    </div>
                </td>
                <td><span class="order-amount">${new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(order.total_amount)}</span></td>
                <td><span class="badge bg-${order.status.color}">${order.status.name}</span></td>
                <td>${order.created_at}</td>
                <td class="text-center">
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-info" onclick="orderPageApp.openOrderSidebar(${order.id})" title="View Details">
                            <i class='bx bx-show'></i>
                        </button>
                        <button class="btn btn-primary" onclick="orderPageApp.openUpdateStatusModal(${order.id}, ${order.status.value})" title="Update Status">
                            <i class='bx bx-edit-alt'></i>
                        </button>
                        <a href="${window.routes.orderShow}/${order.id}" class="btn btn-outline-secondary" title="Full Page View">
                            <i class='bx bx-link-external'></i>
                        </a>
                    </div>
                </td>
            </tr>
        `).join('');

        this.updateSelectAllCheckbox();
        this.updateBulkActionBar();
    },

    renderLoading() {
        this.dom.tbody.innerHTML = '<tr><td colspan="8" class="text-center py-4"><div class="spinner-border text-primary" role="status"></div></td></tr>';
    },

    renderPagination() {
        const container = this.dom.pagination;
        if (!container || !this.state.pagination.links) return;

        container.innerHTML = this.state.pagination.links.map(link => `
            <li class="page-item ${link.active ? 'active' : ''} ${!link.url ? 'disabled' : ''}">
                <a class="page-link" href="javascript:void(0)" onclick="${link.url ? `orderPageApp.goToPage('${link.url}')` : ''}">
                    ${link.label}
                </a>
            </li>
        `).join('');
    },

    goToPage(url) {
        const urlParams = new URL(url).searchParams;
        this.state.currentPage = urlParams.get('page');
        this.fetchOrders();
    },

    switchTab(status, el) {
        this.state.statusFilter = status;
        this.state.currentPage = 1;

        document.querySelectorAll('.nav-link').forEach(link => link.classList.remove('active'));
        el.classList.add('active');

        if (this.dom.statusFilter) {
            this.dom.statusFilter.value = status;
        }

        this.fetchOrders();
    },

    // ─── Bulk Selection ────────────────────────────────────────────────────────

    toggleSelectOrder(orderId) {
        if (this.state.selectedOrders.has(orderId)) {
            this.state.selectedOrders.delete(orderId);
        } else {
            this.state.selectedOrders.add(orderId);
        }

        this.updateBulkActionBar();
        this.updateSelectAllCheckbox();
    },

    toggleSelectAll() {
        const allChecked = this.dom.selectAll?.checked;

        this.state.orders.forEach(order => {
            if (allChecked) {
                this.state.selectedOrders.add(order.id);
            } else {
                this.state.selectedOrders.delete(order.id);
            }
        });

        this.renderTable();
    },

    updateSelectAllCheckbox() {
        if (!this.dom.selectAll) return;
        const orders = this.state.orders;
        this.dom.selectAll.checked = orders.length > 0 && orders.every(o => this.state.selectedOrders.has(o.id));
        this.dom.selectAll.indeterminate = !this.dom.selectAll.checked && orders.some(o => this.state.selectedOrders.has(o.id));
    },

    updateBulkActionBar() {
        const { bulkActionsBar, selectedCount, bulkDeleteBtn } = this.dom;
        const count = this.state.selectedOrders.size;

        if (!bulkActionsBar) return;

        if (selectedCount) selectedCount.textContent = count;

        bulkActionsBar.classList.toggle('show', count > 0);

        if (bulkDeleteBtn) {
            bulkDeleteBtn.style.display = count > 0 ? 'inline-block' : 'none';
        }
    },

    clearSelection() {
        this.state.selectedOrders.clear();
        this.updateBulkActionBar();
        this.renderTable();
    },

    // ─── Order Sidebar ─────────────────────────────────────────────────────────

    async openOrderSidebar(id) {
        try {
            const token = localStorage.getItem('auth_token');
            const response = await axios.get(`${window.routes.apiOrders}/${id}`, {
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
            });
            const order = response.data.data;

            this.renderSidebar(order);
        } catch (e) {
            console.error('Load order detail failed', e);
            alert('Failed to load order details');
        }
    },

    renderSidebar(order) {
        const sidebar = document.getElementById('orderSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const content = document.getElementById('sidebarContent');

        if (!sidebar || !content) return;

        content.innerHTML = `
            <div class="mb-4">
                <div class="order-section-title">Order Info</div>
                <div class="row mb-2">
                    <div class="col-6 text-muted">Order ID:</div>
                    <div class="col-6 fw-bold">#${order.id}</div>
                </div>
                <div class="row mb-2">
                    <div class="col-6 text-muted">Status:</div>
                    <div class="col-6"><span class="badge bg-${order.status.color}">${order.status.name}</span></div>
                </div>
                <div class="row mb-2">
                    <div class="col-6 text-muted">Date:</div>
                    <div class="col-6">${order.created_at}</div>
                </div>
            </div>

            <div class="mb-4">
                <div class="order-section-title">Customer Info</div>
                <p class="mb-1"><strong>Name:</strong> ${order.address?.recipient_name || order.user?.full_name}</p>
                <p class="mb-1"><strong>Phone:</strong> ${order.address?.recipient_phone || 'N/A'}</p>
                <p class="mb-1"><strong>Email:</strong> ${order.user?.email}</p>
                <p class="mb-0"><strong>Address:</strong> ${order.address?.full_address}</p>
            </div>

            <div class="mb-4">
                <div class="order-section-title">Items</div>
                <div class="order-sidebar-items">
                    ${order.items.map(item => `
                        <div class="order-item-row">
                            <div class="order-item-info">
                                <span class="order-item-name">${item.product_name}</span>
                                <span class="fw-bold">${new Intl.NumberFormat('vi-VN').format(item.unit_price)} x ${item.quantity}</span>
                            </div>
                            <div class="order-item-meta">${item.variant_name}</div>
                        </div>
                    `).join('')}
                </div>
                <div class="d-flex justify-content-between mt-3 pt-2 border-top">
                    <span class="fw-bold">Total:</span>
                    <span class="fw-bold text-primary fs-5">${new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(order.total_amount)}</span>
                </div>
            </div>

            ${order.customer_note ? `
                <div class="mb-4">
                    <div class="order-section-title">Customer Note</div>
                    <div class="bg-light p-2 rounded small font-italic">${order.customer_note}</div>
                </div>
            ` : ''}

            ${order.admin_note ? `
                <div class="mb-4">
                    <div class="order-section-title">Admin Note</div>
                    <div class="bg-label-info p-2 rounded small">${order.admin_note}</div>
                </div>
            ` : ''}

            ${order.cancel_reason ? `
                <div class="mb-4 text-danger">
                    <div class="order-section-title text-danger">Cancel Reason</div>
                    <div class="bg-label-danger p-2 rounded small">${order.cancel_reason}</div>
                </div>
            ` : ''}
        `;

        sidebar.classList.add('show');
        overlay.classList.add('show');
    },

    closeSidebar() {
        document.getElementById('orderSidebar')?.classList.remove('show');
        document.getElementById('sidebarOverlay')?.classList.remove('show');
    },

    // ─── Update Status ─────────────────────────────────────────────────────────

    openUpdateStatusModal(id, currentStatus) {
        this.state.updateId = id;
        this.dom.newStatusSelect.value = currentStatus;
        document.getElementById('adminNote').value = '';
        document.getElementById('cancelReason').value = '';

        this.dom.cancelReasonWrapper.classList.toggle('d-none', currentStatus !== 5 && currentStatus !== 6);

        const modal = new bootstrap.Modal(this.dom.updateStatusModal);
        modal.show();
    },

    async confirmUpdateStatus() {
        const id = this.state.updateId;
        const status = this.dom.newStatusSelect.value;
        const adminNote = document.getElementById('adminNote').value;
        const cancelReason = document.getElementById('cancelReason').value;

        try {
            const token = localStorage.getItem('auth_token');
            await axios.patch(`${window.routes.apiOrders}/${id}/status`, {
                status,
                admin_note: adminNote,
                cancel_reason: cancelReason,
            }, {
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
            });

            bootstrap.Modal.getInstance(this.dom.updateStatusModal).hide();
            this.fetchOrders();

            if (window.Toast) {
                window.Toast.success('Order status updated successfully');
            } else {
                alert('Order status updated successfully');
            }
        } catch (e) {
            console.error('Update status failed', e);
            alert(e.response?.data?.message || 'Failed to update status');
        }
    },

    // ─── Helpers ───────────────────────────────────────────────────────────────

    debounce(fn, delay) {
        let t;
        return (...args) => {
            clearTimeout(t);
            t = setTimeout(() => fn.apply(this, args), delay);
        };
    }
};

window.orderPageApp = OrderPage;
document.addEventListener('DOMContentLoaded', () => OrderPage.init());
