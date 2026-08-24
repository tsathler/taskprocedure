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
    new MutationObserver(moveProceduresTab).observe(document.documentElement, {childList: true, subtree: true});
}());
