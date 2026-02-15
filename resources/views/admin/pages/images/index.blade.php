@extends('admin.layouts.layout-page')

@vite(['resources/js/admin/images/index.js', 'resources/css/admin/images.css'])

@section('content')
    <div class="container-fluid" id="admin-image-page">
        <div class="d-flex justify-content-between mb-2">
            <x-tabs-section>
                <x-tabs-section.item nameTab='Active Images' pageManager="imagePageApp"
                    event="switchTab('active', this)" spanId="activeCount" :isActive=true></x-tabs-section.item>
                <x-tabs-section.item nameTab="Deleted Images" pageManager="imagePageApp" event="switchTab('only', this)"
                    spanId="deletedCount"></x-tabs-section.item>
            </x-tabs-section>
            <button class="btn btn-add text-white" onclick="imagePageApp.openUploadModal()">
                <i class="bx bx-plus-circle"></i> Upload New Image
            </button>
        </div>

        <div class="card">
            <x-search-box placeholder="Search by url..." />

            <x-filters>
                <x-filters-box.filter-box-select id="perPage">
                    <option value="10">10 / page</option>
                    <option value="25">25 / page</option>
                    <option value="50">50 / page</option>
                    <option value="100">100 / page</option>
                </x-filters-box.filter-box-select>
            </x-filters>

            <div class="card-body p-0">
                <x-bulk-actions name="Images selected">
                    <x-button-action btnName="Delete Selected" id="bulkDeleteBtn" pageManager="imagePageApp"
                        event="openBulkDeleteModal()" color="danger" icon="bx bx-trash" />
                    <x-button-action btnName="Restore Selected" id="bulkRestoreBtn" pageManager="imagePageApp"
                        event="openBulkRestoreModal()" color="success" icon="bx bx-undo" />
                    <x-button-action btnName="Clear Selection" pageManager="imagePageApp" event="clearSelection()"
                        color="secondary" />
                </x-bulk-actions>

                <x-table id="imageTableBody" pageManager="imagePageApp">
                    <th>#</th>
                    <th>Preview</th>
                    <th>URL</th>
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
    <x-modal id="confirmBulkDeleteModal" nameModal="Confirm Bulk Delete" pageManager="imagePageApp"
        event="confirmBulkDelete()" color="danger" nameBtn="Delete">
        <p>
            Are you sure you want to delete
            <strong id="bulkDeleteCount"></strong> images?
        </p>
        <p class="text-muted mb-0">
            This action can be restored later.
        </p>
    </x-modal>

    <!-- Bulk Restore Modal -->
    <x-modal id="confirmBulkRestoreModal" nameModal="Confirm Bulk Restore" pageManager="imagePageApp"
        event="confirmBulkRestore()" color="success" nameBtn="Restore">
        <p>
            Are you sure you want to restore
            <strong id="bulkRestoreCount"></strong> images?
        </p>
        <p class="text-muted mb-0">
            This action will make the images active again.
        </p>
    </x-modal>

    <!-- Single Delete Modal -->
    <x-modal id="confirmDeleteModal" nameModal="Confirm Delete" pageManager="imagePageApp" event="confirmDelete()"
        color="danger" nameBtn="Delete">
        <p>
            Are you sure you want to delete this image?
        </p>
        <p class="text-muted mb-0">
            This action can be restored later.
        </p>
    </x-modal>

    <!-- Upload Modal -->
    <x-modal id="uploadImageModal" nameModal="Upload Images" nameBtn="Upload All" pageManager="imagePageApp"
        event="confirmUploadImage()" color="primary">
        <form id="uploadImageForm" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Select Images (Multiple)</label>
                <input type="file" class="form-control" name="images[]" id="imageFile" accept="image/*" multiple required>
                <small class="text-muted">You can select multiple images at once</small>
            </div>
            <div class="mb-3" id="imagePreviewContainer" style="display: none;">
                <label class="form-label">Preview (<span id="imageCount">0</span> images selected)</label>
                <div id="imagePreviewGrid" class="image-preview-grid">
                    <!-- Previews will be inserted here -->
                </div>
            </div>
        </form>
    </x-modal>

    <!-- Edit Modal -->
    <x-modal id="editImageModal" nameModal="Edit Image" nameBtn="Save changes" pageManager="imagePageApp"
        event="confirmEditImage()" color="primary">
        <form id="editImageForm">
            <input type="hidden" name="id" id="editImageId">

            <div class="mb-3">
                <label class="form-label">Current Image</label>
                <div>
                    <img id="currentImage" src="" alt="Current" class="img-thumbnail" style="max-width: 300px; max-height: 300px;">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Replace Image (Optional)</label>
                <input type="file" class="form-control" name="image" id="editImageFile" accept="image/*">
            </div>

            <div class="mb-3" id="editImagePreviewContainer" style="display: none;">
                <label class="form-label">New Preview</label>
                <div>
                    <img id="editImagePreview" src="" alt="Preview" class="img-thumbnail" style="max-width: 300px; max-height: 300px;">
                </div>
            </div>
        </form>
    </x-modal>

    <!-- Sidebar -->
    <x-sidebar-item.sidebar-overlay pageManager="imagePageApp" event="closeSidebar()" />
    <x-sidebar id="imageSidebar" nameSidebar="Image Details" pageManager="imagePageApp" event="closeSidebar()" />

    <script>
        window.routes = {
            imageEdit: "{{ url('/admin/images') }}"
        };
    </script>
@endsection
