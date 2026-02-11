@props(['name', 'pageManager', 'event'])
<div class="bulk-actions" id="bulkActionsBar">
    <div>
        <span class="selected-count">
            <i class="bi bi-check-circle-fill"></i>
            <span id="selectedCount">0</span> {{ $name }}
        </span>
    </div>
    <div>
        {{ $slot }}
    </div>
</div>
