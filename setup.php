<?php

/**
 * TaskProcedure - Procedures and reusable checklists for GLPI tickets.
 *
 * @license GPL-3.0-or-later
 */

use function Safe\define;

define('PLUGIN_TASKPROCEDURE_VERSION', '0.1.0');
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
    // Bootstrap only: no runtime integration is enabled yet.
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
