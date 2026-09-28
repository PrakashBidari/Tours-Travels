import GLightbox from 'glightbox';
import 'glightbox/dist/css/glightbox.min.css';

/**
 * Any <a class="glightbox"> becomes a zoomable lightbox image. Group related
 * images into one gallery by giving them a shared data-gallery value — see
 * resources/views/frontend/properties/show.blade.php.
 */
document.addEventListener('DOMContentLoaded', () => {
    GLightbox({ selector: '.glightbox' });
});
