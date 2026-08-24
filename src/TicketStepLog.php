<?php

/** Audit entry for a ticket checklist step. */
class PluginTaskprocedureTicketStepLog extends CommonDBTM
{
    public static function getTable($classname = null): string
    {
        return 'glpi_plugin_taskprocedure_step_logs';
    }
}
