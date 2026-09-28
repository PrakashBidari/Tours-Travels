<div class="menu-item" data-id="{{ $item->id }}">
    <div class="menu-item-row flex flex-wrap items-center gap-2 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-2">
        <span class="drag-handle cursor-move shrink-0 text-gray-300 dark:text-gray-600 hover:text-gray-500 dark:hover:text-gray-400" title="Drag to reorder / nest">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9h16.5m-16.5 6h16.5" />
            </svg>
        </span>

        <form method="POST" action="{{ route('dashboard.header-settings.menu-items.update', $item) }}" class="flex flex-1 flex-wrap items-center gap-1.5 min-w-0">
            @csrf
            @method('PUT')
            <input type="text" name="label" value="{{ $item->label }}" placeholder="Label" class="w-32 min-w-0 border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 rounded-md shadow-sm text-xs">
            <input type="text" name="url" value="{{ $item->url }}" placeholder="URL" class="flex-1 min-w-[140px] border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 rounded-md shadow-sm text-xs">
            <select name="target" class="border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 rounded-md shadow-sm text-xs">
                <option value="_self" @selected($item->target === '_self')>Same tab</option>
                <option value="_blank" @selected($item->target === '_blank')>New tab</option>
            </select>
            <button type="submit" class="text-xs font-medium text-indigo-600 hover:text-indigo-700 shrink-0">Save</button>
        </form>

        <button type="button" class="outdent-btn shrink-0 h-7 w-7 flex items-center justify-center rounded-md text-gray-500 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700/60" title="Outdent (move up a level)">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
        </button>
        <button type="button" class="indent-btn shrink-0 h-7 w-7 flex items-center justify-center rounded-md text-gray-500 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700/60" title="Indent (nest under previous item)">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
        </button>

        <form method="POST" action="{{ route('dashboard.header-settings.menu-items.destroy', $item) }}" data-confirm="Delete this menu item and its submenu items?" class="shrink-0">
            @csrf
            @method('DELETE')
            <button type="submit" class="text-xs font-medium text-red-600 hover:text-red-700">Delete</button>
        </form>
    </div>

    <div class="item-children menu-sortable-list" data-parent="{{ $item->id }}">
        @foreach ($item->children as $child)
            @include('backend.header-settings.partials.menu-item', ['item' => $child])
        @endforeach
    </div>
</div>
