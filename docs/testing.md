# Harness de validação

Com uma instalação GLPI 11 disponível, executar na raiz do GLPI:

```bash
php -l plugins/taskprocedure/setup.php
php -l plugins/taskprocedure/hook.php
php bin/console glpi:plugin:install taskprocedure -u glpi
php bin/console glpi:plugin:activate taskprocedure
php bin/console glpi:plugin:uninstall taskprocedure
```

Após a instalação, confirmar no banco a existência das quatro tabelas e que todas estão vazias. A validação do Loop 1 não deve exigir menu, CRUD ou procedimento de exemplo.
