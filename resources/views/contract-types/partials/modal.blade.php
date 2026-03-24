<div id="overlay" class="hidden fixed inset-0 bg-black bg-opacity-50 z-40"></div>

<div id="modal" class="hidden fixed inset-0 flex items-center justify-center z-50">

    <div class="bg-white p-6 rounded w-80">

        <h3 class="text-lg font-bold mb-3">Vrsta ugovora</h3>

        <input id="name" class="w-full border px-3 py-2 mb-3" placeholder="Naziv">
        <input id="code" class="w-full border px-3 py-2 mb-4" placeholder="Šifra">

        <div class="flex justify-end gap-2">
            <button onclick="closeModal()">Zatvori</button>
            <button onclick="saveType()" class="bg-blue-600 text-white px-4 py-2 rounded">
                Sačuvaj
            </button>
        </div>

    </div>
</div>

<script>
let currentId = null;

function el(id){ return document.getElementById(id); }
function csrf(){ return document.querySelector('meta[name="csrf-token"]').content; }

function openModal(){
    currentId = null;
    el('name').value = '';
    el('code').value = '';
    el('overlay').classList.remove('hidden');
    el('modal').classList.remove('hidden');
}

function editType(type){

    currentId = type.id;

    el('name').value = type.name ?? '';
    el('code').value = type.code ?? '';

    el('overlay').classList.remove('hidden');
    el('modal').classList.remove('hidden');
}

function closeModal(){
    el('modal').classList.add('hidden');
    el('overlay').classList.add('hidden');
}

function saveType(){

    let name = el('name').value;
    let code = el('code').value;

    if(!name){
        alert('Naziv je obavezan');
        return;
    }

    let url = currentId
        ? `/contract-types/${currentId}`
        : `/contract-types`;

    let method = currentId ? 'PUT' : 'POST';

    fetch(url, {
        method: method,
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrf()
        },
        body: JSON.stringify({ name: name, code: code })
    })
    .then(res => {
        if(res.ok){
            location.reload();
        }
    })
    .catch(err => {
        console.error(err);
        alert("Greška ❌");
    });
}

function deleteType(id){
    if(!confirm('Obrisati?')) return;

    fetch(`/contract-types/${id}`, {
        method: 'DELETE',
        headers: {
            "X-CSRF-TOKEN": csrf()
        }
    }).then(() => location.reload());
}
</script>