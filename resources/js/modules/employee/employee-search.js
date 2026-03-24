// resources/js/modules/employee/employee-search.js

import { renderRow } from "./employee-render";

let timer;

export function searchEmployees() {

    clearTimeout(timer);

    timer = setTimeout(() => {

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

    }, 300); // čeka 300ms
}