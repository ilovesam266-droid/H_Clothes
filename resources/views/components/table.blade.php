@props(['id', 'pageManager'])
<table class="table table-hover mb-0">
    <thead>
        <tr>
            <th width="50">
                <input type="checkbox" class="checkbox-custom" id="selectAll" {{ $attributes->merge(['onchange' => $pageManager . '.toggleSelectAll()']) }}/>
            </th>
            {{ $slot }}
        </tr>
    </thead>
    <tbody {{ $attributes->merge(['id' => $id]) }}>
        <!-- JS render -->
    </tbody>
</table>
