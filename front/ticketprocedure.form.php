<?php

include '../../../inc/includes.php';

Session::checkLoginUser();
$ticketId = (int) ($_POST['tickets_id'] ?? 0);
$procedureId = (int) ($_POST['procedure_id'] ?? 0);
$ticketProcedureId = (int) ($_POST['ticketprocedure_id'] ?? 0);

$ticket = new Ticket();
if (!$ticket->getFromDB($ticketId) || !$ticket->canUpdateItem()) {
    Html::displayErrorAndDie(__s('Acesso não autorizado.', 'taskprocedure'));
}

$ticketProcedure = new PluginTaskprocedureTicketProcedure();
$ticketStep = new PluginTaskprocedureTicketStep();

if (isset($_POST['delete'])) {
    if (!$ticketProcedure->getFromDB($ticketProcedureId)
        || (int) $ticketProcedure->fields['tickets_id'] !== $ticketId) {
        Html::displayErrorAndDie(__s('Procedimento do chamado não encontrado.', 'taskprocedure'));
    }

    foreach ($ticketStep->find(['ticketprocedures_id' => $ticketProcedureId]) as $step) {
        $ticketStep->delete(['id' => (int) $step['id']], true);
    }
    $ticketProcedure->delete(['id' => $ticketProcedureId], true);
    Html::redirect('/front/ticket.form.php?id=' . $ticketId);
}

$procedure = new PluginTaskprocedureProcedure();
if (!$procedure->getFromDB($procedureId) || (int) $procedure->fields['is_active'] !== 1) {
    Html::displayErrorAndDie(__s('Procedimento inválido.', 'taskprocedure'));
}

$assignmentId = $ticketProcedure->add([
    'tickets_id' => $ticketId,
    'procedure_name' => $procedure->fields['name'],
    'procedure_version' => '1',
]);

if ($assignmentId) {
    $sourceStep = new PluginTaskprocedureProcedureStep();
    foreach ($sourceStep->find(
        ['plugin_taskprocedure_procedures_id' => $procedureId, 'is_active' => 1],
        ['ORDER' => 'position ASC, id ASC'],
    ) as $step) {
        $ticketStep->add([
            'ticketprocedures_id' => (int) $assignmentId,
            'name' => $step['name'],
            'description' => $step['description'],
            'position' => $step['position'],
            'is_active' => 1,
        ]);
    }
}

Html::redirect('/front/ticket.form.php?id=' . $ticketId);
