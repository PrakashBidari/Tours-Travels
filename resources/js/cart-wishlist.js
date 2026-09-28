window.toggleWishlist = function (button) {
    axios.post(button.dataset.url).then(({ data }) => {
        const card = button.closest('[data-wishlist-card]');

        if (card) {
            card.remove();
            return;
        }

        button.dataset.wishlisted = data.wishlisted ? '1' : '0';
        button.title = data.wishlisted ? 'Remove from wishlist' : 'Add to wishlist';
        button.classList.toggle('text-red-500', data.wishlisted);
        button.classList.toggle('text-gray-400', !data.wishlisted);

        const label = button.querySelector('[data-wishlist-label]');
        if (label) {
            label.textContent = data.wishlisted ? '♥ Remove from wishlist' : '♡ Add to wishlist';
        } else {
            button.textContent = data.wishlisted ? '♥' : '♡';
        }
    });
};

window.addToCart = function (button) {
    const form = button.closest('form');
    const data = new FormData(form);
    const feedback = form.querySelector('[data-cart-feedback]');

    button.disabled = true;

    axios.post(button.dataset.url, Object.fromEntries(data))
        .then(({ data }) => {
            if (feedback) {
                feedback.textContent = data.status;
                feedback.classList.remove('hidden', 'text-red-600');
                feedback.classList.add('text-green-600');
            }
        })
        .catch((error) => {
            if (feedback) {
                const message = error.response?.data?.message
                    ?? Object.values(error.response?.data?.errors ?? {}).flat()[0]
                    ?? 'Something went wrong.';
                feedback.textContent = message;
                feedback.classList.remove('hidden', 'text-green-600');
                feedback.classList.add('text-red-600');
            }
        })
        .finally(() => {
            button.disabled = false;
        });
};

window.removeCartItem = function (button) {
    const row = button.closest('[data-cart-row]');

    axios.delete(button.dataset.url).then(({ data }) => {
        row.remove();

        const totalEl = document.querySelector('[data-cart-total]');
        const emptyEl = document.querySelector('[data-cart-empty]');
        const summaryEl = document.querySelector('[data-cart-summary]');

        if (data.count === 0) {
            summaryEl?.remove();
            if (emptyEl) emptyEl.classList.remove('hidden');
        } else if (totalEl) {
            totalEl.textContent = `Total: ${data.currency} ${Number(data.total).toFixed(2)}`;
        }
    });
};
