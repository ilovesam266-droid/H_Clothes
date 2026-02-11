@extends('admin.layouts.layout-page')

@vite(['resources/js/admin/categories/index.js', 'resources/css/admin/categories.css'])

@section('content')
    <div class="container-fluid" id="admin-category-page">
        <div class="d-flex justify-content-between mb-2">
            <x-tabs-section>
                <x-tabs-section.item nameTab='Active Categories' pageManager="categoryPageApp" event="switchTab('active', this)"
                    spanId="activeCount" :isActive=true></x-tabs-section.item>
                <x-tabs-section.item nameTab="Deleted Categories" pageManager="categoryPageApp" event="switchTab('only', this)"
                    spanId="deletedCount"></x-tabs-section.item>
            </x-tabs-section>
            <button class="btn btn-add text-white" onclick="categoryPageApp.addCategory()">
                <i class="bx bx-plus-circle"></i> Add New Category
            </button>
        </div>

        <div class="card">
            <x-search-box placeholder="Search by name, slug..." />

            <x-filters>
                <x-filters-box.filter-box-select id="perPage">
                    <option value="10">10 / page</option>
                    <option value="25">25 / page</option>
                    <option value="50">50 / page</option>
                    <option value="100">100 / page</option>
                </x-filters-box.filter-box-select>
            </x-filters>

            <div class="card-body p-0">
                <x-bulk-actions name="Categories selected">
                    <x-button-action btnName="Delete Selected" id="bulkDeleteBtn" pageManager="categoryPageApp"
                        event="openBulkDeleteModal()" color="danger" />
                    <x-button-action btnName="Restore Selected" id="bulkRestoreBtn" pageManager="categoryPageApp"
                        event="openBulkRestoreModal()" color="success" />
                    <x-button-action btnName="Clear Selection" pageManager="categoryPageApp" event="clearSelection()"
                        color="secondary" />
                </x-bulk-actions>

                <x-table id="categoryTableBody" pageManager="categoryPageApp">
                    <th>#</th>
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Created By</th>
                    <th>Usage Count</th>
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
    <x-modal nameModal="Confirm Bulk Delete" pageManager="categoryPageApp" event="confirmBulkDelete()">
        <p>
            Are you sure you want to delete
            <strong id="bulkDeleteCount"></strong> categories?
        </p>
        <p class="text-muted mb-0">
            This action can be restored later.
        </p>
    </x-modal>

    <!-- Bulk Restore Modal -->
    <div class="modal fade" id="confirmBulkRestoreModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-success">
                        <i class="bi bi-arrow-counterclockwise"></i>
                        Confirm Bulk Restore
                    </h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>
                        Are you sure you want to restore
                        <strong id="bulkRestoreCount"></strong> categories?
                    </p>
                    <p class="text-muted mb-0">
                        This action will make the categories active again.
                    </p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button class="btn btn-success" onclick="categoryPageApp.confirmBulkRestore()">
                        Restore
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Single Delete Modal -->
    <div class="modal fade" id="confirmDeleteModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger">
                        <i class="bi bi-exclamation-triangle"></i>
                        Confirm Delete
                    </h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>
                        Are you sure you want to delete this category?
                    </p>
                    <p class="text-muted mb-0">
                        This action can be restored later.
                    </p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button class="btn btn-danger" onclick="categoryPageApp.confirmDelete()">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="categoryPageApp.closeSidebar()"></div>
    <div class="category-sidebar" id="categorySidebar">
        <div class="sidebar-header">
            <h3 class="text-white"><i class="bx bx-category"></i> Category Details</h3>
            <button class="btn-close-sidebar" onclick="categoryPageApp.closeSidebar()">
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

    <script>
        window.routes = {
            categoryEdit: "{{ url('/admin/categories') }}"
        };
    </script>
@endsection
