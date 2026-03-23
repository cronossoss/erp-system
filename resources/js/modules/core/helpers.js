export function el(id) {
    return document.getElementById(id);
}

export function csrf() {
    return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
}