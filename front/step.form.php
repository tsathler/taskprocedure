<?php

include '../../../inc/includes.php';

Session::checkLoginUser();
if (!Session::haveRight('config', UPDATE)) {
    Html::displayErrorAndDie(__s('Acesso não autorizado.', 'taskprocedure'));
}

$step = new PluginTaskprocedureProcedureStep();
$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
$procedureId = (int) ($_GET['procedure_id'] ?? $_POST['plugin_taskprocedure_procedures_id'] ?? 0);

if ($id > 0 && !$step->getFromDB($id)) {
    Html::displayErrorAndDie(__s('Etapa não encontrada.', 'taskprocedure'));
}
$procedureId = $procedureId ?: (int) ($step->fields['plugin_taskprocedure_procedures_id'] ?? 0);

$procedure = new PluginTaskprocedureProcedure();
if (!$procedure->getFromDB($procedureId)) {
    Html::displayErrorAndDie(__s('Procedimento não encontrado.', 'taskprocedure'));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete'])) {
        $step->delete(['id' => $id], true);
        Html::redirect('procedure.form.php?id=' . $procedureId);
    }

    $input = [
        'plugin_taskprocedure_procedures_id' => $procedureId,
        'name' => trim((string) ($_POST['name'] ?? '')),
        'description' => trim((string) ($_POST['description'] ?? '')),
        'position' => max(1, (int) ($_POST['position'] ?? 1)),
        'is_active' => isset($_POST['is_active']) ? 1 : 0,
    ];
    if ($input['name'] === '') {
        Session::addMessageAfterRedirect(__s('Informe o nome da etapa.', 'taskprocedure'), false, ERROR);
    } elseif ($id > 0) {
        $step->update(['id' => $id] + $input);
        Html::redirect('procedure.form.php?id=' . $procedureId);
    } else {
        $step->add($input);
        Html::redirect('procedure.form.php?id=' . $procedureId);
    }
}

Html::header(__s('Etapas', 'taskprocedure'), $_SERVER['PHP_SELF'], 'config', 'plugins');
echo '<div class="center"><div class="card"><div class="card-header"><div class="card-title h3">'
    . ($id > 0 ? __s('Editar etapa', 'taskprocedure') : __s('Adicionar etapa', 'taskprocedure'))
    . '</div></div><div class="card-body">';
echo '<form method="post" action="step.form.php' . ($id > 0 ? '?id=' . $id : '?procedure_id=' . $procedureId) . '">';
echo Html::hidden('id', ['value' => $id]);
echo Html::hidden('plugin_taskprocedure_procedures_id', ['value' => $procedureId]);
echo '<input type="hidden" name="_glpi_csrf_token" value="'
    . htmlescape(Session::getNewCSRFToken()) . '">';
echo '<div class="mb-3"><label class="form-label" for="name">' . __s('Nome', 'taskprocedure') . '</label>'
    . Html::input('name', ['id' => 'name', 'value' => $step->fields['name'] ?? '', 'required' => true]) . '</div>';
echo '<div class="mb-3"><label class="form-label" for="description">' . __s('Descrição', 'taskprocedure') . '</label>'
    . '<textarea class="form-control" id="description" name="description" rows="4">'
    . htmlescape((string) ($step->fields['description'] ?? '')) . '</textarea></div>';
echo '<div class="mb-3"><label class="form-label" for="position">' . __s('Posição', 'taskprocedure') . '</label>'
    . Html::input('position', ['id' => 'position', 'type' => 'number', 'min' => 1, 'value' => $step->fields['position'] ?? 1]) . '</div>';
echo '<label class="form-check"><input class="form-check-input" type="checkbox" name="is_active" value="1"'
    . (!isset($step->fields['is_active']) || $step->fields['is_active'] ? ' checked' : '')
    . '><span class="form-check-label">' . __s('Ativa', 'taskprocedure') . '</span></label>';
echo '<div class="mt-4"><button class="btn btn-primary" type="submit">' . __s('Salvar', 'taskprocedure')
    . '</button> <a class="btn" href="procedure.form.php?id=' . $procedureId . '">'
    . __s('Voltar', 'taskprocedure') . '</a></div></form></div></div>';
if ($id > 0) {
    echo '<form class="mt-3" method="post" action="step.form.php?id=' . $id . '">'
        . Html::hidden('id', ['value' => $id])
        . Html::hidden('plugin_taskprocedure_procedures_id', ['value' => $procedureId])
        . '<input type="hidden" name="_glpi_csrf_token" value="'
        . htmlescape(Session::getNewCSRFToken()) . '">'
        . '<button class="btn btn-danger" type="submit" name="delete" value="1">'
        . __s('Excluir etapa', 'taskprocedure') . '</button></form>';
}
echo '</div>';
Html::footer();
