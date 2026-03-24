// resources/js/modules/employee/employee-render.js

export function renderRow(emp) {
    return `
        <tr id="row-${emp.id}" 
            class="border-b cursor-pointer hover:bg-gray-50 employee-row"
            data-id="${emp.id}">

            <td class="p-3">${emp.employee_number ?? ''}</td>

            <td class="p-3">
                ${emp.first_name ?? ''} ${emp.last_name ?? ''}
            </td>

            <td class="p-3">${emp.position ?? ''}</td>

            <td class="p-3">
                ${emp.organizational_unit?.name ?? '-'}
            </td>

            <td class="p-3">
                ${emp.contract_type?.name ?? ''}
            </td>

            <td class="p-3 text-right space-x-2">

                <button 
                    class="delete-btn bg-red-500 text-white px-2 py-1 rounded"
                    data-id="${emp.id}">
                    🗑
                </button>

            </td>
        </tr>
    `;
}