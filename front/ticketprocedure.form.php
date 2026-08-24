<?php

include '../../../inc/includes.php';

Session::checkLoginUser();
$ticketId = (int) ($_POST['tickets_id'] ?? 0);
$procedureId = (int) ($_POST['procedure_id'] ?? 0);

$ticket = new Ticket();
if (!$ticket->getFromDB($ticketId) || !$ticket->canUpdateItem()) {
    Html::displayErrorAndDie(__s('Acesso não autorizado.', 'taskprocedure'));
}

$procedure = new PluginTaskprocedureProcedure();
if (!$procedure->getFromDB($procedureId) || (int) $procedure->fields['is_active'] !== 1) {
    Html::displayErrorAndDie(__s('Procedimento inválido.', 'taskprocedure'));
}

$ticketProcedure = new PluginTaskprocedureTicketProcedure();
$assignmentId = $ticketProcedure->add([
    'tickets_id' => $ticketId,
    'procedure_name' => $procedure->fields['name'],
    'procedure_version' => '1',
]);

if ($assignmentId) {
    $sourceStep = new PluginTaskprocedureProcedureStep();
    $ticketStep = new PluginTaskprocedureTicketStep();
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
