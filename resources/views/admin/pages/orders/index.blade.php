@extends('admin.layouts.layout-page')

@section('pageTitle', 'Order Management')

@vite(['resources/js/admin/orders/index.js', 'resources/css/admin/orders.css'])

@section('content')
    <div class="container-fluid" id="admin-order-page">
        <div class="d-flex justify-content-between mb-2">
            <x-tabs-section>
                <x-tabs-section.item nameTab='All' pageManager="orderPageApp"
                    event="switchTab('', this)" :isActive=true></x-tabs-section.item>
                <x-tabs-section.item nameTab='Pending' pageManager="orderPageApp"
                    event="switchTab('0', this)"></x-tabs-section.item>
                <x-tabs-section.item nameTab="Confirmed" pageManager="orderPageApp" 
                    event="switchTab('1', this)"></x-tabs-section.item>
                <x-tabs-section.item nameTab="Completed" pageManager="orderPageApp" 
                    event="switchTab('4', this)"></x-tabs-section.item>
                <x-tabs-section.item nameTab="Cancelled" pageManager="orderPageApp" 
                    event="switchTab('5', this)"></x-tabs-section.item>
            </x-tabs-section>
        </div>

        <div class="card">
            <x-search-box placeholder="Search by Order ID or Customer..." />

            <x-filters>
                <x-filters-box.filter-box-select id="statusFilter">
                    <option value="">All Status</option>
                    <option value="0">Pending</option>
                    <option value="1">Confirmed</option>
                    <option value="2">Awaiting Shipping</option>
                    <option value="3">Delivering</option>
                    <option value="4">Completed</option>
                    <option value="5">Cancelled</option>
                    <option value="6">Failed</option>
                </x-filters-box.filter-box-select>

                <x-filters-box.filter-box-select id="perPage">
                    <option value="10">10 / page</option>
                    <option value="25">25 / page</option>
                    <option value="50">50 / page</option>
                    <option value="100">100 / page</option>
                </x-filters-box.filter-box-select>
            </x-filters>

            <div class="card-body p-0">
                <x-table id="orderTableBody" pageManager="orderPageApp">
                    <th>#</th>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Total Amout</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th width="150" class="text-center">Actions</th>
                </x-table>

                <nav>
                    <ul id="pagination" class="pagination justify-content-end"></ul>
                </nav>
            </div>
        </div>
    </div>

    <!-- Status Update Modal -->
    <x-modal id="updateStatusModal" nameModal="Update Order Status" pageManager="orderPageApp"
        event="confirmUpdateStatus()" color="primary" nameBtn="Update Status">
        <form id="updateStatusForm">
            <div class="mb-3">
                <label class="form-label">New Status</label>
                <select id="newStatus" class="form-select">
                    <option value="0">Pending</option>
                    <option value="1">Confirmed</option>
                    <option value="2">Awaiting Shipping</option>
                    <option value="3">Delivering</option>
                    <option value="4">Completed</option>
                    <option value="5">Cancelled</option>
                    <option value="6">Failed</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Admin Note</label>
                <textarea id="adminNote" class="form-control" rows="3" placeholder="Additional notes for this update..."></textarea>
            </div>
            <div id="cancelReasonWrapper" class="mb-3 d-none">
                <label class="form-label text-danger">Cancellation Reason</label>
                <textarea id="cancelReason" class="form-control border-danger" rows="3" placeholder="Why is this order being cancelled?"></textarea>
            </div>
        </form>
    </x-modal>

    <!-- Sidebar Detail -->
    <x-sidebar-item.sidebar-overlay pageManager="orderPageApp" event="closeSidebar()" />
    <x-sidebar id="orderSidebar" nameSidebar="Order Details" pageManager="orderPageApp" event="closeSidebar()" />

    <script>
        window.routes = {
            orderShow: "{{ url('/admin/orders') }}",
            apiOrders: "{{ route('api.admin.orders') }}"
        };
    </script>
@endsection
