# Harness de validação

Com uma instalação GLPI 11 disponível, executar na raiz do GLPI:

```bash
php -l plugins/taskprocedure/setup.php
php -l plugins/taskprocedure/hook.php
php bin/console glpi:plugin:install taskprocedure -u glpi
php bin/console glpi:plugin:activate taskprocedure
php bin/console glpi:plugin:uninstall taskprocedure
```

Após a instalação, confirmar no banco a existência das quatro tabelas e que todas estão vazias. Abrir um chamado e confirmar a aba lateral **Procedimentos**, entre **Chamado** e **Estatísticas**, com a mensagem “Nenhum procedimento associado a este chamado.” Não deve haver menu paralelo, CRUD ou procedimento de exemplo.
