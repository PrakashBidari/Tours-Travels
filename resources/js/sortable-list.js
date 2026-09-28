/**
 * Any <ul data-sortable-list data-sortable-url="..."> with draggable <li data-id="...">
 * children becomes a drag-to-reorder list. Reordering (by drag, or via the up/down/top/bottom
 * buttons wired up through window.reorderListItem, see backend/home-order/index.blade.php)
 * POSTs the new top-to-bottom order of data-id values as {order: [...]} to data-sortable-url.
 *
 * A <li> may also carry data-position-badge on a child element; its text is kept in sync with
 * the item's 1-based position after every reorder, without waiting on a page reload.
 */
function renumber(list) {
    Array.from(list.querySelectorAll('[data-id]')).forEach((el, index) => {
        const badge = el.querySelector('[data-position-badge]');
        if (badge) {
            badge.textContent = '#' + (index + 1);
        }
    });
}

function saveOrder(list) {
    renumber(list);

    const order = Array.from(list.querySelectorAll('[data-id]')).map((el) => el.dataset.id);

    fetch(list.dataset.sortableUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
            Accept: 'application/json',
        },
        body: JSON.stringify({ order }),
    })
        .then((response) => {
            if (!response.ok) {
                throw new Error('Request failed');
            }
            window.AppAlert?.success('Order saved.');
        })
        .catch(() => window.AppAlert?.error('Could not save the new order, please try again.'));
}

function initSortableList(list) {
    let dragEl = null;

    list.addEventListener('dragstart', (event) => {
        dragEl = event.target.closest('[data-id]');
        event.dataTransfer.effectAllowed = 'move';
    });

    list.addEventListener('dragover', (event) => {
        event.preventDefault();

        const target = event.target.closest('[data-id]');
        if (!target || target === dragEl || !list.contains(target)) {
            return;
        }

        const rect = target.getBoundingClientRect();
        const insertAfter = (event.clientY - rect.top) / rect.height > 0.5;
        list.insertBefore(dragEl, insertAfter ? target.nextSibling : target);
    });

    list.addEventListener('drop', (event) => event.preventDefault());

    list.addEventListener('dragend', () => {
        if (!dragEl) {
            return;
        }
        dragEl = null;
        saveOrder(list);
    });
}

/**
 * Moves the <li data-id="id"> within `list` one step up/down, or to the very top/bottom, then
 * persists the new order. Called from the per-item up/down/top/bottom popover buttons.
 */
function moveListItem(list, id, direction) {
    const item = list.querySelector(`[data-id="${CSS.escape(String(id))}"]`);
    if (!item) {
        return;
    }

    switch (direction) {
        case 'up': {
            const prev = item.previousElementSibling;
            if (prev) {
                list.insertBefore(item, prev);
            }
            break;
        }
        case 'down': {
            const next = item.nextElementSibling;
            if (next) {
                list.insertBefore(next, item);
            }
            break;
        }
        case 'top':
            list.insertBefore(item, list.firstElementChild);
            break;
        case 'bottom':
            list.appendChild(item);
            break;
    }

    saveOrder(list);
}

window.reorderListItem = (list, id, direction) => moveListItem(list, id, direction);

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-sortable-list]').forEach(initSortableList);

    /**
     * Any <input data-list-search="#listId"> filters that list's <li data-name="..."> items
     * as you type (simple substring match, case-insensitive).
     */
    document.querySelectorAll('[data-list-search]').forEach((input) => {
        const list = document.querySelector(input.dataset.listSearch);
        if (!list) {
            return;
        }

        input.addEventListener('input', () => {
            const term = input.value.trim().toLowerCase();

            list.querySelectorAll('[data-id]').forEach((item) => {
                const matches = term === '' || (item.dataset.name || '').includes(term);
                item.classList.toggle('hidden', !matches);
            });
        });
    });
});
