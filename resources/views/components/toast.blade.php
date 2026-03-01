@props(['id' => 'liveToast'])

<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999">
    <div id="{{ $id }}" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <i class="bi bi-info-circle me-2 toast-icon"></i>
            <strong class="me-auto toast-title">Notification</strong>
            <small class="text-muted">Just now</small>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            <!-- Message will be injected here via JS -->
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if (session('success'))
            Toast.success("{{ session('success') }}");
        @endif

        @if (session('error'))
            Toast.error("{{ session('error') }}");
        @endif

        @if (session('info'))
            Toast.info("{{ session('info') }}");
        @endif

        @if (session('warning'))
            Toast.warning("{{ session('warning') }}");
        @endif
    });
</script>
