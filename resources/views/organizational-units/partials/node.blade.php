<!-- resources/views/organizational-units/partials/node.blade.php -->

<tr 
    onclick="toggleNode(event, this, {{ $unit->id }})"
    data-id="{{ $unit->id }}"
    data-parent="{{ $unit->parent_id }}"
    data-level="{{ $level }}"
    class="cursor-pointer hover:bg-gray-100">
    <td class="p-2">
        <span class="bg-gray-100 px-2 py-1 rounded font-mono">
            {{ $unit->code }}
        </span>
    </td>

    <td class="p-2">
        {!! str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $level) !!}
        @if($unit->children && $unit->children->count())
                <span class="toggle-icon mr-1 inline-block transition-transform">▼</span>
            @else
                <span class="inline-block w-3 mr-1"></span>
            @endif
        <span 
            class="unit-name"
            onclick="event.stopPropagation(); showEmployees({{ $unit->id }})">
             {{ $unit->name }}
            <span class="ml-2 bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs">
                {{ $unit->employees_count ?? 0 }}
            </span>
        </span>
    </td>

    <td class="p-2 text-right space-x-2">

        <button 
                data-id="{{ $unit->id }}"
                data-name="{{ $unit->name }}"
                data-code="{{ $unit->code }}"
                data-parent="{{ $unit->parent_id }}"
                onclick="event.stopPropagation(); openEditUnitModal(this)"
                class="bg-yellow-400 px-2 py-1 rounded">
                ✏️ Izmena
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

