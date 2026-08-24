<?php

/** Ticket procedure association and checklist tab. */
class PluginTaskprocedureTicketProcedure extends CommonDBTM
{
    public static function getTable($classname = null): string
    {
        return 'glpi_plugin_taskprocedure_ticketprocedures';
    }

    public static function getForeignKeyField(): string
    {
        return 'tickets_id';
    }

    public static function getTypeName($nb = 0): string
    {
        return _n('Procedure', 'Procedures', $nb, 'taskprocedure');
    }

    public function getTabNameForItem(CommonGLPI $item, $withtemplate = 0)
    {
        if (!($item instanceof Ticket)) {
            return '';
        }

        return [1 => self::createTabEntry(
            __s('Procedimentos', 'taskprocedure'),
            0,
            Ticket::class,
            'ti ti-list-check',
        )];
    }

    public static function displayTabContentForItem(
        CommonGLPI $item,
        $tabnum = 1,
        $withtemplate = 0
    ): bool {
        if (!($item instanceof Ticket)) {
            return false;
        }

        $ticketId = (int) $item->getID();
        $ticketProcedure = new self();
        $assignments = $ticketProcedure->find(
            ['tickets_id' => $ticketId],
            ['ORDER' => 'id ASC'],
        );

        echo '<div class="card card-sm"><div class="card-header"><div class="card-title h3">'
            . __s('Procedimentos', 'taskprocedure') . '</div></div><div class="card-body">';
        if (count($assignments) === 0) {
            echo '<p>' . __s('Nenhum procedimento associado a este chamado.', 'taskprocedure') . '</p>';
        }

        $ticketStep = new PluginTaskprocedureTicketStep();
        foreach ($assignments as $assignment) {
            echo '<div class="border rounded p-3 mb-3"><div class="d-flex justify-content-between">'
                . '<h4 class="mb-2">' . htmlescape($assignment['procedure_name']) . '</h4>';
            if ($item->canUpdateItem()) {
                echo '<form method="post" action="/plugins/taskprocedure/front/ticketprocedure.form.php"'
                    . ' onsubmit="return confirm(\''
                    . __s('Remover este procedimento do chamado?', 'taskprocedure') . '\');">'
                    . '<input type="hidden" name="_glpi_csrf_token" value="'
                    . htmlescape(Session::getNewCSRFToken()) . '">'
                    . '<input type="hidden" name="tickets_id" value="' . $ticketId . '">'
                    . '<input type="hidden" name="ticketprocedure_id" value="'
                    . (int) $assignment['id'] . '">'
                    . '<button class="btn btn-sm btn-outline-danger" type="submit" name="delete" value="1">'
                    . __s('Remover', 'taskprocedure') . '</button></form>';
            }
            echo '</div><ol class="mb-0">';
            $steps = $ticketStep->find(
                ['ticketprocedures_id' => (int) $assignment['id']],
                ['ORDER' => 'position ASC, id ASC'],
            );
            foreach ($steps as $step) {
                echo '<li>' . htmlescape($step['name']) . '</li>';
            }
            if (count($steps) === 0) {
                echo '<li>' . __s('Nenhuma etapa cadastrada.', 'taskprocedure') . '</li>';
            }
            echo '</ol></div>';
        }

        $procedure = new PluginTaskprocedureProcedure();
        $available = $procedure->find(['is_active' => 1], ['ORDER' => 'name ASC']);
        if ($item->canUpdateItem() && count($available) > 0) {
            echo '<hr><form method="post" action="/plugins/taskprocedure/front/ticketprocedure.form.php">';
            echo '<input type="hidden" name="_glpi_csrf_token" value="'
                . htmlescape(Session::getNewCSRFToken()) . '">';
            echo '<input type="hidden" name="tickets_id" value="' . $ticketId . '">';
            echo '<label class="form-label" for="taskprocedure_id">'
                . __s('Adicionar procedimento', 'taskprocedure') . '</label>';
            echo '<div class="d-flex gap-2"><select class="form-select" id="taskprocedure_id"'
                . ' name="procedure_id" required><option value="">'
                . __s('Selecione...', 'taskprocedure') . '</option>';
            foreach ($available as $row) {
                echo '<option value="' . (int) $row['id'] . '">'
                    . htmlescape($row['name']) . '</option>';
            }
            echo '</select><button class="btn btn-primary" type="submit">'
                . __s('Adicionar', 'taskprocedure') . '</button></div></form>';
        }

        echo '</div></div>';
        return true;
    }
}
