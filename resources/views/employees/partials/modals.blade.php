<!-- resources\views\employees\partials\modals.blade.php -->

<div id="overlay" class="hidden fixed inset-0 bg-black bg-opacity-50 z-40"></div>

<!-- ADD -->
<div id="modal" class="hidden fixed inset-0 flex items-center justify-center z-50">
<div class="bg-white p-6 rounded w-96">

<h3 class="text-lg font-bold mb-3">Dodaj zaposlenog</h3>

<input id="employee_number" placeholder="Matični broj (5 cifara)"
class="w-full border px-3 py-2 mb-2">

<input id="first_name" placeholder="Ime" class="w-full border px-3 py-2 mb-2">
<input id="last_name" placeholder="Prezime" class="w-full border px-3 py-2 mb-2">
<input id="position" placeholder="Pozicija" class="w-full border px-3 py-2 mb-2">

<select id="contract_type" class="w-full border px-3 py-2 mb-2">
<option value="">-- Ugovor --</option>
<option value="neodređeno">Neodređeno</option>
<option value="određeno">Određeno</option>
<option value="drugo">Drugo</option>
</select>

<div class="flex gap-2 mb-4">
    <select id="unit_select" class="w-full border px-3 py-2">
        @foreach($units as $unit)
        <option value="{{ $unit->id }}">{{ $unit->name }}</option>
        @endforeach
    </select>

    <button onclick="openUnitModal()"
        class="bg-green-500 text-white px-3 rounded">
        +
    </button>
</div>

<div class="flex justify-end gap-2">
<button onclick="closeModal()">Zatvori</button>
<button onclick="saveEmployee()" class="bg-blue-600 text-white px-4 py-2 rounded">
Sačuvaj
</button>
</div>

</div>
</div>

<!-- EDIT -->
<div id="editModal" class="hidden fixed inset-0 flex items-center justify-center z-50">
<div class="bg-white p-6 rounded w-96">

<input type="hidden" id="edit_id">

<h3 class="text-lg font-bold mb-3">Izmena zaposlenog</h3>

<div class="grid grid-cols-2 gap-4 text-sm">

    <div>
        <span class="text-gray-500">Matični broj</span>
        <input id="edit_employee_number" class="border p-2 w-full mt-1">
    </div>

    <div>
        <span class="text-gray-500">JMBG</span>
        <input id="edit_jmbg" class="border p-2 w-full mt-1">
    </div>

    <div>
        <span class="text-gray-500">Organizaciona jedinica</span>
        <select id="edit_unit_id" class="border p-2 w-full mt-1">
            @foreach($units as $unit)
                <option value="{{ $unit->id }}">
                    {{ $unit->code ?? '' }} - {{ $unit->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <span class="text-gray-500">Tip ugovora</span>
        <select id="edit_contract_type_id" class="border p-2 w-full mt-1">
            <option value="">-- Tip ugovora --</option>
            @foreach($contractTypes as $type)
                <option value="{{ $type->id }}">{{ $type->name }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <span class="text-gray-500">Datum zaposlenja</span>
        <input id="edit_employment_date" type="date" class="border p-2 w-full mt-1">
    </div>

    <div id="contractEndWrapper" class="hidden">
        <span class="text-gray-500">Datum isteka ugovora</span>
        <input id="edit_contract_end_date" type="date" class="border p-2 w-full mt-1">
    </div>

</div>

<div class="flex justify-end gap-2">
<button onclick="closeEditModal()">Zatvori</button>
<button onclick="updateEmployee()" class="bg-blue-600 text-white px-4 py-2 rounded">
Sačuvaj
</button>
</div>

</div>
</div>

<div id="toast" class="hidden fixed top-5 right-5 bg-green-600 text-white px-4 py-2 rounded">
<span id="toast-msg"></span>
</div>

<!-- UNIT MODAL -->
<div id="unitModal" class="hidden fixed inset-0 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded w-80">

        <h3 class="text-lg font-bold mb-3">Nova organizaciona jedinica</h3>

        <input type="text" id="unit_name" placeholder="Naziv"
            class="w-full border px-3 py-2 mb-3">

        <div class="mb-4">
            <label class="text-sm">Nadređena jedinica</label>

            <select id="parent_id" class="w-full border px-3 py-2 mt-1">
                <option value="">-- Nema (root) --</option>

                @foreach($units as $u)
                <option value="{{ $u->id }}">{{ $u->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex justify-end gap-2">
            <button onclick="closeUnitModal()">Zatvori</button>
            <button onclick="saveUnit()" class="bg-blue-600 text-white px-4 py-2 rounded">
                Sačuvaj
            </button>
        </div>

    </div>
</div>

<div id="employeeDetailModal" class="hidden fixed inset-0 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded w-[1000px] max-h-[80vh] overflow-y-auto">

        <h3 class="text-lg font-bold mb-4">Detalji zaposlenog</h3>

        <div id="employeeDetailContent" class="space-y-2 text-sm"></div>
        <div id="employeeDetailFooter" class="flex justify-end mt-4 gap-2"></div>

        <div class="flex justify-end mt-4">
            <button onclick="closeEmployeeDetail()">Zatvori</button>
        </div>

    </div>
</div>