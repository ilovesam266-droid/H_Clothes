@props(['nameModal', 'pageManager', 'event'])
<div class="modal fade" id="confirmBulkDeleteModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger">
                    <i class="bi bi-exclamation-triangle"></i>
                    {{ $nameModal}}
                </h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                {{ $slot }}
            </div>
            <div class="modal-footer">
                <button class="btn btn-light" data-bs-dismiss="modal">
                    Cancel
                </button>
                <button class="btn btn-danger" {{ $attributes->merge(['onclick' => $pageManager . '.' . $event])}}onclick="categoryPageApp.confirmBulkDelete()">
                    Delete
                </button>
            </div>
        </div>
    </div>
</div>
