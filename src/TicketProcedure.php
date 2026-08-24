<?php

/**
 * Native Ticket tab integration for TaskProcedure.
 */
class PluginTaskprocedureTicketProcedure extends CommonDBTM
{
    public static function getTypeName($nb = 0): string
    {
        return _n('Procedure', 'Procedures', $nb, 'taskprocedure');
    }

    public function getTabNameForItem(CommonGLPI $item, $withtemplate = 0)
    {
        if (!($item instanceof Ticket)) {
            return '';
        }

        return [
            1 => self::createTabEntry(
                __s('Procedimentos', 'taskprocedure'),
                0,
                Ticket::class,
                'ti ti-list-check',
            ),
        ];
    }

    public static function displayTabContentForItem(
        CommonGLPI $item,
        $tabnum = 1,
        $withtemplate = 0
    ): bool {
        if (!($item instanceof Ticket)) {
            return false;
        }

        echo '<div class="card card-sm"><div class="card-body">';
        echo '<p class="mb-0">';
        echo __s('Nenhum procedimento associado a este chamado.', 'taskprocedure');
        echo '</p></div></div>';

        return true;
    }
}
