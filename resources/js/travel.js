/**
 * Ram Tours visitor conveniences kept in the browser (no account needed):
 *  - wishlist:        [data-wishlist-tour="slug"] toggles a saved package
 *  - compare:         [data-compare-tour="slug"] adds up to 3 packages to the compare bar
 *  - recently viewed: a <script data-recent-tour> JSON blob on a tour page records the visit,
 *                     and [data-recently-viewed] sections render the list.
 */
const store = {
    get(key) {
        try {
            return JSON.parse(localStorage.getItem(key)) ?? [];
        } catch {
            return [];
        }
    },
    set(key, value) {
        try {
            localStorage.setItem(key, JSON.stringify(value));
        } catch {
            // Storage unavailable (private mode) — features simply don't persist.
        }
    },
};

const WISHLIST = 'rt_wishlist';
const COMPARE = 'rt_compare';
const RECENT = 'rt_recent';
const MAX_COMPARE = 3;

const toast = (message) => window.AppAlert?.success(message, '');

function paintWishlist() {
    const saved = store.get(WISHLIST);
    document.querySelectorAll('[data-wishlist-tour]').forEach((button) => {
        const active = saved.includes(button.dataset.wishlistTour);
        button.classList.toggle('!text-rose-500', active);
        button.querySelector('svg')?.setAttribute('fill', active ? 'currentColor' : 'none');
    });
}

function renderCompareBar() {
    document.getElementById('rt-compare-bar')?.remove();
    const items = store.get(COMPARE);
    if (!items.length) {
        return;
    }

    const bar = document.createElement('div');
    bar.id = 'rt-compare-bar';
    bar.className = 'fixed bottom-5 left-1/2 z-40 flex -translate-x-1/2 items-center gap-3 rounded-full bg-brand-950 py-2 pl-5 pr-2 text-sm text-white shadow-2xl';
    const url = `/tours/compare?tours=${items.map((item) => encodeURIComponent(item.slug)).join(',')}`;
    bar.innerHTML = `
        <span><strong>${items.length}</strong> / ${MAX_COMPARE} selected to compare</span>
        <button type="button" data-compare-clear class="text-xs text-white/70 hover:text-white">Clear</button>
        <a href="${url}" class="rounded-full bg-gold-500 px-4 py-2 font-semibold text-brand-950 hover:bg-gold-400">Compare now</a>`;
    document.body.appendChild(bar);
}

function renderRecent() {
    document.querySelectorAll('[data-recently-viewed]').forEach((section) => {
        const exclude = section.dataset.exclude;
        const items = store.get(RECENT).filter((item) => item.slug !== exclude).slice(0, 4);
        const list = section.querySelector('[data-recently-viewed-list]');
        if (!items.length || !list) {
            return;
        }

        list.replaceChildren(...items.map((item) => {
            const link = document.createElement('a');
            link.href = item.url;
            link.className = 'group block overflow-hidden rounded-xl bg-white shadow-card ring-1 ring-slate-900/5';

            const img = document.createElement('img');
            img.src = item.image;
            img.alt = item.title;
            img.loading = 'lazy';
            img.className = 'aspect-[4/3] w-full object-cover transition group-hover:scale-105';

            const body = document.createElement('div');
            body.className = 'p-3';
            const title = document.createElement('p');
            title.className = 'text-sm font-semibold text-brand-950 line-clamp-1';
            title.textContent = item.title;
            const price = document.createElement('p');
            price.className = 'text-xs font-semibold text-brand-700';
            price.textContent = item.price;
            body.append(title, price);

            link.append(img, body);
            return link;
        }));
        section.hidden = false;
    });
}

document.addEventListener('click', (event) => {
    const wish = event.target.closest('[data-wishlist-tour]');
    if (wish) {
        event.preventDefault();
        const slug = wish.dataset.wishlistTour;
        const saved = store.get(WISHLIST);
        const exists = saved.includes(slug);
        store.set(WISHLIST, exists ? saved.filter((s) => s !== slug) : [slug, ...saved]);
        toast(exists ? 'Removed from your wishlist' : `Saved “${wish.dataset.title}” to your wishlist`);
        paintWishlist();
        return;
    }

    const compare = event.target.closest('[data-compare-tour]');
    if (compare) {
        event.preventDefault();
        const slug = compare.dataset.compareTour;
        let items = store.get(COMPARE);
        if (items.some((item) => item.slug === slug)) {
            toast('Already in your compare list');
        } else if (items.length >= MAX_COMPARE) {
            window.AppAlert?.info(`You can compare up to ${MAX_COMPARE} packages. Remove one first.`);
        } else {
            items = [...items, { slug, title: compare.dataset.title }];
            store.set(COMPARE, items);
            toast('Added to compare');
        }
        renderCompareBar();
        return;
    }

    const remove = event.target.closest('[data-compare-remove]');
    if (remove) {
        const items = store.get(COMPARE).filter((item) => item.slug !== remove.dataset.compareRemove);
        store.set(COMPARE, items);
        window.location.href = `/tours/compare?tours=${items.map((item) => item.slug).join(',')}`;
        return;
    }

    if (event.target.closest('[data-compare-clear]')) {
        store.set(COMPARE, []);
        renderCompareBar();
    }
});

document.addEventListener('DOMContentLoaded', () => {
    const current = document.querySelector('script[data-recent-tour]');
    if (current) {
        try {
            const tour = JSON.parse(current.textContent);
            store.set(RECENT, [tour, ...store.get(RECENT).filter((item) => item.slug !== tour.slug)].slice(0, 8));
        } catch {
            // ignore malformed payload
        }
    }

    paintWishlist();
    renderCompareBar();
    renderRecent();

    const savedLink = document.querySelector('[data-wishlist-link]');
    const saved = store.get(WISHLIST);
    if (savedLink && saved.length) {
        savedLink.href = `/tours?saved=${saved.join(',')}`;
        savedLink.classList.replace('hidden', 'inline-flex');
        savedLink.querySelector('[data-count]').textContent = saved.length;
    }
});
