<?php

/**
 * Procedure template model.
 */
class PluginTaskprocedureProcedure extends CommonDBTM
{
    public static function getTable($classname = null): string
    {
        return 'glpi_plugin_taskprocedure_procedures';
    }

    public static function getTypeName($nb = 0): string
    {
        return _n('Procedure', 'Procedures', $nb, 'taskprocedure');
    }

    public static function getFormURL($full = true): string
    {
        return '/plugins/taskprocedure/front/procedure.form.php';
    }
}
