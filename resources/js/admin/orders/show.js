const OrderShow = {
    state: {
        orderId: null,
        order: null,
    },

    init() {
        this.state.orderId = document.getElementById('order-detail-page')?.dataset.orderId;
        if (!this.state.orderId) return;

        this.fetchOrder();
    },

    async fetchOrder() {
        try {
            const token = localStorage.getItem('auth_token');
            const response = await axios.get(window.routes.apiOrderShow, {
                headers: { Authorization: `Bearer ${token}` }
            });

            this.state.order = response.data.data;
            this.render();
        } catch (e) {
            console.error('Fetch order detail failed', e);
            document.getElementById('orderContent').innerHTML = `
                <div class="col-12 text-center py-5">
                    <div class="alert alert-danger">Order not found or access denied.</div>
                </div>
            `;
        }
    },

    render() {
        const order = this.state.order;
        const container = document.getElementById('orderContent');
        if (!order || !container) return;

        container.innerHTML = `
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Order Items</h5>
                        <span class="badge bg-${order.status.color}">${order.status.name}</span>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive text-nowrap">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th>Qty</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${order.items.map(item => `
                                        <tr>
                                            <td>
                                                <strong>${item.product_name}</strong><br>
                                                <small class="text-muted">${item.variant_name}</small>
                                            </td>
                                            <td>${new Intl.NumberFormat('vi-VN').format(item.unit_price)}</td>
                                            <td>${item.quantity}</td>
                                            <td>${new Intl.NumberFormat('vi-VN').format(item.subtotal)}</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold">Grand Total</td>
                                        <td class="fw-bold text-primary fs-5">${new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(order.total_amount)}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Audit Log / Notes</h5>
                    </div>
                    <div class="card-body">
                        ${order.customer_note ? `
                            <div class="mb-3">
                                <label class="form-label text-muted small">Customer Note</label>
                                <div class="bg-light p-3 rounded">${order.customer_note}</div>
                            </div>
                        ` : ''}
                        
                        <div class="mb-3">
                            <label class="form-label text-muted small">Admin Note</label>
                            <div class="bg-label-info p-3 rounded">${order.admin_note || 'No notes added.'}</div>
                        </div>

                        ${order.cancel_reason ? `
                            <div class="mb-3">
                                <label class="form-label text-danger small">Cancel Reason</label>
                                <div class="bg-label-danger p-3 rounded">${order.cancel_reason}</div>
                            </div>
                        ` : ''}
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Customer Details</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-3">
                                <span class="fw-bold d-block">Name:</span>
                                <span>${order.address?.recipient_name || order.user?.full_name}</span>
                            </li>
                            <li class="mb-3">
                                <span class="fw-bold d-block">Email:</span>
                                <span>${order.user?.email}</span>
                            </li>
                            <li class="mb-3">
                                <span class="fw-bold d-block">Phone:</span>
                                <span>${order.address?.recipient_phone || 'N/A'}</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Shipping Address</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">${order.address?.full_address || 'N/A'}</p>
                    </div>
                </div>

                <div class="card mb-4 border-primary">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0 text-white">Update Status</h5>
                    </div>
                    <div class="card-body pt-3">
                        <form id="updateStatusDetailForm">
                            <div class="mb-3">
                                <label class="form-label">Set Status</label>
                                <select id="detailStatus" class="form-select">
                                    <option value="0" ${order.status.value === 0 ? 'selected' : ''}>Pending</option>
                                    <option value="1" ${order.status.value === 1 ? 'selected' : ''}>Confirmed</option>
                                    <option value="2" ${order.status.value === 2 ? 'selected' : ''}>Awaiting Shipping</option>
                                    <option value="3" ${order.status.value === 3 ? 'selected' : ''}>Delivering</option>
                                    <option value="4" ${order.status.value === 4 ? 'selected' : ''}>Completed</option>
                                    <option value="5" ${order.status.value === 5 ? 'selected' : ''}>Cancelled</option>
                                    <option value="6" ${order.status.value === 6 ? 'selected' : ''}>Failed</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Add Note</label>
                                <textarea id="detailAdminNote" class="form-control" rows="2"></textarea>
                            </div>
                            <button type="button" onclick="orderShowApp.updateStatus()" class="btn btn-primary w-100">
                                Apply Changes
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        `;
    },

    async updateStatus() {
        const status = document.getElementById('detailStatus').value;
        const note = document.getElementById('detailAdminNote').value;

        try {
            const token = localStorage.getItem('auth_token');
            await axios.patch(window.routes.apiOrderStatus, {
                status: status,
                admin_note: (this.state.order.admin_note ? this.state.order.admin_note + '\n' : '') + note
            }, {
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
            });

            if (window.Toast) {
                window.Toast.success('Status updated');
            } else {
                alert('Status updated');
            }
            this.fetchOrder();
        } catch (e) {
            console.error('Update status failed', e);
            alert(e.response?.data?.message || 'Failed to update status');
        }
    }
};

window.orderShowApp = OrderShow;
document.addEventListener('DOMContentLoaded', () => OrderShow.init());
