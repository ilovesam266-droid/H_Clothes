@props(['id'])
<div class="col-md-4">
    <select class="form-select" {{ $attributes->merge(['id' => $id]) }}>
        {{ $slot }}
    </select>
</div>
