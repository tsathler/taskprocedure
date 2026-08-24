<?php

/**
 * TaskProcedure installation and uninstallation hooks.
 *
 * The schema is normalized from the beginning so future manual and automatic
 * assignments can share the same domain/service layer.
 */

function plugin_taskprocedure_install(): bool
{
    global $DB;

    $migration = new Migration(PLUGIN_TASKPROCEDURE_VERSION);
    $charset = DBConnection::getDefaultCharset();
    $collation = DBConnection::getDefaultCollation();
    $keySign = DBConnection::getDefaultPrimaryKeySignOption();

    $tables = [
        'glpi_plugin_taskprocedure_procedures' => "
            CREATE TABLE `glpi_plugin_taskprocedure_procedures` (
                `id` int {$keySign} NOT NULL AUTO_INCREMENT,
                `name` varchar(255) NOT NULL,
                `description` text DEFAULT NULL,
                `is_active` tinyint NOT NULL DEFAULT 1,
                `date_creation` timestamp NULL DEFAULT NULL,
                `date_mod` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `name` (`name`),
                KEY `is_active` (`is_active`)
            ) ENGINE=InnoDB DEFAULT CHARSET={$charset} COLLATE={$collation} ROW_FORMAT=DYNAMIC;
        ",
        'glpi_plugin_taskprocedure_steps' => "
            CREATE TABLE `glpi_plugin_taskprocedure_steps` (
                `id` int {$keySign} NOT NULL AUTO_INCREMENT,
                `plugin_taskprocedure_procedures_id` int {$keySign} NOT NULL,
                `name` varchar(255) NOT NULL,
                `description` text DEFAULT NULL,
                `position` int NOT NULL DEFAULT 0,
                `is_active` tinyint NOT NULL DEFAULT 1,
                `date_creation` timestamp NULL DEFAULT NULL,
                `date_mod` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `procedure_id` (`plugin_taskprocedure_procedures_id`),
                KEY `position` (`position`)
            ) ENGINE=InnoDB DEFAULT CHARSET={$charset} COLLATE={$collation} ROW_FORMAT=DYNAMIC;
        ",
        'glpi_plugin_taskprocedure_ticketprocedures' => "
            CREATE TABLE `glpi_plugin_taskprocedure_ticketprocedures` (
                `id` int {$keySign} NOT NULL AUTO_INCREMENT,
                `tickets_id` int {$keySign} NOT NULL,
                `procedure_name` varchar(255) NOT NULL,
                `procedure_version` varchar(32) NOT NULL DEFAULT '1',
                `date_creation` timestamp NULL DEFAULT NULL,
                `date_mod` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `tickets_id` (`tickets_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET={$charset} COLLATE={$collation} ROW_FORMAT=DYNAMIC;
        ",
        'glpi_plugin_taskprocedure_ticketsteps' => "
            CREATE TABLE `glpi_plugin_taskprocedure_ticketsteps` (
                `id` int {$keySign} NOT NULL AUTO_INCREMENT,
                `ticketprocedures_id` int {$keySign} NOT NULL,
                `name` varchar(255) NOT NULL,
                `description` text DEFAULT NULL,
                `position` int NOT NULL DEFAULT 0,
                `is_active` tinyint NOT NULL DEFAULT 1,
                `is_completed` tinyint NOT NULL DEFAULT 0,
                `completed_by` int {$keySign} NOT NULL DEFAULT 0,
                `completed_at` timestamp NULL DEFAULT NULL,
                `comment` text DEFAULT NULL,
                `evidence` text DEFAULT NULL,
                `date_creation` timestamp NULL DEFAULT NULL,
                `date_mod` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `ticketprocedure_id` (`ticketprocedures_id`),
                KEY `is_completed` (`is_completed`)
            ) ENGINE=InnoDB DEFAULT CHARSET={$charset} COLLATE={$collation} ROW_FORMAT=DYNAMIC;
        ",
        'glpi_plugin_taskprocedure_step_logs' => "
            CREATE TABLE `glpi_plugin_taskprocedure_step_logs` (
                `id` int {$keySign} NOT NULL AUTO_INCREMENT,
                `ticketsteps_id` int {$keySign} NOT NULL,
                `ticketprocedures_id` int {$keySign} NOT NULL,
                `tickets_id` int {$keySign} NOT NULL,
                `users_id` int {$keySign} NOT NULL DEFAULT 0,
                `action` varchar(32) NOT NULL,
                `old_value` text DEFAULT NULL,
                `new_value` text DEFAULT NULL,
                `date_creation` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `ticketstep_id` (`ticketsteps_id`),
                KEY `ticket_id` (`tickets_id`),
                KEY `date_creation` (`date_creation`)
            ) ENGINE=InnoDB DEFAULT CHARSET={$charset} COLLATE={$collation} ROW_FORMAT=DYNAMIC;
        ",
    ];

    foreach ($tables as $table => $query) {
        if (!$DB->tableExists($table) && !$DB->doQuery($query)) {
            return false;
        }
    }

    if ($DB->tableExists('glpi_plugin_taskprocedure_ticketsteps')) {
        if (!$DB->fieldExists('glpi_plugin_taskprocedure_ticketsteps', 'comment')
            && !$DB->doQuery("ALTER TABLE `glpi_plugin_taskprocedure_ticketsteps` ADD `comment` text DEFAULT NULL")) {
            return false;
        }
        if (!$DB->fieldExists('glpi_plugin_taskprocedure_ticketsteps', 'evidence')
            && !$DB->doQuery("ALTER TABLE `glpi_plugin_taskprocedure_ticketsteps` ADD `evidence` text DEFAULT NULL")) {
            return false;
        }
    }

    // Keep the migration object in the install flow for future schema versions.
    unset($migration);

    return true;
}

function plugin_taskprocedure_uninstall(): bool
{
    global $DB;

    $tables = [
        'glpi_plugin_taskprocedure_step_logs',
        'glpi_plugin_taskprocedure_ticketsteps',
        'glpi_plugin_taskprocedure_ticketprocedures',
        'glpi_plugin_taskprocedure_steps',
        'glpi_plugin_taskprocedure_procedures',
    ];

    foreach ($tables as $table) {
        if ($DB->tableExists($table) && !$DB->doQuery("DROP TABLE `{$table}`")) {
            return false;
        }
    }

    return true;
}
