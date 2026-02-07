@props(['nameTab', 'pageManager', 'event', 'spanId'])
<li class="nav-item">
    <a class="nav-link active" href="#" {{ $attributes->merge(['onchange' => $pageManager.'.'.$event]) }}>
        $nameTab <span class="badge-count" {{ $attributes->merge(['id' => $spanId]) }}>0</span>
    </a>
</li>
