import './bootstrap';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';

/* ---------- Theme (light / dark) ---------- */
const root = document.documentElement;

function applyTheme(theme) {
    root.setAttribute('data-bs-theme', theme);
    localStorage.setItem('wm-theme', theme);
    document.querySelectorAll('[data-theme-toggle]').forEach((btn) => {
        btn.setAttribute('aria-pressed', theme === 'dark' ? 'true' : 'false');
        btn.setAttribute('title', theme === 'dark' ? 'Switch to light mode' : 'Switch to dark mode');
    });
    document.dispatchEvent(new CustomEvent('wm:themechange', { detail: { theme } }));
}

document.querySelectorAll('[data-theme-toggle]').forEach((btn) => {
    btn.addEventListener('click', () => {
        applyTheme(root.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark');
    });
});

applyTheme(root.getAttribute('data-bs-theme') || 'light');

/* ---------- Chart.js theming helper ---------- */
window.wmCssVar = (name) => getComputedStyle(root).getPropertyValue(name).trim();

window.wmApplyChartTheme = () => {
    if (!window.Chart) return;
    window.Chart.defaults.color = window.wmCssVar('--wm-muted');
    window.Chart.defaults.borderColor = window.wmCssVar('--wm-border');
    window.Chart.defaults.font.family = "'Inter', system-ui, sans-serif";
};

/* ---------- Sidebar (mobile drawer) ---------- */
const shell = document.querySelector('[data-shell-root]');

if (shell) {
    const close = () => shell.classList.remove('sidebar-open');

    document.querySelector('[data-sidebar-toggle]')?.addEventListener('click', () => {
        shell.classList.toggle('sidebar-open');
    });
    shell.querySelector('[data-sidebar-backdrop]')?.addEventListener('click', close);
    shell.querySelectorAll('[data-sidebar] a').forEach((a) => a.addEventListener('click', close));
    document.addEventListener('keydown', (e) => e.key === 'Escape' && close());
}

/* ---------- Data tables: selection, bulk bar, exports ---------- */
const isVisible = (row) => row.style.display !== 'none';

function selectableBoxes(tableShell) {
    return [...tableShell.querySelectorAll('[data-row-select]')].filter((cb) => isVisible(cb.closest('tr')));
}

function syncSelection(tableShell) {
    const master = tableShell.querySelector('[data-master-select]');
    const toolbar = tableShell.querySelector('[data-bulk-toolbar]');
    const counter = tableShell.querySelector('[data-bulk-count]');
    const template = tableShell.querySelector('template[data-selection-template]');

    tableShell.querySelectorAll('[data-row-select]').forEach((cb) => {
        const row = cb.closest('tr');
        if (!isVisible(row)) cb.checked = false;
        row.classList.toggle('is-selected', cb.checked);
    });

    const boxes = selectableBoxes(tableShell);
    const selected = boxes.filter((cb) => cb.checked);

    if (master) {
        master.checked = boxes.length > 0 && selected.length === boxes.length;
        master.indeterminate = selected.length > 0 && selected.length < boxes.length;
    }

    if (!toolbar) return;

    if (counter) counter.textContent = selected.length.toLocaleString('en-US');
    toolbar.hidden = selected.length === 0;

    toolbar.querySelectorAll('input[data-dynamic-id]').forEach((el) => el.remove());

    if (template) {
        selected.forEach((cb) => {
            const holder = document.createElement('div');
            holder.innerHTML = template.innerHTML.replace('__VALUE__', cb.value);
            const input = holder.firstElementChild;
            if (input) {
                input.setAttribute('data-dynamic-id', '1');
                toolbar.appendChild(input);
            }
        });
    }
}

function exportRows(tableShell) {
    const table = tableShell.querySelector('table');
    const headCells = [...table.querySelectorAll('thead th')];
    const keep = headCells.map((th) => !th.hasAttribute('data-export-skip'));
    const clean = (el) => el.textContent.replace(/\s+/g, ' ').trim();

    const header = headCells.filter((_, i) => keep[i]).map(clean);
    const rows = selectableBoxes(tableShell)
        .filter((cb) => cb.checked)
        .map((cb) => [...cb.closest('tr').children].filter((_, i) => keep[i]).map(clean));

    return { header, rows };
}

function downloadCsv(tableShell) {
    const { header, rows } = exportRows(tableShell);
    const esc = (v) => `"${String(v).replace(/"/g, '""')}"`;
    const csv = [header, ...rows].map((r) => r.map(esc).join(',')).join('\r\n');
    const blob = new Blob(['\ufeff' + csv], { type: 'text/csv;charset=utf-8' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = `telemetry-readings-${new Date().toISOString().slice(0, 10)}.csv`;
    document.body.appendChild(link);
    link.click();
    link.remove();
    URL.revokeObjectURL(link.href);
}

function printRows(tableShell) {
    const { header, rows } = exportRows(tableShell);
    const win = window.open('', '_blank');
    if (!win) return;

    const doc = win.document;
    doc.title = 'Telemetry readings';
    const style = doc.createElement('style');
    style.textContent = 'body{font:13px/1.4 system-ui,sans-serif;margin:24px;color:#0f172a}h1{font-size:18px;margin:0 0 4px}p{margin:0 0 16px;color:#475569}table{border-collapse:collapse;width:100%}th,td{border:1px solid #cbd5e1;padding:6px 8px;text-align:left}th{background:#f1f5f9}';
    doc.head.appendChild(style);

    const title = doc.createElement('h1');
    title.textContent = 'Telemetry readings';
    const meta = doc.createElement('p');
    meta.textContent = `${rows.length} record(s) - ${new Date().toLocaleString()}`;
    const table = doc.createElement('table');
    const addRow = (cells, tag) => {
        const tr = doc.createElement('tr');
        cells.forEach((text) => {
            const cell = doc.createElement(tag);
            cell.textContent = text;
            tr.appendChild(cell);
        });
        table.appendChild(tr);
    };
    addRow(header, 'th');
    rows.forEach((r) => addRow(r, 'td'));
    doc.body.append(title, meta, table);
    win.focus();
    win.print();
}

document.querySelectorAll('[data-table-shell]').forEach((tableShell) => {
    const master = tableShell.querySelector('[data-master-select]');
    const toolbar = tableShell.querySelector('[data-bulk-toolbar]');

    master?.addEventListener('change', () => {
        selectableBoxes(tableShell).forEach((cb) => (cb.checked = master.checked));
        syncSelection(tableShell);
    });

    tableShell.addEventListener('change', (e) => {
        if (e.target.matches('[data-row-select]')) syncSelection(tableShell);
    });

    tableShell.addEventListener('wm:rows-changed', () => syncSelection(tableShell));

    toolbar?.querySelector('[data-bulk-clear]')?.addEventListener('click', () => {
        tableShell.querySelectorAll('[data-row-select]').forEach((cb) => (cb.checked = false));
        syncSelection(tableShell);
    });

    toolbar?.querySelectorAll('[data-export]').forEach((btn) => {
        btn.addEventListener('click', () => {
            if (btn.dataset.export === 'csv') downloadCsv(tableShell);
            else printRows(tableShell);
        });
    });

    if (toolbar?.tagName === 'FORM') {
        toolbar.addEventListener('submit', (e) => {
            const action = toolbar.querySelector('[name="action"]')?.value;
            const count = toolbar.querySelectorAll('input[data-dynamic-id]').length;
            if (action === 'delete' && !window.confirm(`Delete ${count} selected record(s)? This cannot be undone.`)) {
                e.preventDefault();
            }
        });
    }

    syncSelection(tableShell);
});
