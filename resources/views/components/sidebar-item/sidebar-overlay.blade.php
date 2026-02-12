@props(['pageManager', 'event'])
<div class="sidebar-overlay" id="sidebarOverlay"
{{ $attributes->merge(['onclick' => $pageManager . '.' . $event]) }}></div>
