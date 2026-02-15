@extends('admin.layouts.layout-page')

@vite(['resources/js/admin/users/create.js', 'resources/css/admin/users.css'])

@section('content')
    <div class="form-container">
        <div class="form-header">
            <h2><i class="bi bi-person-plus-fill"></i> Create New Product</h2>
            <p>Fill in the information below to create a new product</p>
        </div>

        <form id="productForm">
            <!-- Tên sản phẩm -->
            <div class="mb-4">
                <label for="name" class="form-label fw-semibold">
                    Tên sản phẩm <span class="text-danger">*</span>
                </label>
                <input type="text" id="name" name="name" maxlength="125" required
                    class="form-control form-control-lg" placeholder="Nhập tên sản phẩm">
                <div class="form-text">Slug sẽ tự động tạo từ tên sản phẩm</div>
            </div>

            <!-- Slug (auto-generated) -->
            <div class="mb-4">
                <label for="slug" class="form-label fw-semibold">
                    Slug <span class="text-danger">*</span>
                </label>
                <input type="text" id="slug" name="slug" maxlength="125" required readonly
                    class="form-control bg-light" placeholder="slug-tu-dong">
            </div>

            <!-- Trạng thái -->
            <div class="mb-4">
                <label for="status" class="form-label fw-semibold">
                    Trạng thái
                </label>
                <select id="status" name="status" class="form-select">
                    <option value="1">Hoạt động</option>
                    <option value="0">Không hoạt động</option>
                    <option value="2">Nháp</option>
                </select>
            </div>

            <!-- Mô tả -->
            <div class="mb-4">
                <label for="description" class="form-label fw-semibold">
                    Mô tả <span class="text-danger">*</span>
                </label>
                <textarea id="description" name="description" rows="5" required class="form-control"
                    placeholder="Nhập mô tả sản phẩm"></textarea>
            </div>

            <!-- Danh mục -->
            <div class="mb-4">
                <label class="form-label fw-semibold">
                    <i class="bi bi-tags me-1"></i>Danh mục
                </label>
                <div id="categoriesContainer" class="border rounded p-3" style="max-height: 250px; overflow-y: auto;">
                    <!-- Categories will be loaded here -->
                </div>
            </div>

            <!-- Hình ảnh -->
            <div class="mb-4">
                <label class="form-label fw-semibold">
                    <i class="bi bi-images me-1"></i>Hình ảnh sản phẩm
                </label>
                <div class="drop-zone">
                    <input type="file" id="imageUpload" accept="image/*" multiple class="d-none">
                    <i class="bi bi-cloud-upload fs-1 text-primary"></i>
                    <div class="mt-3">
                        <button type="button" onclick="document.getElementById('imageUpload').click()"
                            class="btn btn-primary">
                            <i class="bi bi-plus-lg me-2"></i>Chọn ảnh
                        </button>
                    </div>
                    <p class="text-muted mt-2 mb-0">Hoặc kéo thả ảnh vào đây</p>
                </div>
                <div id="imagePreview" class="mt-3"></div>
            </div>

            <!-- Chi tiết (JSON) -->
            <div class="mb-4">
                <label class="form-label fw-semibold">
                    <i class="bi bi-list-ul me-1"></i>Chi tiết sản phẩm (Thông tin bổ sung)
                </label>
                <div id="detailFields" class="border rounded p-3">
                    <div class="detail-field row g-2">
                        <div class="col-md-5">
                            <input type="text" placeholder="Tên trường (vd: màu sắc, kích thước...)"
                                class="form-control detail-key">
                        </div>
                        <div class="col-md-5">
                            <input type="text" placeholder="Giá trị" class="form-control detail-value">
                        </div>
                        <div class="col-md-2">
                            <button type="button" onclick="removeDetailField(this)" class="btn btn-danger w-100">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <button type="button" onclick="addDetailField()" class="btn btn-success mt-2">
                    <i class="bi bi-plus-lg me-2"></i>Thêm trường
                </button>
            </div>

            <!-- Buttons -->
            <div class="d-grid gap-2 d-md-flex justify-content-md-end pt-4 border-top">
                <button type="button" onclick="resetForm()" class="btn btn-secondary px-4">
                    <i class="bi bi-x-circle me-2"></i>Hủy
                </button>
                <button type="submit" class="btn btn-primary px-5">
                    <i class="bi bi-check-circle me-2"></i>Tạo sản phẩm
                </button>
            </div>
        </form>
    </div>
@endsection
