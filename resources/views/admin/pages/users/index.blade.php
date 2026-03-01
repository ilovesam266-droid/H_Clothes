@extends('admin.layouts.layout-page')

@vite(['resources/js/admin/users/index.js', 'resources/css/admin/users.css'])

@section('content')
    <div class="container-fluid" id="admin-user-page">
        <div class="d-flex justify-content-between mb-2">
            <x-tabs-section>
                <x-tabs-section.item nameTab='Active Users' pageManager="userPageApp"
                    event="switchTab('active', this)" spanId="activeCount" :isActive=true></x-tabs-section.item>
                <x-tabs-section.item nameTab="Deleted Users" pageManager="userPageApp" event="switchTab('only', this)"
                    spanId="deletedCount"></x-tabs-section.item>
            </x-tabs-section>
            <a class="btn btn-add text-white" href="{{ route('admin.user-create') }}">
                <i class="bx bx-plus-circle"></i> Add New User
            </a>
        </div>

        <div class="card">
            <x-search-box placeholder="Search by name, email..." />

            <x-filters>
                <x-filters-box.filter-box-select id="statusFilter">
                    <option value="">All Status</option>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </x-filters-box.filter-box-select>

                <x-filters-box.filter-box-select id="roleFilter">
                    <option value="">All Roles</option>
                    <option value="0">Admin</option>
                    <option value="1">User</option>
                </x-filters-box.filter-box-select>

                <x-filters-box.filter-box-select id="verifiedFilter">
                    <option value="">All</option>
                    <option value="1">Verified</option>
                    <option value="0">Not Verified</option>
                </x-filters-box.filter-box-select>

                <x-filters-box.filter-box-select id="perPage">
                    <option value="10">10 / page</option>
                    <option value="25">25 / page</option>
                    <option value="50">50 / page</option>
                    <option value="100">100 / page</option>
                </x-filters-box.filter-box-select>
            </x-filters>

            <div class="card-body p-0">
                <x-bulk-actions name="Users selected">
                    <x-button-action btnName="Delete Selected" id="bulkDeleteBtn" pageManager="userPageApp"
                        event="openBulkDeleteModal()" color="danger" icon="bx bx-trash" />
                    <x-button-action btnName="Restore Selected" id="bulkRestoreBtn" pageManager="userPageApp"
                        event="openBulkRestoreModal()" color="success" icon="bx bx-undo" />
                    <x-button-action btnName="Clear Selection" pageManager="userPageApp" event="clearSelection()"
                        color="secondary" />
                </x-bulk-actions>

                <x-table id="userTableBody" pageManager="userPageApp">
                    <th>#</th>
                    <th>User</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Updated</th>
                    <th width="150" class="text-center">Actions</th>
                </x-table>

                <nav>
                    <ul id="pagination" class="pagination justify-content-end"></ul>
                </nav>
            </div>
        </div>
        <div class="mt-3" id="pagination"></div>
    </div>

    <!-- Bulk Delete Modal -->
    <x-modal id="confirmBulkDeleteModal" nameModal="Confirm Bulk Delete" pageManager="userPageApp"
        event="confirmBulkDelete()" color="danger" nameBtn="Delete">
        <p>
            Are you sure you want to delete
            <strong id="bulkDeleteCount"></strong> users?
        </p>
        <p class="text-muted mb-0">
            This action can be restored later.
        </p>
    </x-modal>

    <!-- Bulk Restore Modal -->
    <x-modal id="confirmBulkRestoreModal" nameModal="Confirm Bulk Restore" pageManager="userPageApp"
        event="confirmBulkRestore()" color="success" nameBtn="Restore">
        <p>
            Are you sure you want to restore
            <strong id="bulkRestoreCount"></strong> users?
        </p>
        <p class="text-muted mb-0">
            This action will make the users active again.
        </p>
    </x-modal>

    <!-- Single Delete Modal -->
    <x-modal id="confirmDeleteModal" nameModal="Confirm Delete" pageManager="userPageApp" event="confirmDelete()"
        color="danger" nameBtn="Delete">
        <p>
            Are you sure you want to delete this user?
        </p>
        <p class="text-muted mb-0">
            This action can be restored later.
        </p>
    </x-modal>

    <!-- Single Restore Modal -->
    <x-modal id="confirmRestoreModal" nameModal="Confirm Restore" pageManager="userPageApp" event="confirmRestore()"
        color="success" nameBtn="Restore">
        <p>
            Are you sure you want to restore this user?
        </p>
        <p class="text-muted mb-0">
            This action will make the user active again.
        </p>
    </x-modal>

    <!-- Force Delete Modal -->
    <x-modal id="confirmForceDeleteModal" nameModal="Confirm Permanent Delete" pageManager="userPageApp"
        event="confirmForceDelete()" color="danger" nameBtn="Delete Permanently">
        <p>
            Are you sure you want to permanently delete this user?
        </p>
        <p class="text-warning mb-0">
            <i class="bi bi-exclamation-triangle"></i> This action cannot be undone!
        </p>
    </x-modal>

    <!-- Sidebar -->
    <x-sidebar-item.sidebar-overlay pageManager="userPageApp" event="closeSidebar()" />
    <x-sidebar id="userSidebar" nameSidebar="User Details" pageManager="userPageApp" event="closeSidebar()" />

    <script>
        window.routes = {
            userEdit: "{{ url('/admin/users') }}"
        };
    </script>
@endsection
