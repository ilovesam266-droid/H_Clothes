const UserPage = {
    /* ===================== STATE ===================== */
    state: {
        users: [],
        userShow: null,
        deletedUsers: [],
        idUserDelete: null,

        currentTab: 'active',
        userStatus: '',
        userRole: '',
        userVerified: '',
        search: '',
        perPage: 10,

        selectedUser: new Set(),
        currentPage: 1,
        pagination: {},
        loading: false,
    },

    /* ===================== DOM CACHE ===================== */
    dom: {},

    /* ===================== INIT ===================== */
    init() {
        this.cacheDom();
        this.bindEvents();
        this.fetchUsers();
    },

    cacheDom() {
        this.dom = {
            searchInput: document.getElementById('searchInput'),
            statusFilter: document.getElementById('statusFilter'),
            roleFilter: document.getElementById('roleFilter'),
            verifiedFilter: document.getElementById('verifiedFilter'),
            perPage: document.getElementById('perPage'),

            tbody: document.getElementById('userTableBody'),
            pagination: document.getElementById('pagination'),

            bulkBar: document.getElementById('bulkActionsBar'),
            selectedCount: document.getElementById('selectedCount'),
            selectAll: document.getElementById('selectAll'),
        };
    },

    /* ===================== EVENTS ===================== */
    bindEvents() {
        if (!this.dom.searchInput) return;

        this.debounceSearch = this.debounce(e => {
            this.state.search = e.target.value;
            this.state.currentPage = 1;
            this.fetchUsers();
        }, 400);

        this.dom.searchInput.addEventListener('input', this.debounceSearch);

        this.dom.statusFilter?.addEventListener('change', e => {
            this.state.userStatus = e.target.value || null;
            this.resetAndFetch();
        });

        this.dom.roleFilter?.addEventListener('change', e => {
            this.state.userRole = e.target.value || null;
            this.resetAndFetch();
        });

        this.dom.verifiedFilter?.addEventListener('change', e => {
            this.state.userVerified = e.target.value;
            this.resetAndFetch();
        });

        this.dom.perPage?.addEventListener('change', e => {
            this.state.perPage = e.target.value;
            this.resetAndFetch();
        });
    },

    resetAndFetch() {
        this.state.currentPage = 1;
        this.fetchUsers();
    },

    /* ===================== API ===================== */
    async fetchUsers() {
        this.state.loading = true;

        const response = await axios.get('/api/admin/users', {
            params: {
                trashed: this.state.currentTab,
                search: this.state.search,
                perPage: this.state.perPage,
                filters: {
                    status: this.state.userStatus,
                    role: this.state.userRole,
                    verified: this.state.userVerified,
                },
                ...this.getQueryParams(),
            }
        });

        this.state.users = response.data.data;
        this.state.pagination = response.data.meta;

        this.renderTable();
        this.renderPagination();

        this.state.loading = false;
    },

    /* ===================== BULK ACTION ===================== */
    toggleSelectAll() {
        const checked = this.dom.selectAll.checked;

        this.state.users.forEach(user => {
            checked
                ? this.state.selectedUser.add(user.id)
                : this.state.selectedUser.delete(user.id);
        });

        this.renderTable();
        this.updateBulkActionBar();
    },

    toggleSelectUser(id) {
        this.state.selectedUser.has(id)
            ? this.state.selectedUser.delete(id)
            : this.state.selectedUser.add(id);

        this.updateBulkActionBar();
        this.updateSelectAllCheckbox();
    },

    updateSelectAllCheckbox() {
        const allSelected =
            this.state.users.length > 0 &&
            this.state.users.every(u => this.state.selectedUser.has(u.id));

        this.dom.selectAll.checked = allSelected;
    },

    updateBulkActionBar() {
        const count = this.state.selectedUser.size;
        this.dom.selectedCount.textContent = count;

        this.dom.bulkBar.classList.toggle('show', count > 0);

        document.getElementById('bulkDeleteBtn').style.display =
            this.state.currentTab === 'active' ? 'inline-block' : 'none';

        document.getElementById('bulkRestoreBtn').style.display =
            this.state.currentTab !== 'active' ? 'inline-block' : 'none';
    },

    /* ===================== SIDEBAR ===================== */
    async openUserSidebar(id) {
        if (!id) return;

        try {
            const { data } = await axios.get(`/api/admin/users/${id}`);
            this.state.userShow = data.data ?? data;
            this.renderSidebar(this.state.userShow);
        } catch (e) {
            console.error('Load user failed', e);
        }
    },

    /* ===================== DELETE / RESTORE ===================== */
    async confirmBulkDelete() {
        try {
            bootstrap.Modal.getInstance(
                document.getElementById('confirmBulkDeleteModal')
            )?.hide();

            await axios.post(
                '/api/admin/users',
                Array.from(this.state.selectedUser)
            );

            this.state.selectedUser.clear();
            this.updateBulkActionBar();
            this.fetchUsers();
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

            await axios.patch(
                '/api/admin/users/restore',
                Array.from(this.state.selectedUser)
            );

            this.state.selectedUser.clear();
            this.updateBulkActionBar();
            this.fetchUsers();
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

            await axios.delete(
                `/api/admin/users/${this.state.idUserDelete}/delete`
            );

            this.state.idUserDelete = null;
            this.fetchUsers();
        } catch (e) {
            console.error(e);
            alert('Delete failed');
        }
    },

    openDeleteModal(id) {
        this.state.idUserDelete = id;
        new bootstrap.Modal(
            document.getElementById('confirmDeleteModal')
        ).show();
    },

    openBulkDeleteModal() {
        const count = this.state.selectedUser.size;

        if (!count) return;

        document.getElementById('bulkDeleteCount').innerText = count;

        const modalEl = document.getElementById('confirmBulkDeleteModal');
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    },

    openBulkRestoreModal() {
        const count = this.state.selectedUser.size;

        if (!count) return;

        document.getElementById('bulkRestoreCount').innerText = count;

        const modalEl = document.getElementById('confirmBulkRestoreModal');
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    },

    clearSelection() {
        // 1. Reset state trước
        this.state.selectedUser.clear();

        // 2. Reset select all
        if (this.dom.selectAll) {
            this.dom.selectAll.checked = false;
        }

        // 3. Render lại UI theo state
        this.renderTable();

        // 4. Update bulk bar
        this.updateBulkActionBar();
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
        this.fetchUsers();
    },

    /* ===================== RENDER ===================== */
    renderTable() {
        const tbody = this.dom.tbody;
        tbody.innerHTML = '';

        if (!this.state.users.length) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="9" class="text-center text-muted">
                        No users found
                    </td>
                </tr>
            `;
            return;
        }

        this.state.users.forEach((user, index) => {
            const isSelected = this.state.selectedUser.has(user.id);

            tbody.innerHTML += `
                <tr class="${isSelected ? 'table-active' : ''}">
                    <td>
                                <input type="checkbox"
                                    data-user-id="${user.id}"
                                    ${isSelected ? 'checked' : ''}
                                    onchange="userPageApp.toggleSelectUser(${user.id})">
                            </td>
                    <td>${index + 1}</td>
                    <td>
                        <div class="user-info">
                            ${user.avatar
                    ? `<img src="/${user.avatar}" class="avatar-circle" alt="${user.full_name}">`
                    : `<div class="avatar-placeholder"></div>`
                }
                            <div>
                                <div class="user-name">${user.full_name}</div>
                                <div class="user-username">@${user.user_name}</div>
                            </div>
                        </div>
                    </td>
                    <td>${user.email}</td>
                    <td>
                        <span class="badge bg-${user.role.color}">
                            ${user.role.name}
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-${user.status.color}">
                            ${user.status.name}
                        </span>
                    </td>
                    <td>
                        <span class="text-nowrap">${user.created_at.date}</span>
                        <small class="text-muted d-block">${user.created_at.time}</small>
                    </td>
                    <td>
                        <span class="text-nowrap">${user.updated_at.date}</span>
                        <small class="text-muted d-block">${user.updated_at.time}</small>
                    </td>
                    <td class="text-nowrap text-center">
                        <button class="btn btn-info btn-action" onclick="userPageApp.openUserSidebar(${user.id})" title="View Details">
                                    <i class='bx bx-scan'></i>
                                </button>
                                <a href="${window.routes.userEdit}/${user.id}/edit" class="btn btn-warning btn-action">
                                    <i class="bx bx-edit"></i>
                                </a>
                                <button class="btn btn-danger btn-action" onclick="userPageApp.openDeleteModal(${user.id})" title="Delete">
                                    <i class="bx bx-trash"></i>
                                </button>
                            </td>
                </tr>
            `;
        });

        this.updateBulkActionBar();
    },

    renderSidebar(user) {
        const sidebar = document.getElementById('userSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const content = document.getElementById('sidebarContent');

        if (!sidebar || !overlay || !content) return;

        const initials = user.full_name
            ?.split(' ')
            .map(w => w[0])
            .join('')
            .substring(0, 2)
            .toUpperCase();

        content.innerHTML = `
        <div class="user-avatar-large">
            ${user.avatar
                ? `<img src="/${user.avatar}" class="avatar-large" alt="${user.full_name}">`
                : `<div class="avatar-placeholder-large">${initials}</div>`
            }
            <div class="user-name-large">${user.full_name}</div>
            <div class="user-username-large">@${user.user_name}</div>
        </div>

        <div class="info-group">
            <div class="info-group-title">Contact Information</div>
            <div class="info-row">
                <span class="info-label">
                    <i class="bi bi-envelope"></i> Email
                </span>
                <span class="info-value">${user.email}</span>
            </div>
            <div class="info-row">
                <span class="info-label">
                    <i class="bi bi-patch-check"></i> Email Verified
                </span>
                <span class="info-value">
                    ${user.email_verified_at
                ? `<span class="badge bg-success">Verified</span>`
                : `<span class="badge bg-warning">Not Verified</span>`
            }
                </span>
            </div>
        </div>

        <div class="info-group">
            <div class="info-group-title">Personal Information</div>
            <div class="info-row">
                <span class="info-label">
                    <i class="bi bi-calendar-heart"></i> Birthday
                </span>
                <span class="info-value">
                    ${user.birthday?.date || 'N/A'}
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">
                    <i class="bi bi-gender-ambiguous"></i> Gender
                </span>
                <span class="info-value">
                    ${user.sex?.value === 0
                ? '<i class="bx bx-male text-primary"></i> Male'
                : user.sex?.value === 1
                    ? '<i class="bx bx-female text-danger"></i> Female'
                    : 'Other'
            }
                </span>
            </div>
        </div>

        <div class="info-group">
            <div class="info-group-title">Account Information</div>
            <div class="info-row">
                <span class="info-label">
                    <i class="bi bi-shield-check"></i> Role
                </span>
                <span class="info-value">
                    <span class="badge bg-${user.role.color}">
                        ${user.role.name}
                    </span>
                </span>
            </div>

            <div class="info-row">
                <span class="info-label">
                    <i class="bi bi-power"></i> Status
                </span>
                <span class="info-value">
                    <span class="badge bg-${user.status.color}">
                        ${user.status.name}
                    </span>
                </span>
            </div>

            <div class="info-row">
                <span class="info-label">
                    <i class="bi bi-clock-history"></i> Created At
                </span>
                <span class="info-value">${user.created_at.date}</span>
            </div>

            ${user.deleted_at
                ? `
                        <div class="info-row">
                            <span class="info-label text-danger">
                                <i class="bi bi-trash"></i> Deleted At
                            </span>
                            <span class="info-value text-danger">
                                ${user.deleted_at}
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
        const sidebar = document.getElementById('userSidebar');
        const overlay = document.getElementById('sidebarOverlay');

        sidebar?.classList.remove('show');
        overlay?.classList.remove('show');
    },

    renderPagination() {
        const container = this.dom.pagination;
        const meta = this.state.pagination;

        if (!meta?.links) {
            container.innerHTML = '';
            return;
        }

        container.innerHTML = meta.links.map(link => `
            <li class="page-item ${link.active ? 'active' : ''} ${!link.url ? 'disabled' : ''}">
                <a class="page-link" href="javascript:void(0)"
                   onclick="${link.url ? `userPageApp.goPage('${link.url}')` : ''}">
                    ${link.label}
                </a>
            </li>
        `).join('');
    },

    goPage(url) {
        const page = new URL(url).searchParams.get('UserDashboard');
        if (!page) return;

        this.setQuery('UserDashboard', page);
        this.fetchUsers();
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

/* ===================== BOOT ===================== */
window.userPageApp = UserPage;
document.addEventListener('DOMContentLoaded', () => UserPage.init());
