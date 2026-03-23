import { searchEmployees } from "./modules/employee/employee-search";
import { saveEmployee } from "./modules/employee/employee-crud";
import { openModal, closeModal, showEmployeeDetail, openEditModal } from "./modules/employee/employee-modal";
import { deleteEmployee } from "./modules/employee/employee-crud";

// DEBUG
console.log("EMPLOYEE PAGE LOADED");

// GLOBAL
window.searchEmployees = searchEmployees;
window.saveEmployee = saveEmployee;
window.openModal = openModal;
window.closeModal = closeModal;
window.showEmployeeDetail = showEmployeeDetail;
window.openEditModal = openEditModal;
window.deleteEmployee = deleteEmployee;

document.addEventListener('click', function(e){

    // ako je klik na dugme → ignoriši
    if(e.target.closest('button')) return;

    let row = e.target.closest('.employee-row');

    if(row){
        let id = row.dataset.id;
        showEmployeeDetail(id);
    }
});