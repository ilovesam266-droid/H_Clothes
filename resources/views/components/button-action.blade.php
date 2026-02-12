@props(['btnName', 'id'=>'', 'pageManager', 'event', 'color', 'icon'=>''])
<button
    {{ $attributes->merge(['class' => "btn btn-" . $color . " btn-bulk"]) }}
    {{ $attributes->merge(['onclick' => $pageManager . '.' . $event]) }}
    {{ $attributes->merge(['id' => $id]) }}>
    <i {{ $attributes->merge(['class' => $icon]) }}></i> {{ $btnName }}
</button>
