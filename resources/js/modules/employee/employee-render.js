export function renderRow(emp) {
    return `
        <tr id="row-${emp.id}" class="border-b cursor-pointer hover:bg-gray-50"
            onclick="showEmployeeDetail(${emp.id})">

            <td class="p-3">${emp.employee_number ?? ''}</td>
            <td class="p-3">${emp.first_name} ${emp.last_name}</td>
            <td class="p-3">${emp.position ?? ''}</td>
            <td class="p-3">${emp.organizational_unit_name ?? '-'}</td>
            <td class="p-3">${emp.contract_type ?? ''}</td>

            <td class="p-3 text-right space-x-2">

                <button class="bg-yellow-400 px-2 py-1 rounded"
                    data-id="${emp.id}"
                    data-first="${emp.first_name}"
                    data-last="${emp.last_name}"
                    data-position="${emp.position ?? ''}"
                    data-unit="${emp.organizational_unit_id ?? ''}"
                    data-employee_number="${emp.employee_number ?? ''}"
                    data-contract_type="${emp.contract_type ?? ''}"
                    onclick="event.stopPropagation(); openEditModal(this)">
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