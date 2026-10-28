import './bootstrap';

function syncTableSelection(shell) {
    const checkboxes = shell.querySelectorAll('[data-row-select]');
    const master = shell.querySelector('[data-master-select]');
    const toolbar = shell.querySelector('[data-bulk-toolbar]');
    const counter = shell.querySelector('[data-bulk-count]');
    const template = shell.querySelector('template[data-selection-template]');

    if (!checkboxes.length || !master) {
        return;
    }

    const selected = [...checkboxes].filter((item) => item.checked);
    const selectedCount = selected.length;

    master.checked = selectedCount === checkboxes.length;
    master.indeterminate = selectedCount > 0 && selectedCount < checkboxes.length;

    if (toolbar && counter) {
        counter.textContent = String(selectedCount);
        toolbar.hidden = selectedCount === 0;

        toolbar.querySelectorAll('input[data-dynamic-id]').forEach((input) => input.remove());

        if (template) {
            selected.forEach((item) => {
                const html = template.innerHTML.replace('__VALUE__', item.value);
                const holder = document.createElement('div');
                holder.innerHTML = html;
                const input = holder.firstElementChild;
                if (input) {
                    input.setAttribute('data-dynamic-id', '1');
                    toolbar.appendChild(input);
                }
            });
        }
    }
}

document.querySelectorAll('[data-table-shell]').forEach((shell) => {
    const master = shell.querySelector('[data-master-select]');
    const checkboxes = shell.querySelectorAll('[data-row-select]');

    if (master) {
        master.addEventListener('change', () => {
            checkboxes.forEach((item) => {
                item.checked = master.checked;
            });
            syncTableSelection(shell);
        });
    }

    checkboxes.forEach((item) => {
        item.addEventListener('change', () => syncTableSelection(shell));
    });

    syncTableSelection(shell);
});

const shellRoot = document.querySelector('[data-shell-root]');
const sidebar = document.querySelector('[data-sidebar]');
const sidebarToggle = document.querySelector('[data-sidebar-toggle]');

if (shellRoot && sidebar && sidebarToggle) {
    sidebarToggle.addEventListener('click', () => {
        shellRoot.classList.toggle('sidebar-open');
    });

    sidebar.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', () => {
            shellRoot.classList.remove('sidebar-open');
        });
    });
}

const themeToggleButtons = document.querySelectorAll('[data-theme-toggle]');
const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
const savedTheme = localStorage.getItem('wm-theme');
const initialTheme = savedTheme || (prefersDark ? 'dark' : 'light');

document.body.setAttribute('data-theme', initialTheme);

themeToggleButtons.forEach((button) => {
    button.addEventListener('click', () => {
        const nextTheme = document.body.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
        document.body.setAttribute('data-theme', nextTheme);
        localStorage.setItem('wm-theme', nextTheme);
    });
});
