@extends('admin.layouts.layout-page')

@vite(['resources/js/admin/products/index.js', 'resources/css/admin/products.css'])

@section('content')
    <div class="container-fluid" id="admin-product-page">
        <div class="d-flex justify-content-between mb-2">
            <x-tabs-section>
                <x-tabs-section.item nameTab='Active Products' pageManager="productPageApp"
                    event="switchTab('active', this)" spanId="activeCount" :isActive=true></x-tabs-section.item>
                <x-tabs-section.item nameTab="Deleted Products" pageManager="productPageApp" event="switchTab('only', this)"
                    spanId="deletedCount"></x-tabs-section.item>
            </x-tabs-section>

            <a class="btn btn-primary" href="{{ route('admin.products-create') }}">
                <i class="bx bx-plus-circle"></i> Add New Product
            </a>
        </div>

        <div class="card">
            <x-search-box placeholder="Search products..." />

            <x-filters>
                <x-filters-box.filter-box-select id="statusFilter">
                    <option value="">All Status</option>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </x-filters-box.filter-box-select>

                <x-filters-box.filter-box-select id="creatorFilter">
                    <option value="">All Creators</option>
                </x-filters-box.filter-box-select>

                <x-filters-box.filter-box-select id="stockFilter">
                    <option value="">All Stock</option>
                    <option value="in_stock">In Stock</option>
                    <option value="low_stock">Low Stock</option>
                    <option value="out_of_stock">Out of Stock</option>
                </x-filters-box.filter-box-select>

                <x-filters-box.filter-box-select id="perPage">
                    <option value="10">10 / page</option>
                    <option value="25">25 / page</option>
                    <option value="50">50 / page</option>
                    <option value="100">100 / page</option>
                </x-filters-box.filter-box-select>
            </x-filters>

            <div class="card-body p-0">
                <x-bulk-actions name="Products selected">
                    <x-button-action btnName="Delete Selected" id="bulkDeleteBtn" pageManager="productPageApp"
                        event="openBulkDeleteModal()" color="danger" icon="bi bi-trash" />

                    <x-button-action btnName="Restore Selected" id="bulkRestoreBtn" pageManager="productPageApp"
                        event="openBulkRestoreModal()" color="success" icon="bi bi-undo"
                        style="display:none;" />

                    <x-button-action btnName="Force Delete Selected" id="bulkForceDeleteBtn" pageManager="productPageApp"
                        event="openBulkForceDeleteModal()" color="danger" icon="bi bi-exclamation-octagon"
                        style="display:none;" />

                    <x-button-action btnName="Clear Selection" id="clearSelectionBtn" pageManager="productPageApp"
                        event="clearSelection()" color="secondary" icon="bi bi-x-circle" />
                </x-bulk-actions>

                <x-table id="productTableBody" pageManager="productPageApp">
                    <th>#</th>
                    <th>Product</th>
                    <th>SKU</th>
                    <th>Stock</th>
                    <th>Price Range</th>
                    <th>Status</th>
                    <th>Created By</th>
                    <th>Created</th>
                    <th width="150" class="text-center">Actions</th>
                </x-table>

                <nav>
                    <ul id="pagination" class="pagination justify-content-end px-3"></ul>
                </nav>
            </div>
        </div>

        <!-- Variants Section -->
        <div class="card mt-3" id="variantsSection" style="display: none;">
            <div class="card-header bg-primary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-white">
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
                <x-table id="variantTableBody" pageManager="productPageApp">
                    <th>#</th>
                    <th>Size</th>
                    <th>Color</th>
                    <th>SKU</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Sold</th>
                    <th>Created</th>
                    <th width="150" class="text-center">Actions</th>
                </x-table>
            </div>
        </div>
    </div>

    {{-- Modals --}}
    <x-modal id="confirmBulkDeleteModal" nameModal="Confirm Bulk Delete" nameBtn="Delete" pageManager="productPageApp"
        event="confirmBulkDelete()" color="danger">
        <p>Are you sure you want to delete <strong id="bulkDeleteCount"></strong> products?</p>
        <p class="text-muted mb-0">This action can be restored later.</p>
    </x-modal>

    <x-modal id="confirmBulkRestoreModal" nameModal="Confirm Bulk Restore" nameBtn="Restore" pageManager="productPageApp"
        event="confirmBulkRestore()" color="success">
        <p>Are you sure you want to restore <strong id="bulkRestoreCount"></strong> products?</p>
    </x-modal>

    <x-modal id="confirmDeleteModal" nameModal="Confirm Delete" nameBtn="Delete" pageManager="productPageApp"
        event="confirmDelete()" color="danger">
        <p>Are you sure you want to delete this product?</p>
        <p class="text-muted mb-0">This action can be restored later.</p>
    </x-modal>

    <x-modal id="confirmRestoreModal" nameModal="Confirm Restore" nameBtn="Restore" pageManager="productPageApp"
        event="confirmRestore()" color="success">
        <p>Are you sure you want to restore this product?</p>
    </x-modal>

    <x-modal id="confirmForceDeleteModal" nameModal="Confirm Permanent Delete" nameBtn="Delete Permanently"
        pageManager="productPageApp" event="confirmForceDelete()" color="danger">
        <p>Are you sure you want to permanently delete this product?</p>
        <p class="text-danger mb-0"><i class="bi bi-exclamation-triangle-fill"></i> This action cannot be undone and will delete all variants and images too.</p>
    </x-modal>

    <x-modal id="confirmDeleteVariantModal" nameModal="Confirm Delete Variant" nameBtn="Delete"
        pageManager="productPageApp" event="confirmDeleteVariant()" color="danger">
        <p>Are you sure you want to delete this variant?</p>
        <p class="text-muted mb-0">This action cannot be undone.</p>
    </x-modal>

    <x-modal id="confirmBulkForceDeleteModal" nameModal="Confirm Bulk Permanent Delete" nameBtn="Delete Permanently"
        pageManager="productPageApp" event="confirmBulkForceDelete()" color="danger">
        <p>Are you sure you want to permanently delete <strong id="bulkForceDeleteCount"></strong> products?</p>
        <p class="text-danger mb-0"><i class="bi bi-exclamation-triangle-fill"></i> This action cannot be undone and will delete all related data (variants, images, etc.).</p>
    </x-modal>

    <!-- Sidebar -->
    <x-sidebar-item.sidebar-overlay pageManager="productPageApp" event="closeSidebar()" />
    <x-sidebar id="productSidebar" nameSidebar="Product Details" pageManager="productPageApp" event="closeSidebar()" />

    <script>
        window.routes = {
            productEdit: "{{ url('/admin/products/') }}",
            productList: "{{ url('/admin/products/list') }}",
            productStore: "{{ url('/admin/products/store') }}",
            productUpdate: "{{ url('/admin/products/update') }}",
            productDelete: "{{ url('/admin/products/delete') }}",
            productRestore: "{{ url('/admin/products/restore') }}",
            productForceDelete: "{{ url('/api/admin/products/force-delete') }}",
            variantList: "{{ url('/admin/products/variants') }}",
            variantStore: "{{ url('/admin/products/variants/store') }}",
            variantUpdate: "{{ url('/admin/products/variants/update') }}",
            variantDelete: "{{ url('/admin/products/variants/delete') }}"
        };
    </script>
@endsection
