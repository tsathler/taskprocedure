<?php

include '../../../inc/includes.php';

Session::checkLoginUser();
if (!Session::haveRight('config', UPDATE)) {
    Html::displayErrorAndDie(__s('Acesso não autorizado.', 'taskprocedure'));
}

$procedure = new PluginTaskprocedureProcedure();
$rows = $procedure->find([], ['ORDER' => 'name ASC']);

Html::header(__s('Procedimentos', 'taskprocedure'), $_SERVER['PHP_SELF'], 'config', 'plugins');

echo '<div class="center">';
echo '<div class="card">';
echo '<div class="card-header">';
echo '<div class="card-title h3">' . __s('Procedimentos', 'taskprocedure') . '</div>';
echo '<div class="card-actions">';
echo '<a class="btn btn-primary" href="procedure.form.php">';
echo '<i class="ti ti-plus me-1"></i>' . __s('Adicionar', 'taskprocedure');
echo '</a>';
echo '</div></div>';

if (count($rows) === 0) {
    echo '<div class="card-body">';
    echo '<p class="mb-0">' . __s('Nenhum procedimento cadastrado.', 'taskprocedure') . '</p>';
    echo '</div>';
} else {
    echo '<div class="table-responsive"><table class="table card-table table-vcenter">';
    echo '<thead><tr>';
    echo '<th>' . __s('Nome', 'taskprocedure') . '</th>';
    echo '<th>' . __s('Etapas', 'taskprocedure') . '</th>';
    echo '<th>' . __s('Status', 'taskprocedure') . '</th>';
    echo '<th>' . __s('Última alteração', 'taskprocedure') . '</th>';
    echo '<th class="text-end">' . __s('Ações', 'taskprocedure') . '</th>';
    echo '</tr></thead><tbody>';

    $step = new PluginTaskprocedureProcedureStep();
    foreach ($rows as $row) {
        $steps = count($step->find(['plugin_taskprocedure_procedures_id' => $row['id']]));
        $status = ((int) $row['is_active'] === 1)
            ? '<span class="badge bg-green-lt">' . __s('Ativo', 'taskprocedure') . '</span>'
            : '<span class="badge bg-secondary-lt">' . __s('Inativo', 'taskprocedure') . '</span>';

        echo '<tr>';
        echo '<td><a href="procedure.form.php?id=' . (int) $row['id'] . '">'
            . htmlescape($row['name']) . '</a></td>';
        echo '<td>' . (int) $steps . '</td>';
        echo '<td>' . $status . '</td>';
        echo '<td>' . htmlescape((string) ($row['date_mod'] ?? $row['date_creation'] ?? '')) . '</td>';
        echo '<td class="text-end"><a class="btn btn-sm" href="procedure.form.php?id='
            . (int) $row['id'] . '">' . __s('Editar', 'taskprocedure') . '</a></td>';
        echo '</tr>';
    }

    echo '</tbody></table></div>';
}

echo '</div></div>';
Html::footer();
