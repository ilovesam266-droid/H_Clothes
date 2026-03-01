@props(['nameTab', 'pageManager', 'event', 'spanId' => '', 'isActive' => false])
<li class="nav-item">
    <a class="nav-link {{ $isActive ? 'active' : '' }}" href="#" {{ $attributes->merge(['onclick' => $pageManager.'.'.$event]) }}>
        {{ $nameTab }} <span class="badge-count" {{ $attributes->merge(['id' => $spanId]) }}>0</span>
    </a>
</li>
