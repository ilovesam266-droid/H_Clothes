@props(['id', 'nameModal', 'nameBtn', 'pageManager', 'event', 'color'])
<div class="modal fade" {{ $attributes->merge(['id' => $id])}}>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 {{ $attributes->merge(['class' => 'modal-title text-' . $color])}}>
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
                <button
                {{ $attributes->merge(['class' => 'btn btn-' . $color])}}
                {{ $attributes->merge(['onclick' => $pageManager . '.' . $event])}}>
                    {{ $nameBtn }}
                </button>
            </div>
        </div>
    </div>
</div>
