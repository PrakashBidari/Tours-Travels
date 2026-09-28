/**
 * Any <div data-nav-menu-builder data-reorder-url="..."> containing nested
 * <div class="menu-sortable-list" data-parent="..."> lists of <div class="menu-item"
 * data-id="..."> rows becomes a drag-and-drop nested menu builder. Dragging a row
 * (via its .drag-handle) into another row's ".item-children" list re-parents it;
 * dropping anywhere triggers an auto-save that POSTs the whole tree as nested
 * {id, children: [...]} objects to data-reorder-url. The .indent-btn/.outdent-btn
 * buttons on each row offer the same re-parenting without dragging.
 */
function serializeMenu(container) {
    const items = [];

    Array.from(container.children).forEach((el) => {
        if (!el.classList.contains('menu-item')) {
            return;
        }

        const childContainer = el.querySelector(':scope > .item-children');

        items.push({
            id: parseInt(el.dataset.id, 10),
            children: childContainer ? serializeMenu(childContainer) : [],
        });
    });

    return items;
}

function initNavMenuBuilder(root) {
    const reorderUrl = root.dataset.reorderUrl;
    const sortables = new Map();
    let saveTimer = null;

    function autoSave() {
        clearTimeout(saveTimer);

        saveTimer = setTimeout(() => {
            const rootList = root.querySelector('.menu-sortable-list[data-parent="0"]');
            const order = serializeMenu(rootList);

            fetch(reorderUrl, {
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
                    window.AppAlert?.success('Menu order saved.');
                })
                .catch(() => window.AppAlert?.error('Could not save the menu order, please try again.'));
        }, 500);
    }

    function initAllSortables() {
        sortables.forEach((instance) => instance.destroy());
        sortables.clear();

        root.querySelectorAll('.menu-sortable-list').forEach((el) => {
            const instance = window.Sortable.create(el, {
                group: { name: 'nav-menu', put: true, pull: true },
                handle: '.drag-handle',
                animation: 180,
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                onEnd: () => autoSave(),
            });
            sortables.set(el, instance);
        });
    }

    function ensureChildrenContainer(itemEl) {
        let container = itemEl.querySelector(':scope > .item-children');

        if (!container) {
            container = document.createElement('div');
            container.className = 'item-children menu-sortable-list';
            container.dataset.parent = itemEl.dataset.id;
            itemEl.appendChild(container);
        }

        return container;
    }

    function indentItem(itemEl) {
        let prev = itemEl.previousElementSibling;
        while (prev && !prev.classList.contains('menu-item')) {
            prev = prev.previousElementSibling;
        }
        if (!prev) {
            return;
        }

        ensureChildrenContainer(prev).appendChild(itemEl);
        initAllSortables();
        autoSave();
    }

    function outdentItem(itemEl) {
        const currentList = itemEl.parentElement;
        if (!currentList || currentList.dataset.parent === '0') {
            return;
        }

        const parentItem = currentList.closest('.menu-item');
        if (!parentItem) {
            return;
        }

        const grandList = parentItem.parentElement;
        grandList.insertBefore(itemEl, parentItem.nextElementSibling);
        initAllSortables();
        autoSave();
    }

    root.addEventListener('click', (event) => {
        const indentBtn = event.target.closest('.indent-btn');
        if (indentBtn) {
            indentItem(indentBtn.closest('.menu-item'));
            return;
        }

        const outdentBtn = event.target.closest('.outdent-btn');
        if (outdentBtn) {
            outdentItem(outdentBtn.closest('.menu-item'));
        }
    });

    initAllSortables();
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-nav-menu-builder]').forEach(initNavMenuBuilder);
});
