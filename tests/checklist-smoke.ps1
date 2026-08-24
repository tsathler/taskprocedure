[CmdletBinding()]
param()

$ErrorActionPreference = 'Stop'
Set-Location (Split-Path -Parent $PSScriptRoot)
Set-Location ../..

$dbPassword = 'glpi-dev-only'
$envFile = Join-Path (Get-Location) '.env'
if (Test-Path -LiteralPath $envFile) {
    $passwordLine = Get-Content -LiteralPath $envFile | Where-Object { $_ -match '^GLPI_DB_PASSWORD=' } | Select-Object -First 1
    if ($passwordLine -match '^GLPI_DB_PASSWORD=(.*)$') {
        $dbPassword = $Matches[1].Trim().Trim('"').Trim("'")
    }
}

function Invoke-Glpi([string[]]$Arguments) {
    $output = & docker compose exec -T glpi bin/console @Arguments 2>&1
    if ($LASTEXITCODE -ne 0) {
        throw "Comando GLPI falhou: bin/console $($Arguments -join ' ')`n$output"
    }
    return ($output -join "`n")
}

function Invoke-Sql([string]$Query) {
    $output = & docker compose exec -T db mariadb -uglpi "-p$dbPassword" glpi -Nse $Query 2>&1
    if ($LASTEXITCODE -ne 0) {
        throw "Consulta SQL falhou: $Query`n$output"
    }
    return ($output -join "`n").Trim()
}

& (Join-Path $PSScriptRoot '..\..\..\scripts\validate-plugin.ps1') taskprocedure
if ($LASTEXITCODE -ne 0) { throw 'Lint do plugin falhou.' }

$pluginList = Invoke-Glpi @('glpi:plugin:list', '--allow-superuser')
if ($pluginList -notmatch 'taskprocedure' -or $pluginList -notmatch 'Enabled') {
    throw 'TaskProcedure não está Enabled.'
}

$expectedTables = @(
    'glpi_plugin_taskprocedure_procedures',
    'glpi_plugin_taskprocedure_steps',
    'glpi_plugin_taskprocedure_ticketprocedures',
    'glpi_plugin_taskprocedure_ticketsteps',
    'glpi_plugin_taskprocedure_step_logs'
)
foreach ($table in $expectedTables) {
    if ((Invoke-Sql "SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='$table'") -ne '1') {
        throw "Tabela ausente: $table"
    }
}

foreach ($field in @('comment', 'evidence')) {
    if ((Invoke-Sql "SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='glpi_plugin_taskprocedure_ticketsteps' AND COLUMN_NAME='$field'") -ne '1') {
        throw "Campo ausente em ticketsteps: $field"
    }
}

$orphanSteps = Invoke-Sql "SELECT COUNT(*) FROM glpi_plugin_taskprocedure_ticketsteps ts LEFT JOIN glpi_plugin_taskprocedure_ticketprocedures tp ON tp.id=ts.ticketprocedures_id WHERE tp.id IS NULL"
if ($orphanSteps -ne '0') { throw "Etapas órfãs encontradas: $orphanSteps" }

$orphanLogs = Invoke-Sql "SELECT COUNT(*) FROM glpi_plugin_taskprocedure_step_logs l LEFT JOIN glpi_plugin_taskprocedure_ticketsteps ts ON ts.id=l.ticketsteps_id WHERE ts.id IS NULL"
if ($orphanLogs -ne '0') { throw "Logs órfãos encontrados: $orphanLogs" }

Write-Output 'Checklist smoke test passed.'
