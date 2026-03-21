<tr onclick="showEmployees({{ $unit->id }})" class="cursor-pointer hover:bg-gray-100">
    <td class="p-2">{{ $unit->id }}</td>

    <td class="p-2">
        {!! str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $level) !!}
        {{ $unit->name }}
    </td>

    <td class="p-2 text-right space-x-2">

        <button onclick="editUnit({{ $unit->id }}, '{{ $unit->name }}', '{{ $unit->parent_id }}')"
            class="bg-yellow-400 px-2 py-1 rounded">
            ✏️
        </button>

        <button onclick="deleteUnit({{ $unit->id }})"
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