param(
    [Parameter(Mandatory = $true)]
    [string]$GlpiRoot,
    [string]$AdminUser = 'glpi'
)

$ErrorActionPreference = 'Stop'
$PluginRoot = Join-Path $GlpiRoot 'plugins\taskprocedure'

if (-not (Test-Path -LiteralPath (Join-Path $PluginRoot 'setup.php'))) {
    throw "Plugin not found at $PluginRoot"
}

php -l (Join-Path $PluginRoot 'setup.php')
php -l (Join-Path $PluginRoot 'hook.php')
php (Join-Path $GlpiRoot 'bin\console') glpi:plugin:install taskprocedure -u $AdminUser
php (Join-Path $GlpiRoot 'bin\console') glpi:plugin:activate taskprocedure
php (Join-Path $GlpiRoot 'bin\console') glpi:plugin:uninstall taskprocedure

Write-Output 'TaskProcedure Loop 1 harness passed.'
