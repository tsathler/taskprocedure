(function () {
    'use strict';

    function moveProceduresTab() {
        const links = Array.from(document.querySelectorAll('a'));
        const procedures = links.find((link) => link.textContent.trim().startsWith('Procedimentos'));
        const ticket = links.find((link) => link.textContent.trim().startsWith('Chamado'));
        if (!procedures || !ticket) return;

        const procedureItem = procedures.closest('li, [role="tab"], .nav-item') || procedures.parentElement;
        const ticketItem = ticket.closest('li, [role="tab"], .nav-item') || ticket.parentElement;
        if (procedureItem && ticketItem && ticketItem.parentElement === procedureItem.parentElement) {
            ticketItem.parentElement.insertBefore(procedureItem, ticketItem.nextSibling);
        }
    }

    document.addEventListener('DOMContentLoaded', moveProceduresTab);
    if (document.documentElement) {
        new MutationObserver(moveProceduresTab).observe(document.documentElement, {childList: true, subtree: true});
    } else {
        document.addEventListener('DOMContentLoaded', () => {
            new MutationObserver(moveProceduresTab).observe(document.documentElement, {childList: true, subtree: true});
        }, {once: true});
    }

    function refreshProgress(form, completed, total) {
        const card = form.closest('.border');
        if (!card) return;

        const label = card.querySelector('[data-taskprocedure-progress-label]');
        const progress = card.querySelector('[data-taskprocedure-progress]');
        const percentage = total > 0 ? Math.round(completed * 100 / total) : 0;
        if (label) label.textContent = `${completed}/${total} etapas concluídas`;
        if (progress) {
            progress.setAttribute('aria-valuenow', percentage);
            const bar = progress.querySelector('.progress-bar');
            if (bar) bar.style.width = `${percentage}%`;
        }
    }

    document.addEventListener('submit', (event) => {
        const form = event.target.closest('form[data-taskprocedure-ajax]');
        if (!form) return;
        event.preventDefault();

        const checkbox = form.querySelector('[data-taskprocedure-checklist]');
        if (!checkbox) return;
        const data = new FormData(form);
        data.set('ajax', '1');
        checkbox.disabled = true;
        fetch(form.action, {
            method: 'POST',
            body: data,
            credentials: 'same-origin',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-Glpi-Csrf-Token': data.get('_glpi_csrf_token'),
            },
        })
            .then((response) => response.ok ? response.json() : Promise.reject(response))
            .then((result) => {
                if (!result.success) throw new Error('Checklist update failed');
                refreshProgress(form, result.completed, result.total);
                const text = form.querySelector('span .d-block');
                if (text) text.classList.toggle('text-decoration-line-through', checkbox.checked);
                if (text) text.classList.toggle('text-muted', checkbox.checked);
            })
            .catch(() => window.location.reload())
            .finally(() => { checkbox.disabled = false; });
    });
}());
