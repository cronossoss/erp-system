<tr onclick="showEmployees({{ $unit->id }})"
    data-id="{{ $unit->id }}"
    data-parent="{{ $unit->parent_id }}"
    class="cursor-pointer hover:bg-gray-100">
    <td class="p-2">{{ $unit->id }}</td>

    <td class="p-2">
        {!! str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $level) !!}
        <span class="unit-name">{{ $unit->name }}</span>
    </td>

    <td class="p-2 text-right space-x-2">

        <button 
            onclick="event.stopPropagation(); editUnit({{ $unit->id }}, '{{ addslashes($unit->name) }}', '{{ $unit->parent_id }}')"
            class="bg-yellow-500 text-white px-2 py-1 rounded text-xs">
            Edit
        </button>

        <button 
            onclick="event.stopPropagation(); deleteUnit({{ $unit->id }})"
            class="bg-red-500 text-white px-2 py-1 rounded">
            🗑
        </button>

</td>
</tr>

@if($unit->children)
    @foreach($unit->children as $child)
        @include('organizational-units.partials.node', ['unit' => $child, 'level' => $level + 1])
    @endforeach
@endif