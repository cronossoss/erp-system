export function showToast(msg, isError = false) {

    let toast = document.getElementById('toast');
    let span = document.getElementById('toast-msg');

    if (!toast || !span) return;

    span.innerText = msg;

    toast.classList.remove('hidden');

    if (isError) {
        toast.classList.remove('bg-green-600');
        toast.classList.add('bg-red-600');
    } else {
        toast.classList.remove('bg-red-600');
        toast.classList.add('bg-green-600');
    }

    setTimeout(() => {
        toast.classList.add('hidden');
    }, 3000);
}