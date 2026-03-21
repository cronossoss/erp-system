<!-- UNIT MODAL -->
<div id="unitModal" class="hidden fixed inset-0 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded w-80">

        <h3 class="text-lg font-bold mb-3">Nova jedinica</h3>

        <input id="unit_name" class="w-full border px-3 py-2 mb-3" placeholder="Naziv">

        <select id="parent_id" class="w-full border px-3 py-2 mb-4">
            <option value="">-- Nema (root) --</option>

            @foreach($allUnits as $u)
                <option value="{{ $u->id }}">{{ $u->name }}</option>
            @endforeach
        </select>

        <div class="flex justify-end gap-2">
            <button onclick="closeUnitModal()">Zatvori</button>
            <button onclick="saveUnit()" class="bg-blue-600 text-white px-4 py-2 rounded">
                Sačuvaj
            </button>
        </div>

    </div>
</div>