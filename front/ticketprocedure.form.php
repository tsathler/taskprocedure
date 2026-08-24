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
$ticketStepLog = new PluginTaskprocedureTicketStepLog();

if (isset($_POST['update_step'])) {
    if (!$ticketProcedure->getFromDB($ticketProcedureId)
        || (int) $ticketProcedure->fields['tickets_id'] !== $ticketId) {
        Html::displayErrorAndDie(__s('Procedimento do chamado não encontrado.', 'taskprocedure'));
    }

    $ticketStepId = (int) ($_POST['ticketstep_id'] ?? 0);
    if (!$ticketStep->getFromDB($ticketStepId)
        || (int) $ticketStep->fields['ticketprocedures_id'] !== $ticketProcedureId) {
        Html::displayErrorAndDie(__s('Etapa do chamado não encontrada.', 'taskprocedure'));
    }

    $isCompleted = isset($_POST['is_completed']) && (int) $_POST['is_completed'] === 1;
    $wasCompleted = (int) $ticketStep->fields['is_completed'] === 1;
    $ticketStep->update([
        'id' => $ticketStepId,
        'is_completed' => $isCompleted ? 1 : 0,
        'completed_by' => $isCompleted ? (int) Session::getLoginUserID() : 0,
        'completed_at' => $isCompleted ? date('Y-m-d H:i:s') : null,
    ]);

    if ($wasCompleted !== $isCompleted) {
        $ticketStepLog->add([
            'ticketsteps_id' => $ticketStepId,
            'ticketprocedures_id' => $ticketProcedureId,
            'tickets_id' => $ticketId,
            'users_id' => (int) Session::getLoginUserID(),
            'action' => 'completion',
            'old_value' => $wasCompleted ? '1' : '0',
            'new_value' => $isCompleted ? '1' : '0',
        ]);
    }

    if (isset($_POST['ajax'])) {
        $steps = $ticketStep->find(['ticketprocedures_id' => $ticketProcedureId]);
        $completed = count(array_filter(
            $steps,
            static fn(array $step): bool => (int) $step['is_completed'] === 1,
        ));
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode([
            'success' => true,
            'completed' => $completed,
            'total' => count($steps),
        ]);
        exit;
    }

    Html::redirect('/front/ticket.form.php?id=' . $ticketId);
}

if (isset($_POST['update_step_details'])) {
    if (!$ticketProcedure->getFromDB($ticketProcedureId)
        || (int) $ticketProcedure->fields['tickets_id'] !== $ticketId) {
        Html::displayErrorAndDie(__s('Procedimento do chamado não encontrado.', 'taskprocedure'));
    }

    $ticketStepId = (int) ($_POST['ticketstep_id'] ?? 0);
    if (!$ticketStep->getFromDB($ticketStepId)
        || (int) $ticketStep->fields['ticketprocedures_id'] !== $ticketProcedureId) {
        Html::displayErrorAndDie(__s('Etapa do chamado não encontrada.', 'taskprocedure'));
    }

    $oldDetails = json_encode([
        'comment' => (string) ($ticketStep->fields['comment'] ?? ''),
        'evidence' => (string) ($ticketStep->fields['evidence'] ?? ''),
    ], JSON_UNESCAPED_UNICODE);
    $newDetails = [
        'comment' => trim((string) ($_POST['comment'] ?? '')),
        'evidence' => trim((string) ($_POST['evidence'] ?? '')),
    ];
    $ticketStep->update(['id' => $ticketStepId] + $newDetails);
    if ($oldDetails !== json_encode($newDetails, JSON_UNESCAPED_UNICODE)) {
        $ticketStepLog->add([
            'ticketsteps_id' => $ticketStepId,
            'ticketprocedures_id' => $ticketProcedureId,
            'tickets_id' => $ticketId,
            'users_id' => (int) Session::getLoginUserID(),
            'action' => 'details',
            'old_value' => $oldDetails,
            'new_value' => json_encode($newDetails, JSON_UNESCAPED_UNICODE),
        ]);
    }
    Html::redirect('/front/ticket.form.php?id=' . $ticketId);
}

if (isset($_POST['move_step'])) {
    if (!$ticketProcedure->getFromDB($ticketProcedureId)
        || (int) $ticketProcedure->fields['tickets_id'] !== $ticketId) {
        Html::displayErrorAndDie(__s('Procedimento do chamado não encontrado.', 'taskprocedure'));
    }

    $ticketStepId = (int) ($_POST['ticketstep_id'] ?? 0);
    if (!$ticketStep->getFromDB($ticketStepId)
        || (int) $ticketStep->fields['ticketprocedures_id'] !== $ticketProcedureId) {
        Html::displayErrorAndDie(__s('Etapa do chamado não encontrada.', 'taskprocedure'));
    }

    $direction = $_POST['move_step'] === 'up' ? -1 : 1;
    $steps = array_values($ticketStep->find(
        ['ticketprocedures_id' => $ticketProcedureId],
        ['ORDER' => 'position ASC, id ASC'],
    ));
    $currentIndex = null;
    foreach ($steps as $index => $step) {
        if ((int) $step['id'] === $ticketStepId) {
            $currentIndex = $index;
            break;
        }
    }
    $targetIndex = $currentIndex === null ? null : $currentIndex + $direction;
    if ($targetIndex !== null && isset($steps[$targetIndex])) {
        $target = $steps[$targetIndex];
        $oldPosition = (int) $ticketStep->fields['position'];
        $newPosition = (int) $target['position'];
        $ticketStep->update(['id' => $ticketStepId, 'position' => $newPosition]);
        $ticketStep->update(['id' => (int) $target['id'], 'position' => $oldPosition]);
        $ticketStepLog->add([
            'ticketsteps_id' => $ticketStepId,
            'ticketprocedures_id' => $ticketProcedureId,
            'tickets_id' => $ticketId,
            'users_id' => (int) Session::getLoginUserID(),
            'action' => 'reorder',
            'old_value' => (string) $oldPosition,
            'new_value' => (string) $newPosition,
        ]);
    }
    Html::redirect('/front/ticket.form.php?id=' . $ticketId);
}

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
