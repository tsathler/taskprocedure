<?php

/**
 * Procedure template step model.
 */
class PluginTaskprocedureProcedureStep extends CommonDBTM
{
    public static function getTable($classname = null): string
    {
        return 'glpi_plugin_taskprocedure_steps';
    }

    public static function getForeignKeyField(): string
    {
        return 'plugin_taskprocedure_procedures_id';
    }

    public static function getFormURL($full = true): string
    {
        return '/plugins/taskprocedure/front/step.form.php';
    }
}
