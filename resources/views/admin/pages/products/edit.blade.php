@extends('admin.layouts.layout-page')

@vite(['resources/js/admin/products/edit.js', 'resources/js/admin/images/image-picker.js', 'resources/css/admin/products.css'])

@section('content')
    <div class="form-container">
        <div class="form-header">
            <h2><i class="bi bi-pencil-square"></i> Edit Product</h2>
            <p>Update the information below to edit the product</p>
        </div>

        <div id="formErrorAlert" class="alert alert-danger d-none"></div>

        <form id="productForm">
            <div class="mb-4">
                <label for="name" class="form-label fw-semibold">
                    Product Name <span class="text-danger">*</span>
                </label>
                <input type="text" id="name" name="name" maxlength="125" required
                    class="form-control form-control-lg"
                    placeholder="Enter Product Name...">
                <div class="form-text">Slug is automatically created</div>
            </div>

            <!-- Trạng thái -->
            <div class="mb-4">
                <label for="status" class="form-label fw-semibold">
                    Status
                </label>
                <select id="status" name="status" class="form-select">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                    <option value="2">Draft</option>
                </select>
            </div>

            <!-- Mô tả -->
            <div class="mb-4">
                <label for="description" class="form-label fw-semibold">
                    Description <span class="text-danger">*</span>
                </label>
                <textarea id="description" name="description" rows="5" required
                    class="form-control" placeholder="Enter description"></textarea>
            </div>

            <!-- Danh mục -->
            <div class="mb-4">
                <label class="form-label fw-semibold">
                    <i class="bx bx-tags me-1"></i>Category
                </label>
                <div id="categoriesContainer" class="border rounded p-3" style="max-height: 250px; overflow-y: auto;">
                    <!-- Categories will be loaded here -->
                </div>
            </div>

            <!-- Hình ảnh -->
            <div class="mb-4">
                <label class="form-label fw-semibold">
                    <i class="bx bx-images me-1"></i>Product Image
                </label>

                <div class="drop-zone" id="imageDropZone">
                    <i class="bx bx-cloud-upload fs-1 text-primary"></i>

                    <div class="mt-3 d-flex justify-content-center gap-2">
                        <!-- Mở ImagePicker -->
                        <button type="button" class="btn btn-outline-secondary"
                            onclick="productEditApp.openImagePicker()">
                            <i class="bx bx-images me-2"></i>
                            Image Library
                        </button>
                    </div>

                    <p class="text-muted mt-2 mb-0">
                        Or drag &amp; drop images here
                    </p>
                </div>

                <!-- QUAN TRỌNG: renderImages dùng id này -->
                <div id="imagePreview" class="mt-3 d-flex flex-wrap"></div>
            </div>

            <!-- Chi tiết (JSON) -->
            <div class="mb-4">
                <label class="form-label fw-semibold">
                    <i class="bx bx-list-ul me-1"></i>Product Detail
                </label>
                <div id="detailFields" class="border rounded p-3">
                    <div class="detail-field row g-2">
                        <div class="col-md-5">
                            <input type="text" placeholder="Name (vd: Color, size...)" class="form-control detail-key">
                        </div>
                        <div class="col-md-5">
                            <input type="text" placeholder="Value" class="form-control detail-value">
                        </div>
                        <div class="col-md-2">
                            <button type="button" onclick="productEditApp.removeDetailField(this)" class="btn btn-danger w-100">
                                <i class="bx bx-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <button type="button" onclick="productEditApp.addDetailField()" class="btn btn-success mt-2">
                    <i class="bx bx-plus-lg me-2"></i>Add Field
                </button>
            </div>

            <!-- Buttons -->
            <div class="d-grid gap-2 d-md-flex justify-content-md-end pt-4 border-top">
                <a href="{{ route('admin.products') }}" class="btn btn-secondary px-4">
                    <i class="bi bi-x-circle me-2"></i>Cancel
                </a>
                <button type="submit" class="btn btn-primary px-5">
                    <i class="bi bi-check-circle me-2"></i>Save Changes
                </button>
            </div>
        </form>

        <x-modal id="imagePickerModal" nameModal="Select Image" nameBtn="Confirm" pageManager="imagePicker"
            event="confirmSelection()" color="primary" class="modal-xl">
            <div id="imagePickerGrid" class="image-picker-grid"></div>
            <div>
                <ul id="imagePickerPagination" class="pagination justify-content-center"></ul>
                <button class="btn btn-add text-white justify-content-start">
                    <i class="bx bx-plus-circle"></i> Upload New Image
                </button>
            </div>
        </x-modal>

        <x-modal id="uploadImageModal" nameModal="Upload Images" nameBtn="Upload All" pageManager="imagePageApp"
            event="confirmUploadImages()" color="primary">
            <form id="uploadImageForm" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Select Images (Multiple)</label>
                    <input type="file" class="form-control" name="images[]" id="imageFile" accept="image/*" multiple
                        required>
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

        <x-modal id="categoryModal" nameModal="Select Categories" nameBtn="Done" pageManager="productEditApp"
            event="confirmSelectCategories()" color="primary">

            <div class="mb-3">
                <input type="text" id="categorySearch" class="form-control" placeholder="Search category...">
            </div>

            <div id="categoryList" class="list-group mb-3" style="max-height: 350px; overflow-y: auto;">
                <!-- Categories render tại đây -->
            </div>

            <div id="categoryLoading" class="text-center py-2 d-none">
                Loading categories...
            </div>

            <div class="d-flex justify-content-between">
                <button type="button" class="btn btn-light btn-sm" onclick="productEditApp.clearSelection()">
                    Clear
                </button>
            </div>

            <input type="hidden" name="category_ids[]" id="categoryIds">

        </x-modal>
    </div>
    <script>
        const productIndexRoute = "{{ route('admin.products') }}";
        const productId = {{ $productId }};
    </script>
@endsection
