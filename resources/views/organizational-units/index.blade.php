@extends('layouts.app')

@section('content')

<div class="bg-white p-4 rounded shadow">

    <h2 class="text-lg font-semibold mb-4">
        Organizacione jedinice
    </h2>

    <table class="w-full text-sm">
        <thead class="bg-gray-200">
            <tr>
                <th class="p-2">ID</th>
                <th class="p-2">Naziv</th>
            </tr>
        </thead>

        <tbody>
            @foreach($units as $unit)
            @include('organizational-units.partials.node', [
            'unit' => $unit,
            'level' => 0
            ])
            @endforeach
        </tbody>
    </table>

</div>

<script>
    function toggleChildren(parentId) {

        parentId = String(parentId); // 🔥 dodaj ovo

        let rows = document.querySelectorAll(`[data-parent='${parentId}']`);

        rows.forEach(row => {

            if (row.style.display === 'none') {
                row.style.display = '';
            } else {
                hideRecursive(row.dataset.id);
                row.style.display = 'none';
            }

        });

        // strelica
        let arrow = document.getElementById('arrow-' + parentId);
        if (arrow) {
            arrow.innerText = arrow.innerText === '►' ? '▼' : '►';
        }
    }

    function hideRecursive(parentId) {
        let children = document.querySelectorAll(`[data-parent='${parentId}']`);

        children.forEach(child => {
            child.style.display = 'none';
            hideRecursive(child.dataset.id);
        });
    }
</script>

@endsection