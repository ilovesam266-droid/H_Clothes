const UserPage = {
    state: {
        users: [],
        userShow: null,
        deletedUsers: [],
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

    init() {
        this.bindEvents();
        this.fetchUsers();
    },

    bindEvents() {
        const searchInput = document.getElementById('searchInput');
        const statusFilter = document.getElementById('statusFilter');
        const roleFilter = document.getElementById('roleFilter');
        const userVerified = document.getElementById('verifiedFilter');
        const perPage = document.getElementById('perPage');

        this.debounceSearch = this.debounce((e) => {
            this.state.search = e.target.value;
            this.state.currentPage = 1;
            this.fetchUsers();
        }, 400);

        searchInput.addEventListener('input', this.debounceSearch);

        statusFilter.addEventListener('change', (e) => {
            this.state.userStatus = e.target.value || null;
            this.state.currentPage = 1;
            this.fetchUsers();
        });

        roleFilter.addEventListener('change', (e) => {
            this.state.userRole = e.target.value || null;
            this.state.currentPage = 1;
            this.fetchUsers();
        })

        userVerified.addEventListener('change', (e) => {
            this.state.userVerified = e.target.value;
            this.state.currentPage = 1;
            this.fetchUsers();
        })

        perPage.addEventListener('change', (e) => {
            this.state.perPage = e.target.value;
            this.state.currentPage = 1;
            this.fetchUsers();
        })
    },

    async fetchUsers() {
        this.state.loading = true;

        const response = await window.axios.get('/api/admin/users', {
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

    async confirmBulkDelete() {
        try {
            const el = document.getElementById('confirmBulkDeleteModal');
            const modal = el ? bootstrap.Modal.getInstance(el) : null;

            if (modal) modal.hide();

            await axios.post('/api/admin/users',
                Array.from(this.state.selectedUser)
            );

            this.state.selectedUser.clear();
            this.updateBulkActionBar();

            this.fetchUsers();
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

            await axios.patch('/api/admin/users/restore', Array.from(this.state.selectedUser));

            this.state.selectedUser.clear();
            this.updateBulkActionBar();


            this.fetchUsers();
        } catch (e) {
            alert('Bulk delete failed');
            console.error(e);
        }
    },

    async openUserSidebar(id) {
        if (!id) return;

        try {
            const { data } = await axios.get(`/api/admin/users/${id}`);

            this.state.userShow = data.data ?? data;
            this.openSidebar(this.state.userShow);
        } catch (e) {
            console.error('Load user failed', e);
        }
    },


    openBulkDeleteModal() {
        if (this.state.selectedUser.size === 0) return;

        document.getElementById('bulkDeleteCount').innerText =
            this.state.selectedUser.size;

        new bootstrap.Modal(
            document.getElementById('confirmBulkDeleteModal')
        ).show();
    },

    openBulkRestoreModal() {
        if (this.state.selectedUser.size === 0) return;

        document.getElementById('bulkRestoreCount').innerText =
            this.state.selectedUser.size;

        new bootstrap.Modal(
            document.getElementById('confirmBulkRestoreModal')
        ).show();
    },


    renderTable() {
        const tbody = document.getElementById('userTableBody');


        tbody.innerHTML = '';

        if (!this.state.users.length) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center text-muted">
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
                                <input type="checkbox" class="checkbox-custom user-checkbox"
                                    data-user-id="${user.id}"
                                    ${isSelected ? 'checked' : ''}
                                    onchange="app.toggleSelectUser(${user.id})">
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
                        <span class="badge bg-${user.status == 'Active' ? 'success' : 'danger'}">
                            ${user.status}
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
                        <button class="btn btn-info btn-action" onclick="app.openUserSidebar(${user.id})" title="View Details">
                                    <i class='bx bx-scan'></i>
                                </button>
                                <button class="btn btn-warning btn-action" onclick="app.editUser(${user.id})" title="Edit">
                                    <i class="bx bx-edit"></i>
                                </button>
                                <button class="btn btn-danger btn-action" onclick="app.deleteUser(${user.id})" title="Delete">
                                    <i class="bx bx-trash"></i>
                                </button>
                            </td>
                </tr>
            `;
        });

        this.updateBulkActionBar();
    },

    toggleSelectAll() {
        const selectAllCheckbox = document.getElementById('selectAll');
        const currentData = this.state.users;

        if (selectAllCheckbox.checked) {
            currentData.forEach(user => this.state.selectedUser.add(user.id));
        } else {
            currentData.forEach(user => this.state.selectedUser.delete(user.id));
        }

        this.updateBulkActionBar();
        this.updateSelectAllCheckbox();
        this.renderTable();
    },

    toggleSelectUser(userId) {
        if (this.state.selectedUser.has(userId)) {
            this.state.selectedUser.delete(userId);
        } else {
            this.state.selectedUser.add(userId);
        }

        this.updateBulkActionBar();
        this.updateSelectAllCheckbox();
    },

    updateSelectAllCheckbox() {
        const selectAllCheckbox = document.getElementById('selectAll');
        const currentData = this.state.users;
        const allSelected = currentData.length > 0 && currentData.every(user => this.state.selectedUser.has(user.id));

        selectAllCheckbox.checked = allSelected;
    },

    clearSelection() {
        this.state.selectedUser.clear();
        this.renderTable();
    },

    updateBulkActionBar() {
        const bar = document.getElementById('bulkActionsBar');
        const count = this.state.selectedUser.size;
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

    switchTab(tab) {
        this.state.currentTab = tab;
        this.state.selectedUser.clear();
        this.state.currentPage = 1;

        document.querySelectorAll('.nav-tabs-custom .nav-link').forEach(link => {
            link.classList.remove('active');
        });
        event.target.classList.add('active');

        const cleanUrl = window.location.origin + window.location.pathname;
        history.replaceState({}, '', cleanUrl);

        this.fetchUsers();
        this.renderTable();
        this.renderPagination();
    },

    openSidebar(user) {
        const sidebar = document.getElementById('userSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const content = document.getElementById('sidebarContent');
        const actions = document.getElementById('sidebarActions');

        const isDeleted = user.deleted_at !== null;

        // Render content
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
                                <i class="bi bi-envelope"></i>
                                Email
                            </span>
                            <span class="info-value">${user.email}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">
                                <i class="bi bi-patch-check"></i>
                                Email Verified
                            </span>
                            <span class="info-value">
                                ${user.email_verified_at
                ? `<span class="badge bg-success">Verified</span>`
                : `<span class="badge bg-warning">Not Verified</span>`}
                            </span>
                        </div>
                    </div>

                    <div class="info-group">
                        <div class="info-group-title">Personal Information</div>
                        <div class="info-row">
                            <span class="info-label">
                                <i class="bi bi-calendar-heart"></i>
                                Birthday
                            </span>
                            <span class="info-value">${user.birthday || 'N/A'}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">
                                <i class="bi bi-gender-ambiguous"></i>
                                Gender
                            </span>
                            <span class="info-value">
                                ${user.sex === 1
                ? '<i class="bi bi-gender-male text-primary"></i> Male'
                : user.sex === 2
                    ? '<i class="bi bi-gender-female text-danger"></i> Female'
                    : 'Other'}
                            </span>
                        </div>
                    </div>

                    <div class="info-group">
                        <div class="info-group-title">Account Information</div>
                        <div class="info-row">
                            <span class="info-label">
                                <i class="bi bi-shield-check"></i>
                                Role
                            </span>
                            <span class="info-value">
                                <span class="badge ${user.role === 1 ? 'badge-role' : 'badge-role-user'}">
                                    ${user.role === 1 ? 'Admin' : 'User'}
                                </span>
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">
                                <i class="bi bi-power"></i>
                                Status
                            </span>
                            <span class="info-value">
                                <span class="badge bg-${user.status === 1 ? 'success' : 'danger'}">
                                    ${user.status === 1 ? 'Active' : 'Inactive'}
                                </span>
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">
                                <i class="bi bi-clock-history"></i>
                                Created At
                            </span>
                            <span class="info-value">${user.created_at}</span>
                        </div>
                        ${user.deleted_at ? `
                            <div class="info-row">
                                <span class="info-label">
                                    <i class="bi bi-trash"></i>
                                    Deleted At
                                </span>
                                <span class="info-value text-danger">${user.deleted_at}</span>
                            </div>
                        ` : ''}
                    </div>
                `;

        // Show sidebar
        sidebar.classList.add('show');
        overlay.classList.add('show');
    },

    closeSidebar() {
        const sidebar = document.getElementById('userSidebar');
        const overlay = document.getElementById('sidebarOverlay');

        sidebar.classList.remove('show');
        overlay.classList.remove('show');
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
                    const page = url.searchParams.get('UserDashboard');

                    if (!page) return;

                    // cập nhật query string
                    this.setQuery('UserDashboard', page);

                    // fetch lại data
                    this.fetchUsers();
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

window.app = UserPage;

document.addEventListener('DOMContentLoaded', () => UserPage.init());
