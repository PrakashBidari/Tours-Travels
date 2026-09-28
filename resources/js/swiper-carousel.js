import Swiper from 'swiper';
import { Navigation } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/navigation';

/**
 * Any <div class="swiper" data-swiper> becomes a Swiper carousel with
 * .swiper-wrapper > .swiper-slide markup inside. Nav buttons don't have to
 * live inside the slider itself — mark [data-swiper-prev]/[data-swiper-next]
 * anywhere under the nearest ancestor carrying [data-swiper-scope] (falls
 * back to the slider's own parent if no scope is given) — see the "Trending
 * now" section in resources/views/frontend/home.blade.php, which puts them
 * in the section header instead.
 */
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-swiper]').forEach((el) => {
        const scope = el.closest('[data-swiper-scope]') || el.parentElement;

        new Swiper(el, {
            modules: [Navigation],
            slidesPerView: 1,
            spaceBetween: 20,
            navigation: {
                nextEl: scope.querySelector('[data-swiper-next]'),
                prevEl: scope.querySelector('[data-swiper-prev]'),
            },
            breakpoints: {
                640: { slidesPerView: 2 },
                1024: { slidesPerView: 4 },
            },
        });
    });
});
