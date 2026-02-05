@extends('admin.layouts.layout-page')

@vite(['resources/js/admin/products/index.js', 'resources/css/admin/products.css'])

@section('content')
    <div class="container-fluid" id="admin-product-page">
        <div class="d-flex justify-content-between mb-2">
            <div class="tabs-section">
                <ul class="nav nav-tabs nav-tabs-custom">
                    <li class="nav-item">
                        <a class="nav-link active" href="#" onclick="productPageApp.switchTab('active')">
                            Active Products <span class="badge-count" id="activeCount">0</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="productPageApp.switchTab('only')">
                            <i class="bi bi-trash"></i> Deleted Products <span class="badge-count" id="deletedCount">0</span>
                        </a>
                    </li>
                </ul>
            </div>
            <button class="btn btn-add-product text-white" onclick="productPageApp.addProduct()">
                <i class="bx bx-plus-circle"></i> Add New Product
            </button>
        </div>

        <div class="card">
            <div class="card-body flex gap-2">
                <div class="search-box">
                    <i class="bx bx-search"></i>
                    <input type="text" class="form-control" id="searchInput" placeholder="Search by name, description...">
                </div>
            </div>
            <div class="card-body flex gap-2" style="padding-top: 0px;">
                <div class="row g-4">
                    <div class="col-md-3">
                        <select class="form-select" id="statusFilter">
                            <option value="">All Status</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="creatorFilter">
                            <option value="">All Creators</option>
                            <!-- Will be populated via JS -->
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="stockFilter">
                            <option value="">All Stock</option>
                            <option value="in_stock">In Stock</option>
                            <option value="low_stock">Low Stock</option>
                            <option value="out_of_stock">Out of Stock</option>
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
                            <span id="selectedCount">0</span> Products selected
                        </span>
                    </div>
                    <div>
                        <button class="btn btn-danger btn-bulk" onclick="productPageApp.openBulkDeleteModal()"
                            id="bulkDeleteBtn">
                            <i class="bi bi-trash"></i> Delete Selected
                        </button>
                        <button class="btn btn-success btn-bulk" onclick="productPageApp.openBulkRestoreModal()"
                            id="bulkRestoreBtn" style="display:none;">
                            <i class="bi bi-arrow-counterclockwise"></i> Restore Selected
                        </button>
                        <button class="btn btn-secondary btn-bulk" onclick="productPageApp.clearSelection()">
                            <i class="bi bi-x-circle"></i> Clear Selection
                        </button>
                    </div>
                </div>
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th width="50">
                                <input type="checkbox" class="checkbox-custom" id="selectAll"
                                    onchange="productPageApp.toggleSelectAll()">
                            </th>
                            <th>#</th>
                            <th>Product</th>
                            <th>SKU</th>
                            <th>Stock</th>
                            <th>Price Range</th>
                            <th>Status</th>
                            <th>Created By</th>
                            <th>Created</th>
                            <th width="150" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="productTableBody">
                        <!-- JS render -->
                    </tbody>
                </table>
                <nav>
                    <ul id="pagination" class="pagination justify-content-end"></ul>
                </nav>
            </div>
        </div>

        <!-- Variants Section (Hidden by default, shown when product is expanded) -->
        <div class="card mt-3" id="variantsSection" style="display: none;">
            <div class="card-header bg-primary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-diagram-3"></i>
                        Product Variants: <span id="variantProductName"></span>
                    </h5>
                    <div>
                        <button class="btn btn-sm btn-light" onclick="productPageApp.addVariant()">
                            <i class="bx bx-plus"></i> Add Variant
                        </button>
                        <button class="btn btn-sm btn-light" onclick="productPageApp.closeVariants()">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Size</th>
                            <th>Color</th>
                            <th>SKU</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Sold</th>
                            <th>Created</th>
                            <th width="150" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="variantTableBody">
                        <!-- JS render -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Bulk Delete Modal -->
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
                        <strong id="bulkDeleteCount"></strong> products?
                    </p>
                    <p class="text-muted mb-0">
                        This action can be restored later.
                    </p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-danger" onclick="productPageApp.confirmBulkDelete()">Delete</button>
                </div>
            </div>
        </div>
    </div>

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
                        <strong id="bulkRestoreCount"></strong> products?
                    </p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-success" onclick="productPageApp.confirmBulkRestore()">Restore</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Single Product Modal -->
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
                    <p>Are you sure you want to delete this product?</p>
                    <p class="text-muted mb-0">This action can be restored later.</p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-danger" onclick="productPageApp.confirmDelete()">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Variant Modal -->
    <div class="modal fade" id="confirmDeleteVariantModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger">
                        <i class="bi bi-exclamation-triangle"></i>
                        Confirm Delete Variant
                    </h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this variant?</p>
                    <p class="text-muted mb-0">This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-danger" onclick="productPageApp.confirmDeleteVariant()">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Sidebar -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="productPageApp.closeSidebar()"></div>
    <div class="product-sidebar" id="productSidebar">
        <div class="sidebar-header">
            <h3 class="text-white"><i class="bx bx-package"></i> Product Details</h3>
            <button class="btn-close-sidebar" onclick="productPageApp.closeSidebar()">
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
            productEdit: "{{ url('/admin/products') }}",
            productList: "{{ url('/admin/products/list') }}",
            productStore: "{{ url('/admin/products/store') }}",
            productUpdate: "{{ url('/admin/products/update') }}",
            productDelete: "{{ url('/admin/products/delete') }}",
            productRestore: "{{ url('/admin/products/restore') }}",
            variantList: "{{ url('/admin/products/variants') }}",
            variantStore: "{{ url('/admin/products/variants/store') }}",
            variantUpdate: "{{ url('/admin/products/variants/update') }}",
            variantDelete: "{{ url('/admin/products/variants/delete') }}"
        };
    </script>
@endsection
