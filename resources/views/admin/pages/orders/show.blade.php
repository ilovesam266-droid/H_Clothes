@extends('admin.layouts.layout-page')

@section('pageTitle', 'Order Detail #' . $orderId)

@vite(['resources/js/admin/orders/show.js', 'resources/css/admin/orders.css'])

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y" id="order-detail-page" data-order-id="{{ $orderId }}">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold py-3 mb-0">
                <span class="text-muted fw-light">Orders /</span> #{{ $orderId }}
            </h4>
            <a href="{{ route('admin.orders') }}" class="btn btn-outline-secondary">
                <i class="bx bx-chevron-left"></i> Back to List
            </a>
        </div>

        <div id="orderContent" class="row">
            <!-- Loading Spinner -->
            <div class="col-12 text-center py-5">
                <div class="spinner-border text-primary" role="status"></div>
            </div>
        </div>
    </div>

    <script>
        window.routes = {
            apiOrderShow: "{{ url('/api/admin/orders') }}/{{ $orderId }}",
            apiOrderStatus: "{{ url('/api/admin/orders') }}/{{ $orderId }}/status"
        };
    </script>
@endsection
