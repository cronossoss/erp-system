@extends('layouts.app')

@section('content')
<div class="p-4">

    <h2 class="text-xl font-semibold mb-4">Dashboard</h2>

    <div class="mt-6">
        <h3 class="text-lg font-semibold mb-4">Zaposleni po organizacionim jedinicama</h3>

        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">

            @foreach($units as $unit)
                <div class="border p-2 rounded shadow-sm text-sm cursor-pointer hover:bg-gray-50"
                    onclick="showEmployees({{ $unit->id }})">

                    <div class="text-gray-500 text-sm">[{{ $unit->code }}]</div>
                    <div class="font-semibold text-sm truncate">{{ $unit->name }}</div>
                    <div class="text-lg font-bold mt-1 text-blue-600">{{ $unit->employees_count }}</div>
                </div>
            @endforeach

        </div>
    </div>

</div>

<!-- MODAL LISTA -->
<div id="employeesModal" class="hidden fixed inset-0 flex items-center justify-center z-50">
    <div class="bg-white rounded shadow-lg w-3/4 max-h-[80vh] overflow-auto p-6">

        <h2 class="text-lg font-bold mb-4">Zaposleni</h2>

        <table class="w-full text-sm">
            <tbody id="employeesTable"></tbody>
        </table>

    </div>
</div>

<script>
// =====================================================
// PRIKAZ ZAPOSLENIH
// =====================================================
function showEmployees(unitId){

    let tbody = document.getElementById('employeesTable');

    document.getElementById('employeesModal').classList.remove('hidden');

    fetch(`/employees/by-unit/${unitId}`)
    .then(res => res.json())
    .then(data => {

        tbody.innerHTML = '';

        data.forEach(emp => {

            tbody.innerHTML += `
                <tr 
                    class="border-b hover:bg-gray-50 cursor-pointer employee-row"
                    data-id="${emp.id}"
                >
                    <td class="p-2">${emp.employee_number ?? ''}</td>
                    <td class="p-2">${emp.first_name} ${emp.last_name}</td>
                    <td class="p-2">${emp.position ?? ''}</td>
                </tr>
            `;
        });
    });
}
</script>

@endsection