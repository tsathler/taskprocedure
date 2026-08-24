<?php

/**
 * TaskProcedure - Procedures and reusable checklists for GLPI tickets.
 *
 * @license GPL-3.0-or-later
 */

use Glpi\Plugin\Hooks;
use function Safe\define;

require_once __DIR__ . '/src/TicketProcedure.php';
require_once __DIR__ . '/src/TicketStep.php';
require_once __DIR__ . '/src/Procedure.php';
require_once __DIR__ . '/src/ProcedureStep.php';

define('PLUGIN_TASKPROCEDURE_VERSION', '0.3.0');
define('PLUGIN_TASKPROCEDURE_MIN_GLPI', '11.0.0');
define('PLUGIN_TASKPROCEDURE_MAX_GLPI', '11.0.99');

/**
 * Initialize the plugin hooks.
 *
 * Loop 1 intentionally registers no UI or Ticket hooks. Later loops will add
 * those integrations without changing the installation contract.
 */
function plugin_init_taskprocedure(): void
{
    global $PLUGIN_HOOKS;

    Plugin::registerClass(PluginTaskprocedureTicketProcedure::class, [
        'addtabon' => ['Ticket'],
    ]);

    $PLUGIN_HOOKS['add_javascript']['taskprocedure'] = 'js/taskprocedure.js';

    if (Session::haveRight('config', UPDATE)) {
        $PLUGIN_HOOKS['config_page']['taskprocedure'] = 'front/procedure.php';
    }
}

function plugin_version_taskprocedure(): array
{
    return [
        'name'           => 'TaskProcedure',
        'version'        => PLUGIN_TASKPROCEDURE_VERSION,
        'author'         => 'TaskProcedure contributors',
        'license'        => 'GPL-3.0-or-later',
        'requirements'   => [
            'glpi' => [
                'min' => PLUGIN_TASKPROCEDURE_MIN_GLPI,
                'max' => PLUGIN_TASKPROCEDURE_MAX_GLPI,
            ],
        ],
    ];
}

function plugin_taskprocedure_check_prerequisites(): bool
{
    if (version_compare(GLPI_VERSION, PLUGIN_TASKPROCEDURE_MIN_GLPI, '<')) {
        echo sprintf(
            'TaskProcedure requires GLPI %s or newer.',
            PLUGIN_TASKPROCEDURE_MIN_GLPI,
        );
        return false;
    }

    return true;
}

function plugin_taskprocedure_check_config(bool $verbose = false): bool
{
    return true;
}
