// Progressive enhancement: tanpa JS kedua bagian tetap dapat diisi;
// PHP memvalidasi hanya bagian yang sesuai jenis pilihan.
const typeSelect = document.querySelector('#jenis');
if (typeSelect) {
  const updateFields = () => {
    document.querySelectorAll('[data-for]').forEach(group => {
      const active = group.dataset.for === typeSelect.value;
      group.hidden = !active;
      group.disabled = !active;
      group.querySelectorAll('input, select').forEach(field => { field.required = active; });
    });
  };
  typeSelect.addEventListener('change', updateFields);
  updateFields();
}

// Poster dari sumber eksternal dapat berubah. Banner lokal menjadi penggantinya.
document.querySelectorAll('img[data-fallback]').forEach((img) => {
  const fallback = () => {
    if (img.getAttribute('src') !== img.dataset.fallback) {
      img.src = img.dataset.fallback;
      img.closest('.card-cover')?.classList.remove('has-poster');
    }
  };
  img.addEventListener('error', fallback);
  if (img.complete && img.naturalWidth === 0) fallback();
});
