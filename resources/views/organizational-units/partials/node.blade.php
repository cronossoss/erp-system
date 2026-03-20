<tr
    data-id="{{ $unit->id }}"
    data-parent="{{ $unit->parent_id }}"
    class="unit-row hover:bg-gray-100 {{ $unit->children->count() ? 'cursor-pointer' : '' }}"
    @if($unit->children->count())
    onclick="toggleChildren(this.dataset.id)"
    @endif
    >
    <td class="p-2">{{ $unit->id }}</td>

    <td class="p-2">
        {!! str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $level) !!}

        @if($unit->children->count())
        <span id="arrow-{{ $unit->id }}" class="inline-block w-4 text-black">►</span>
        @else
        <span class="inline-block w-4 text-gray-700">─</span>
        @endif

        {{ $unit->name }}
    </td>
</tr>

@if($unit->children && $unit->children->count())
@foreach($unit->children as $child)
@include('organizational-units.partials.node', [
'unit' => $child,
'level' => $level + 1
])
@endforeach
@endif