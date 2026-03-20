@extends('layouts.app')

@section('title', 'Radnici')

@section('content')

<div class="bg-white rounded shadow p-4">

    <!-- HEADER -->
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold">Lista zaposlenih</h2>

        <button onclick="openModal()"
            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            + Dodaj zaposlenog
        </button>
    </div>

    <!-- SEARCH -->
    <input type="text" id="search" placeholder="Pretraga..."
        class="border rounded px-3 py-2 w-64 mb-2">

    <!-- SPINNER -->
    <div id="spinner" class="hidden flex items-center gap-2 text-gray-600 mb-4">
        <div class="w-4 h-4 border-2 border-gray-400 border-t-transparent rounded-full animate-spin"></div>
        <span>Pretraga...</span>
    </div>

    <!-- TABLE -->
    <table class="w-full text-sm">
        <thead class="bg-gray-200 text-xs uppercase">
            <tr>
                <th class="p-3 text-left">ID</th>
                <th class="p-3 text-left">Ime</th>
                <th class="p-3 text-left">Pozicija</th>
                <th class="p-3 text-left">Jedinica</th>
                <th class="p-3 text-right">Akcije</th>
            </tr>
        </thead>

        <tbody>
            @foreach($employees as $employee)
            <tr id="row-{{ $employee->id }}" class="border-b">
                <td class="p-3">{{ $employee->id }}</td>
                <td class="p-3">
                    {{ $employee->first_name }} {{ $employee->last_name }}
                </td>
                <td class="p-3">{{ $employee->position }}</td>
                <td class="p-3">
                    <span class="bg-gray-100 px-2 py-1 rounded text-xs">
                        {{ $employee->organizationalUnit->name ?? '-' }}
                    </span>
                </td>
                <td class="p-3 text-right space-x-2">

                    <button class="bg-yellow-400 px-2 py-1 rounded"
                        data-id="{{ $employee->id }}"
                        data-first="{{ $employee->first_name }}"
                        data-last="{{ $employee->last_name }}"
                        data-position="{{ $employee->position }}"
                        data-unit="{{ $employee->organizational_unit_id }}"
                        onclick="openEditModal(this)">
                        ✏️
                    </button>

                    <button class="bg-red-500 text-white px-2 py-1 rounded"
                        data-id="{{ $employee->id }}"
                        onclick="deleteEmployee(this)">
                        🗑
                    </button>

                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- PAGINATION -->
    <div class="mt-4">
        {{ $employees->links() }}
    </div>

</div>

@include('employees.partials.modals')

<script>
    // ===== HELPERS =====
    function el(id) {
        return document.getElementById(id);
    }

    function csrf() {
        return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    }

    // ===== MODALS =====
    function openModal() {

        // RESET FORME
        el('first_name').focus();
        el('first_name').value = '';
        el('last_name').value = '';
        el('position').value = '';
        el('unit_select').value = '';

        el('overlay').classList.remove('hidden');
        el('modal').classList.remove('hidden');
    }

    function closeModal() {
        el('modal').classList.add('hidden');
        el('overlay').classList.add('hidden');
    }

    function openEditModal(btn) {
        el('overlay').classList.remove('hidden');
        el('editModal').classList.remove('hidden');

        el('edit_id').value = btn.dataset.id;
        el('edit_first_name').value = btn.dataset.first;
        el('edit_last_name').value = btn.dataset.last;
        el('edit_position').value = btn.dataset.position;
        el('edit_unit_select').value = btn.dataset.unit;
    }

    function closeEditModal() {
        el('editModal').classList.add('hidden');
        el('overlay').classList.add('hidden');
    }

    function openUnitModal() {

        // RESET
        el('unit_name').value = '';
        if (el('parent_id')) el('parent_id').value = '';

        el('overlay').classList.remove('hidden');
        el('unitModal').classList.remove('hidden');
    }

    function closeAllModals() {
        closeModal();
        closeEditModal();

        el('unitModal')?.classList.add('hidden');
        el('overlay')?.classList.add('hidden');
    }

    function closeUnitModal() {
        el('unitModal').classList.add('hidden');
    }

    // ===== TOAST =====
    function showToast(message) {
        let t = el('toast');
        el('toast-msg').innerText = message;

        t.classList.remove('hidden');
        setTimeout(() => t.classList.add('hidden'), 2000);
    }

    // ===== CRUD =====
    function saveEmployee() {

        fetch("{{ route('employees.store') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrf()
                },
                body: JSON.stringify({
                    first_name: el('first_name').value,
                    last_name: el('last_name').value,
                    position: el('position').value,
                    organizational_unit_id: el('unit_select').value
                })
            })
            .then(res => res.json())
            .then(emp => {

                document.querySelector("tbody")
                    .insertAdjacentHTML('beforeend', renderRow(emp));

                closeModal();
                showToast('Dodat');

            });
    }

    function updateEmployee() {

        let id = el('edit_id').value;

        fetch(`/employees/${id}`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrf(),
                    "X-HTTP-Method-Override": "PUT"
                },
                body: JSON.stringify({
                    first_name: el('edit_first_name').value,
                    last_name: el('edit_last_name').value,
                    position: el('edit_position').value,
                    organizational_unit_id: el('edit_unit_select').value
                })
            })
            .then(res => res.json())
            .then(emp => {

                el('row-' + id).outerHTML = renderRow(emp);

                closeEditModal();
                showToast('Izmenjen');

            });
    }

    function deleteEmployee(btn) {

        let id = btn.dataset.id;

        fetch(`/employees/${id}`, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": csrf(),
                    "X-HTTP-Method-Override": "DELETE"
                }
            })
            .then(() => {
                el('row-' + id).remove();
                showToast('Obrisan');
            });
    }

    // ===== RENDER =====
    function renderRow(emp) {
        return `
        <tr id="row-${emp.id}" class="border-b hover:bg-gray-50">
            <td class="p-3">${emp.id}</td>
            <td class="p-3 font-medium">${emp.first_name} ${emp.last_name}</td>
            <td class="p-3">${emp.position ?? ''}</td>
            <td class="p-3">
                <span class="bg-gray-100 px-2 py-1 rounded text-xs">
                    ${emp.organizational_unit_name ?? '-'}
                </span>
            </td>
            <td class="p-3 text-right space-x-2">

                <button class="bg-yellow-400 px-2 py-1 rounded"
                    data-id="${emp.id}"
                    data-first="${emp.first_name}"
                    data-last="${emp.last_name}"
                    data-position="${emp.position ?? ''}"
                    data-unit="${emp.organizational_unit_id}"
                    onclick="openEditModal(this)">
                    ✏️
                </button>

                <button class="bg-red-500 text-white px-2 py-1 rounded"
                    data-id="${emp.id}"
                    onclick="deleteEmployee(this)">
                    🗑
                </button>

            </td>
        </tr>
    `;
    }

    // ===== SEARCH =====
    let timeout;

    el('search').addEventListener('input', function() {

        clearTimeout(timeout);

        timeout = setTimeout(() => {

            fetch(`/employees/search?search=${this.value}`)
                .then(res => res.json())
                .then(data => {

                    let tbody = document.querySelector("tbody");
                    tbody.innerHTML = '';

                    data.forEach(emp => {
                        tbody.insertAdjacentHTML('beforeend', renderRow(emp));
                    });

                });

        }, 300);
    });

    function saveUnit() {

        fetch("{{ route('organizational-units.store') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrf()
                },
                body: JSON.stringify({
                    name: el('unit_name').value,
                    parent_id: el('parent_id')?.value
                })
            })
            .then(res => res.json())
            .then(data => {

                // dodaj u select
                let select = el('unit_select');
                let editSelect = el('edit_unit_select');

                if (select) {
                    let option = new Option(data.name, data.id);
                    select.add(option);
                    select.value = data.id; // 🔥 odmah selektuj
                }

                if (editSelect) {
                    let option = new Option(data.name, data.id);
                    editSelect.add(option);
                }

                // ✅ zatvori SAMO unit modal
                el('unitModal').classList.add('hidden');

                showToast('Organizaciona jedinica dodata');

            })
            .catch(err => console.error(err));
    }
</script>

@endsection