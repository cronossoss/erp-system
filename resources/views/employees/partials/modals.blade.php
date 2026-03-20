<!-- OVERLAY -->
<div id="overlay" class="hidden fixed inset-0 bg-black bg-opacity-50 z-40"
    onclick="closeAllModals()"></div>

<!-- ADD MODAL -->
<div id="modal" class="hidden fixed inset-0 flex items-center justify-center z-50">
    <div class="bg-white rounded shadow-lg p-6 w-96">

        <h2 class="text-xl font-bold mb-4">Dodaj zaposlenog</h2>

        <input type="text" id="first_name" placeholder="Ime"
            class="w-full border rounded px-3 py-2 mb-2">

        <input type="text" id="last_name" placeholder="Prezime"
            class="w-full border rounded px-3 py-2 mb-2">

        <input type="text" id="position" placeholder="Pozicija"
            class="w-full border rounded px-3 py-2 mb-2">

        <div class="flex gap-2 mb-4">
            <select id="unit_select" class="w-full border rounded px-3 py-2">
                @foreach($units as $unit)
                <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                @endforeach
            </select>

            <button onclick="openUnitModal()" class="bg-green-500 text-white px-3 rounded">+</button>
        </div>

        <div class="flex justify-end gap-2">
            <button onclick="closeModal()" class="px-4 py-2 border rounded">Zatvori</button>
            <button onclick="saveEmployee()" class="bg-blue-600 text-white px-4 py-2 rounded">
                Sačuvaj
            </button>
        </div>

    </div>
</div>

<!-- EDIT MODAL -->
<div id="editModal" class="hidden fixed inset-0 flex items-center justify-center z-50">
    <div class="bg-white rounded shadow-lg p-6 w-96">

        <h2 class="text-xl font-bold mb-4">Izmeni zaposlenog</h2>

        <input type="hidden" id="edit_id">

        <input type="text" id="edit_first_name" placeholder="Ime"
            class="w-full border rounded px-3 py-2 mb-2">

        <input type="text" id="edit_last_name" placeholder="Prezime"
            class="w-full border rounded px-3 py-2 mb-2">

        <input type="text" id="edit_position" placeholder="Pozicija"
            class="w-full border rounded px-3 py-2 mb-2">

        <div class="flex gap-2 mb-4">
            <select id="edit_unit_select" class="w-full border rounded px-3 py-2">
                @foreach($units as $unit)
                <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                @endforeach
            </select>

            <button onclick="openUnitModal()" class="bg-green-500 text-white px-3 rounded">+</button>
        </div>

        <div class="flex justify-end gap-2">
            <button onclick="closeEditModal()" class="px-4 py-2 border rounded">Zatvori</button>
            <button onclick="updateEmployee()" class="bg-blue-600 text-white px-4 py-2 rounded">
                Sačuvaj
            </button>
        </div>

    </div>
</div>

<!-- UNIT MODAL -->
<div id="unitModal" class="hidden fixed inset-0 flex items-center justify-center z-50">
    <div class="bg-white rounded shadow-lg p-6 w-80">

        <h3 class="text-lg font-bold mb-4">Nova jedinica</h3>

        <input type="text" id="unit_name" placeholder="Naziv"
            class="w-full border rounded px-3 py-2 mb-3">

        <div class="mb-4">
            <label class="block text-sm mb-1">Nadređena jedinica</label>

            <select id="parent_id" class="w-full border rounded px-3 py-2">
                <option value="">-- Nema (root) --</option>

                @foreach($units as $u)
                <option value="{{ $u->id }}">
                    {{ $u->name }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="flex justify-end gap-2">
            <button onclick="closeUnitModal()" class="px-4 py-2 border rounded">Zatvori</button>
            <button onclick="saveUnit()" class="bg-blue-600 text-white px-4 py-2 rounded">
                Sačuvaj
            </button>
        </div>

    </div>
</div>

<!-- TOAST -->
<div id="toast" class="hidden fixed top-5 right-5 bg-green-600 text-white px-4 py-2 rounded shadow z-50">
    <span id="toast-msg"></span>
</div>