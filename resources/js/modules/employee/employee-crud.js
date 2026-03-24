import { el, csrf } from "../core/helpers";
import { showToast } from "../core/toast";
import { renderRow } from "./employee-render";
import { openEmployeeModal } from './employee-modal';

export function saveEmployee() {

    let data = {
        employee_number: el('employee_number').value,
        first_name: el('first_name').value,
        last_name: el('last_name').value,
        position: el('position').value,
        organizational_unit_id: el('unit_select').value,
        contract_type: el('contract_type').value
    };

    fetch("/employees", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrf()
        },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(emp => {

        let tbody = document.querySelector("tbody");
        tbody.insertAdjacentHTML('beforeend', renderRow(emp));

        showToast('Radnik dodat');
    });
}

export function deleteEmployee(id) {
    if (!confirm('Da li ste sigurni?')) return;

    fetch(`/employees/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(() => location.reload())
    .catch(() => alert('Greška pri brisanju'));
}