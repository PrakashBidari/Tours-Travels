import $ from 'jquery';
import DataTable from 'datatables.net-dt';

// datatables.net-dt only self-attaches to a *global* window.jQuery it finds while its own
// module is evaluating — which happens before the `window.jQuery = $` line below ever runs
// (ES module imports always finish evaluating before this file's own statements do). Binding
// explicitly via `.use()` avoids depending on that timing and reliably wires $.fn.dataTable.
DataTable.use($);

window.$ = window.jQuery = $;

/**
 * Any <table data-datatable> gets client-side pagination, a search box and
 * column sorting for free. Mark a <th data-no-sort> to disable sorting on it
 * (e.g. an Actions column), and <th data-filter data-filter-label="Status">
 * to get an auto-populated dropdown filter for that column injected next to
 * the search box. Set <table data-empty-message="..."> to customize the
 * message shown when the table has zero rows (the <tbody> should contain no
 * placeholder row of its own — DataTables reads cells by position and a
 * colspan'd placeholder row throws off every column after it).
 */
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('table[data-datatable]').forEach((table) => {
        const $table = $(table);
        const columnDefs = [];
        const filterColumns = [];

        $table.find('thead th').each((index, th) => {
            if (th.hasAttribute('data-no-sort')) {
                columnDefs.push({ targets: index, orderable: false });
            }

            if (th.hasAttribute('data-filter')) {
                filterColumns.push({ index, label: th.dataset.filterLabel || th.textContent.trim() });
            }
        });

        const dt = $table.DataTable({
            pageLength: 10,
            lengthMenu: [5, 10, 25, 50, 100],
            columnDefs,
            language: {
                search: '',
                searchPlaceholder: 'Search…',
                lengthMenu: 'Show _MENU_ per page',
                info: 'Showing _START_–_END_ of _TOTAL_',
                infoEmpty: 'No entries to show',
                infoFiltered: '(filtered from _MAX_ total)',
                zeroRecords: 'No matching records found',
                emptyTable: table.dataset.emptyMessage || 'No records found.',
                paginate: { previous: '‹ Prev', next: 'Next ›' },
            },
        });

        filterColumns.forEach((filterColumn) => {
            const column = dt.column(filterColumn.index);
            const select = $(
                `<select class="dt-status-filter"><option value="">All ${filterColumn.label}</option></select>`
            );

            column
                .data()
                .unique()
                .sort()
                .each((value) => {
                    const text = $('<div>').html(value).text().trim();
                    if (text && select.find(`option[value="${CSS.escape(text)}"]`).length === 0) {
                        select.append(`<option value="${text}">${text}</option>`);
                    }
                });

            select.on('change', function () {
                const value = $.fn.dataTable.util.escapeRegex(this.value);
                column.search(value ? `^${value}$` : '', true, false).draw();
            });

            const wrapperFilter = $table.closest('.dt-container, .dataTables_wrapper').find('.dt-search, .dataTables_filter');
            wrapperFilter.append(select);
        });
    });
});
