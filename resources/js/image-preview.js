/**
 * Any <input type="file" data-preview="#selector"> live-previews the chosen
 * file(s) inside the target container, replacing whatever was rendered there
 * (e.g. the currently saved image on an edit page) with the new selection.
 */
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('input[type="file"][data-preview]').forEach((input) => {
        const target = document.querySelector(input.dataset.preview);
        if (! target) return;

        input.addEventListener('change', () => {
            target.querySelectorAll('img[data-preview-item]').forEach((img) => URL.revokeObjectURL(img.src));
            target.innerHTML = '';

            Array.from(input.files || []).forEach((file) => {
                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                img.dataset.previewItem = '';
                img.alt = file.name;
                img.className = 'w-[200px] h-[140px] object-cover rounded-lg ring-1 ring-gray-900/10';
                target.appendChild(img);
            });
        });
    });
});
