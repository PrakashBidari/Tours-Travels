document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('[data-search-form]');
    const resultsEl = document.getElementById('search-results');

    if (!form || !resultsEl) {
        return;
    }

    const viewInput = form.querySelector('input[name="view"]');
    const formUrl = new URL(form.getAttribute('action'), window.location.origin);

    async function loadResults(url, { push = true } = {}) {
        resultsEl.style.opacity = '0.5';

        try {
            const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });

            if (!response.ok) {
                throw new Error('Request failed');
            }

            resultsEl.innerHTML = await response.text();

            if (push) {
                window.history.pushState({}, '', url);
            }

            resultsEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
        } catch (error) {
            window.AppAlert?.error('Could not load results. Please try again.');
        } finally {
            resultsEl.style.opacity = '';
        }
    }

    function buildUrl(extra = {}) {
        const params = new URLSearchParams(new FormData(form));

        Object.entries(extra).forEach(([key, value]) => params.set(key, value));

        return `${form.getAttribute('action')}?${params.toString()}`;
    }

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        loadResults(buildUrl());
    });

    form.addEventListener('change', (event) => {
        if (event.target.id === 'sort') {
            loadResults(buildUrl());
        }
    });

    form.addEventListener('click', (event) => {
        const viewBtn = event.target.closest('.view-toggle-btn');

        if (viewBtn) {
            event.preventDefault();
            if (viewInput) {
                viewInput.value = viewBtn.dataset.viewBtn;
            }
            loadResults(buildUrl({ view: viewBtn.dataset.viewBtn }));
            return;
        }

        const link = event.target.closest('a[href]');

        if (!link || link.hasAttribute('data-no-ajax')) {
            return;
        }

        const linkUrl = new URL(link.href, window.location.origin);

        if (linkUrl.pathname !== formUrl.pathname) {
            return;
        }

        event.preventDefault();
        loadResults(link.href);
    });

    window.addEventListener('popstate', () => loadResults(window.location.href, { push: false }));
});
