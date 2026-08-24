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
            'ti ti-clipboard-check',
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
        $stepsByProcedure = [];
        if (count($assignments) > 0) {
            $assignmentIds = array_map(
                static fn(array $assignment): int => (int) $assignment['id'],
                $assignments,
            );
            foreach ($ticketStep->find(
                ['ticketprocedures_id' => $assignmentIds],
                ['ORDER' => 'position ASC, id ASC'],
            ) as $step) {
                $stepsByProcedure[(int) $step['ticketprocedures_id']][] = $step;
            }
        }
        foreach ($assignments as $assignment) {
            $steps = $stepsByProcedure[(int) $assignment['id']] ?? [];
            $completedSteps = count(array_filter(
                $steps,
                static fn(array $step): bool => (int) $step['is_completed'] === 1,
            ));
            $totalSteps = count($steps);
            $progress = $totalSteps > 0 ? (int) round($completedSteps * 100 / $totalSteps) : 0;

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
            echo '</div>';
            if ($totalSteps > 0) {
                echo '<div class="small text-muted mb-2" data-taskprocedure-progress-label>'
                    . sprintf(__('%1$s/%2$s etapas concluídas', 'taskprocedure'), $completedSteps, $totalSteps)
                    . '</div><div class="progress mb-3" data-taskprocedure-progress role="progressbar" aria-valuenow="' . $progress
                    . '" aria-valuemin="0" aria-valuemax="100"><div class="progress-bar" style="width: '
                    . $progress . '%"></div></div>';
            }
            echo '<ol class="mb-0">';
            foreach ($steps as $stepIndex => $step) {
                echo '<li class="mb-2">';
                if ($item->canUpdateItem()) {
                    echo '<form method="post" action="/plugins/taskprocedure/front/ticketprocedure.form.php"'
                        . ' class="d-inline-flex align-items-start gap-2" data-taskprocedure-ajax>'
                        . '<input type="hidden" name="_glpi_csrf_token" value="'
                        . htmlescape(Session::getNewCSRFToken()) . '">'
                        . '<input type="hidden" name="tickets_id" value="' . $ticketId . '">'
                        . '<input type="hidden" name="ticketprocedure_id" value="'
                        . (int) $assignment['id'] . '">'
                        . '<input type="hidden" name="ticketstep_id" value="' . (int) $step['id'] . '">'
                        . '<input type="hidden" name="update_step" value="1">'
                        . '<input class="form-check-input mt-1" type="checkbox" name="is_completed" value="1"'
                        . ((int) $step['is_completed'] === 1 ? ' checked' : '')
                        . ' data-taskprocedure-checklist onchange="this.form.requestSubmit()" aria-label="'
                        . htmlescape($step['name']) . '">'
                        . '<span><span class="d-block' . ((int) $step['is_completed'] === 1 ? ' text-decoration-line-through text-muted' : '')
                        . '">' . htmlescape($step['name']) . '</span>';
                    if ((string) ($step['description'] ?? '') !== '') {
                        echo '<small class="text-muted">' . htmlescape($step['description']) . '</small>';
                    }
                    echo '</span></form>';
                    echo '<div class="ms-4 mt-1"><details><summary class="small text-muted">'
                        . __s('Comentário e evidência', 'taskprocedure') . '</summary>'
                        . '<form method="post" action="/plugins/taskprocedure/front/ticketprocedure.form.php" class="mt-2">'
                        . '<input type="hidden" name="_glpi_csrf_token" value="'
                        . htmlescape(Session::getNewCSRFToken()) . '">'
                        . '<input type="hidden" name="tickets_id" value="' . $ticketId . '">'
                        . '<input type="hidden" name="ticketprocedure_id" value="' . (int) $assignment['id'] . '">'
                        . '<input type="hidden" name="ticketstep_id" value="' . (int) $step['id'] . '">'
                        . '<input type="hidden" name="update_step_details" value="1">'
                        . '<label class="form-label small" for="taskprocedure-comment-' . (int) $step['id'] . '">'
                        . __s('Comentário', 'taskprocedure') . '</label>'
                        . '<textarea class="form-control form-control-sm mb-2" rows="2" name="comment" id="taskprocedure-comment-'
                        . (int) $step['id'] . '">' . htmlescape((string) ($step['comment'] ?? '')) . '</textarea>'
                        . '<label class="form-label small" for="taskprocedure-evidence-' . (int) $step['id'] . '">'
                        . __s('Evidência (link ou referência)', 'taskprocedure') . '</label>'
                        . '<input class="form-control form-control-sm mb-2" name="evidence" id="taskprocedure-evidence-'
                        . (int) $step['id'] . '" value="' . htmlescape((string) ($step['evidence'] ?? '')) . '">'
                        . '<button class="btn btn-sm btn-secondary" type="submit">'
                        . __s('Salvar detalhes', 'taskprocedure') . '</button></form></details></div>';
                    echo '<div class="ms-4 mt-1 d-flex gap-1">';
                    foreach (['up' => __s('Subir', 'taskprocedure'), 'down' => __s('Descer', 'taskprocedure')] as $direction => $label) {
                        $disabled = ($direction === 'up' && $stepIndex === 0)
                            || ($direction === 'down' && $stepIndex === count($steps) - 1);
                        echo '<form method="post" action="/plugins/taskprocedure/front/ticketprocedure.form.php">'
                            . '<input type="hidden" name="_glpi_csrf_token" value="'
                            . htmlescape(Session::getNewCSRFToken()) . '">'
                            . '<input type="hidden" name="tickets_id" value="' . $ticketId . '">'
                            . '<input type="hidden" name="ticketprocedure_id" value="' . (int) $assignment['id'] . '">'
                            . '<input type="hidden" name="ticketstep_id" value="' . (int) $step['id'] . '">'
                            . '<button class="btn btn-sm btn-ghost-secondary" type="submit" name="move_step" value="'
                            . $direction . '"' . ($disabled ? ' disabled' : '') . '>' . $label . '</button></form>';
                    }
                    echo '</div>';
                } else {
                    echo '<span class="me-2">' . ((int) $step['is_completed'] === 1 ? '☑' : '☐')
                        . '</span>' . htmlescape($step['name']);
                }
                echo '</li>';
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
