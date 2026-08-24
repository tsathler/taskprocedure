<?php

/**
 * A checklist step copied to a ticket procedure.
 */
class PluginTaskprocedureTicketStep extends CommonDBTM
{
    public static function getTable($classname = null): string
    {
        return 'glpi_plugin_taskprocedure_ticketsteps';
    }

    public static function getForeignKeyField(): string
    {
        return 'ticketprocedures_id';
    }
}
