@props(['placeholder' => "Search"])
<div class="card-body flex gap-2">
    <div class="search-box">
        <i class="bx bx-search"></i>
        <input type="text" class="form-control" id="searchInput" {{ $attributes->merge(['placeholder' => $placeholder]) }}>
    </div>
</div>
