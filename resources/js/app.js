// resources/js/app.js

import './bootstrap';

import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.min.css";

import { searchEmployees } from "./modules/employee/employee-search";
import { openEmployeeModal, closeEmployeeModal } from "./modules/employee/employee-modal";

console.log("APP JS RADI");

// GLOBAL (za Blade)
window.searchEmployees = searchEmployees;
window.openEmployeeModal = openEmployeeModal;
window.closeEmployeeModal = closeEmployeeModal;

// DELETE STATE
let deleteId = null;

// EVENT DELEGATION
document.addEventListener('click', function(e) {

    // DELETE BUTTON
    const deleteBtn = e.target.closest('.delete-btn');
    if (deleteBtn) {
        e.stopPropagation();

        deleteId = deleteBtn.dataset.id;

        const modal = document.getElementById('deleteModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');

        return;
    }

    // ROW CLICK
    const row = e.target.closest('.employee-row');
    if (row && row.dataset.id) {
        openEmployeeModal(row.dataset.id);
    }

});

// DELETE ACTIONS
window.closeDeleteModal = function() {
    const modal = document.getElementById('deleteModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

window.confirmDelete = function() {

    fetch(`/employees/${deleteId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(() => {
        closeDeleteModal();
        location.reload();
    })
    .catch(() => alert('Greška pri brisanju'));

}

document.addEventListener('click', function(e) {

    if (e.target && e.target.id === 'addWorkBtn') {

        const employeeId = e.target.dataset.id;

        console.log('klik radi', employeeId);

        openWorkEntryModal(employeeId);
    }

});

window.openWorkEntryModal = function(employeeId) {
    console.log('klik radi', employeeId);

    document.getElementById('we_employee_id').value = employeeId;

    let modal = document.getElementById('workEntryModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

window.closeWorkEntryModal = function() {
    let modal = document.getElementById('workEntryModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

// FLATPICKR
window.flatpickr = flatpickr;