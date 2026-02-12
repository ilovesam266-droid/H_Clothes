@props(['id', 'nameSidebar', 'pageManager', 'event'])
<div class="sidebar" {{ $attributes->merge(['id' => $id]) }}>
    <div class="sidebar-header">
        <h3 class="text-white"><i class="bx bx-category"></i> {{ $nameSidebar }}</h3>
        <button class="btn-close-sidebar" {{ $attributes->merge(['onclick' => $pageManager . '.' . $event]) }}>
            <i class="bx bx-menu"></i>
        </button>
    </div>
    {{ $slot }}
    <div class="sidebar-body" id="sidebarContent">
        </div>
    <div class="sidebar-actions" id="sidebarActions">
        </div>
</div>
