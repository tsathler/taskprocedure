<?php

include '../../../inc/includes.php';

Session::checkLoginUser();
if (!Session::haveRight('config', UPDATE)) {
    Html::displayErrorAndDie(__s('Acesso não autorizado.', 'taskprocedure'));
}

$procedure = new PluginTaskprocedureProcedure();
$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Session::checkCSRF();

    if (isset($_POST['delete'])) {
        $procedure->getFromDB($id);
        $step = new PluginTaskprocedureProcedureStep();
        foreach ($step->find(['plugin_taskprocedure_procedures_id' => $id]) as $row) {
            $step->delete(['id' => (int) $row['id']], true);
        }
        $procedure->delete(['id' => $id], true);
        Html::redirect('procedure.php');
    }

    $input = [
        'name' => trim((string) ($_POST['name'] ?? '')),
        'description' => trim((string) ($_POST['description'] ?? '')),
        'is_active' => isset($_POST['is_active']) ? 1 : 0,
    ];

    if ($input['name'] === '') {
        Session::addMessageAfterRedirect(__s('Informe o nome do procedimento.', 'taskprocedure'), false, ERROR);
    } elseif ($id > 0) {
        $procedure->update(['id' => $id] + $input);
        Html::redirect('procedure.form.php?id=' . $id);
    } else {
        $id = $procedure->add($input);
        Html::redirect('procedure.form.php?id=' . (int) $id);
    }
}

if ($id > 0 && !$procedure->getFromDB($id)) {
    Html::displayErrorAndDie(__s('Procedimento não encontrado.', 'taskprocedure'));
}

Html::header(__s('Procedimentos', 'taskprocedure'), $_SERVER['PHP_SELF'], 'config', 'plugins');

echo '<div class="center"><div class="card">';
echo '<div class="card-header"><div class="card-title h3">'
    . ($id > 0 ? __s('Editar procedimento', 'taskprocedure') : __s('Adicionar procedimento', 'taskprocedure'))
    . '</div></div>';
echo '<div class="card-body">';
echo '<form method="post" action="procedure.form.php' . ($id > 0 ? '?id=' . $id : '') . '">';
echo Html::hidden('id', ['value' => $id]);
echo Html::hidden('_glpi_csrf_token', ['value' => Session::getNewCSRFToken()]);
echo '<div class="mb-3"><label class="form-label" for="name">' . __s('Nome', 'taskprocedure') . '</label>';
echo Html::input('name', ['id' => 'name', 'value' => $procedure->fields['name'] ?? '', 'required' => true]);
echo '</div>';
echo '<div class="mb-3"><label class="form-label" for="description">' . __s('Descrição', 'taskprocedure') . '</label>';
echo '<textarea class="form-control" id="description" name="description" rows="4">'
    . htmlescape((string) ($procedure->fields['description'] ?? '')) . '</textarea></div>';
echo '<label class="form-check"><input class="form-check-input" type="checkbox" name="is_active" value="1"'
    . (!isset($procedure->fields['is_active']) || $procedure->fields['is_active'] ? ' checked' : '')
    . '><span class="form-check-label">' . __s('Ativo', 'taskprocedure') . '</span></label>';
echo '<div class="mt-4"><button class="btn btn-primary" type="submit">'
    . __s('Salvar', 'taskprocedure') . '</button> '
    . '<a class="btn" href="procedure.php">' . __s('Voltar', 'taskprocedure') . '</a></div>';
echo '</form></div></div>';

if ($id > 0) {
    $step = new PluginTaskprocedureProcedureStep();
    $steps = $step->find(
        ['plugin_taskprocedure_procedures_id' => $id],
        ['ORDER' => 'position ASC, id ASC'],
    );

    echo '<div class="card mt-3"><div class="card-header">';
    echo '<div class="card-title h3">' . __s('Etapas', 'taskprocedure') . '</div>';
    echo '<div class="card-actions"><a class="btn btn-primary" href="step.form.php?procedure_id='
        . $id . '"><i class="ti ti-plus me-1"></i>' . __s('Adicionar etapa', 'taskprocedure') . '</a></div>';
    echo '</div><div class="table-responsive"><table class="table card-table">';
    echo '<thead><tr><th>#</th><th>' . __s('Nome', 'taskprocedure') . '</th><th>'
        . __s('Descrição', 'taskprocedure') . '</th><th></th></tr></thead><tbody>';
    foreach ($steps as $stepRow) {
        echo '<tr><td>' . (int) $stepRow['position'] . '</td><td>' . htmlescape($stepRow['name'])
            . '</td><td>' . htmlescape((string) $stepRow['description']) . '</td><td class="text-end">'
            . '<a class="btn btn-sm" href="step.form.php?id=' . (int) $stepRow['id'] . '">'
            . __s('Editar', 'taskprocedure') . '</a></td></tr>';
    }
    if (count($steps) === 0) {
        echo '<tr><td colspan="4">' . __s('Nenhuma etapa cadastrada.', 'taskprocedure') . '</td></tr>';
    }
    echo '</tbody></table></div></div>';

    echo '<form class="mt-3" method="post" action="procedure.form.php?id=' . $id . '">'
        . Html::hidden('id', ['value' => $id])
        . Html::hidden('_glpi_csrf_token', ['value' => Session::getNewCSRFToken()])
        . '<button class="btn btn-danger" type="submit" name="delete" value="1">'
        . __s('Excluir procedimento', 'taskprocedure') . '</button></form>';
}

echo '</div>';
Html::footer();
