import { renderRow } from "./employee-render";

export function searchEmployees() {

    let query = document.getElementById('searchInput').value;

    fetch(`/employees/search?search=${query}`)
    .then(res => res.json())
    .then(data => {

        let tbody = document.querySelector("tbody");
        tbody.innerHTML = '';

        data.forEach(emp => {
            tbody.insertAdjacentHTML('beforeend', renderRow(emp));
        });

    });
}