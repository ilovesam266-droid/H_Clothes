@extends('admin.layouts.layout-page')

@vite(['resources/js/admin/categories/index.js', 'resources/css/admin/categories.css'])

@section('content')
    <div class="container-fluid" id="admin-category-page">
        <div class="d-flex justify-content-between mb-2">
            <x-tabs-section>
                <x-tabs-section.item nameTab='Active Categories' pageManager="categoryPageApp"
                    event="switchTab('active', this)" spanId="activeCount" :isActive=true></x-tabs-section.item>
                <x-tabs-section.item nameTab="Deleted Categories" pageManager="categoryPageApp" event="switchTab('only', this)"
                    spanId="deletedCount"></x-tabs-section.item>
            </x-tabs-section>
            <button class="btn btn-add text-white" onclick="categoryPageApp.openCreateModal()">
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
                        event="openBulkDeleteModal()" color="danger" icon="bx bx-trash" />
                    <x-button-action btnName="Restore Selected" id="bulkRestoreBtn" pageManager="categoryPageApp"
                        event="openBulkRestoreModal()" color="success" icon="bx bx-undo" />
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
    <x-modal id="confirmBulkDeleteModal" nameModal="Confirm Bulk Delete" pageManager="categoryPageApp"
        event="confirmBulkDelete()" color="danger" nameBtn="Delete">
        <p>
            Are you sure you want to delete
            <strong id="bulkDeleteCount"></strong> categories?
        </p>
        <p class="text-muted mb-0">
            This action can be restored later.
        </p>
    </x-modal>

    <!-- Bulk Restore Modal -->
    <x-modal id="confirmBulkRestoreModal" nameModal="Confirm Bulk Restore" pageManager="categoryPageApp"
        event="confirmBulkRestore()" color="success" nameBtn="Restore">
        <p>
            Are you sure you want to restore
            <strong id="bulkRestoreCount"></strong> categories?
        </p>
        <p class="text-muted mb-0">
            This action will make the categories active again.
        </p>
    </x-modal>

    <!-- Single Delete Modal -->
    <x-modal id="confirmDeleteModal" nameModal="Confirm Delete" pageManager="categoryPageApp" event="confirmDelete()"
        color="danger" nameBtn="Delete">
        <p>
            Are you sure you want to delete this category?
        </p>
        <p class="text-muted mb-0">
            This action can be restored later.
        </p>
    </x-modal>

    <!-- Edit Modal -->
    <x-modal id="createCategoryModal" nameModal="Create Category" nameBtn="Save changes" pageManager="categoryPageApp"
        event="confirmCreateCategory()" color="primary">
        <form id="createCategoryForm">
            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" class="form-control" name="name" id="editCategoryName" required>
            </div>
        </form>
    </x-modal>

    <!-- Create Modal -->
    <x-modal id="editCategoryModal" nameModal="Edit Category" nameBtn="Save changes" pageManager="categoryPageApp"
        event="confirmEditCategory()" color="primary">
        <form id="editCategoryForm">
            <input type="hidden" name="id" id="editCategoryId">

            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" class="form-control" name="name" id="editCategoryName" required>
            </div>
        </form>
    </x-modal>

    <!-- Sidebar -->
    <x-sidebar-item.sidebar-overlay pageManager="categoryPageApp" event="closeSidebar()" />
    <x-sidebar id="categorySidebar" nameSidebar="Category Details" pageManager="categoryPageApp" event="closeSidebar()" />

    <script>
        window.routes = {
            categoryEdit: "{{ url('/admin/categories') }}"
        };
    </script>
@endsection
