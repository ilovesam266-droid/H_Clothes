@props(['btnName', 'id'=>'', 'pageManager', 'event', 'color'])
<button
    {{ $attributes->merge(['class' => "btn btn-" . $color . " btn-bulk"]) }}
    {{ $attributes->merge(['onclick' => $pageManager . '.' . $event]) }}
    {{ $attributes->merge(['id' => $id]) }}>
    <i class="bi bi-trash"></i> {{ $btnName }}
</button>
