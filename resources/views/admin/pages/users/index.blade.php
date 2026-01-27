@extends('admin.layouts.layout-page')

@vite(['resources/js/admin/users/index.js', 'resources/css/admin/users.css'])

@section('content')
    <div class="container-fluid" id="admin-user-page">


        <div class="d-flex justify-content-between mb-2">
            <div class="tabs-section">
                <ul class="nav nav-tabs nav-tabs-custom">
                    <li class="nav-item">
                        <a class="nav-link active" href="#" onclick="app.switchTab('active')">
                            Active Users <span class="badge-count" id="activeCount">0</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="app.switchTab('only')">
                            <i class="bi bi-trash"></i> Deleted Users <span class="badge-count" id="deletedCount">0</span>
                        </a>
                    </li>
                </ul>
            </div>
            <button class="btn btn-add-user text-white" onclick="app.addUser()">
                <i class="bx bx-plus-circle"></i> Add New User
            </button>
        </div>

        <div class="card">
            <div class="card-body flex gap-2">
                <div class="search-box">
                    <i class="bx bx-search"></i>
                    <input type="text" class="form-control" id="searchInput" placeholder="Search by name, email...">
                </div>
            </div>
            <div class="card-body flex gap-2" style="
    padding-top: 0px;
">
                <div class="row g-4">
                    <div class="col-md-3">
                        <select class="form-select" id="statusFilter">
                            <option value="">All Status</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="roleFilter">
                            <option value="">All Roles</option>
                            <option value="0">Admin</option>
                            <option value="1">User</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="verifiedFilter">
                            <option value="">All</option>
                            <option value="1">Verified</option>
                            <option value="0">Not Verified</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="perPage">
                            <option value="10">10 / page</option>
                            <option value="25">25 / page</option>
                            <option value="50">50 / page</option>
                            <option value="100">100 / page</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="bulk-actions" id="bulkActionsBar">
                    <div>
                        <span class="selected-count">
                            <i class="bi bi-check-circle-fill"></i>
                            <span id="selectedCount">0</span> Users selected
                        </span>
                    </div>
                    <div>
                        <button class="btn btn-danger btn-bulk" onclick="app.openBulkDeleteModal()" id="bulkDeleteBtn">
                            <i class="bi bi-trash"></i> Delete Selected
                        </button>
                        <button class="btn btn-success btn-bulk" onclick="app.openBulkRestoreModal()" id="bulkRestoreBtn"
                            style="display:none;">
                            <i class="bi bi-arrow-counterclockwise"></i> Restore Selected
                        </button>
                        <button class="btn btn-secondary btn-bulk" onclick="app.clearSelection()">
                            <i class="bi bi-x-circle"></i> Clear Selection
                        </button>
                    </div>
                </div>
                <table class="table table-hover mb-0">
                    <thead>

                        <tr>
                            <th width="50">
                                <input type="checkbox" class="checkbox-custom" id="selectAll"
                                    onchange="app.toggleSelectAll()">
                            </th>
                            <th>#</th>
                            <th>User</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Updated</th>
                            <th width="150" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="userTableBody">
                        <!-- JS render -->
                    </tbody>
                </table>
                <nav>
                    <ul id="pagination" class="pagination justify-content-end"></ul>
                </nav>
            </div>
        </div>
        <div class="mt-3" id="pagination"></div>
    </div>
    <div class="modal fade" id="confirmBulkDeleteModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title text-danger">
                        <i class="bi bi-exclamation-triangle"></i>
                        Confirm Bulk Delete
                    </h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <p>
                        Are you sure you want to delete
                        <strong id="bulkDeleteCount"></strong> users?
                    </p>
                    <p class="text-muted mb-0">
                        This action can be restored later.
                    </p>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-light" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button class="btn btn-danger" onclick="app.confirmBulkDelete()">
                        Delete
                    </button>
                </div>

            </div>
        </div>
    </div>
    <div class="modal fade" id="confirmBulkRestoreModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title text-danger">
                        <i class="bi bi-exclamation-triangle"></i>
                        Confirm Bulk Restore
                    </h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <p>
                        Are you sure you want to delete
                        <strong id="bulkRestoreCount"></strong> users?
                    </p>
                    <p class="text-muted mb-0">
                        This action can be restored later.
                    </p>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-light" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button class="btn btn-success" onclick="app.confirmBulkRestore()">
                        Restore
                    </button>
                </div>

            </div>
        </div>
    </div>
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="app.closeSidebar()"></div>
    <div class="user-sidebar" id="userSidebar">
        <div class="sidebar-header">
            <h3 class="text-white"><i class="bx bx-user-circle"></i> User Details</h3>
            <button class="btn-close-sidebar" onclick="app.closeSidebar()">
                <i class="bx bx-menu"></i>
            </button>
        </div>
        <div class="sidebar-body" id="sidebarContent">
            <!-- Content will be loaded here -->
        </div>
        <div class="sidebar-actions" id="sidebarActions">
            <!-- Actions will be loaded here -->
        </div>
    </div>
@endsection
