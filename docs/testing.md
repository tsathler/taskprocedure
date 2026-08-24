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

Para o CRUD, acessar a página de configuração do plugin, criar um procedimento, adicionar etapas, editar, ativar/desativar e excluir. Confirmar no banco que os registros são gravados nas tabelas normalizadas.

Com um procedimento associado a um chamado, confirmar que cada etapa aparece como uma caixa de seleção. Marcar e desmarcar uma etapa deve atualizar `is_completed`, `completed_by` e `completed_at` em `glpi_plugin_taskprocedure_ticketsteps`, sem permitir alterar uma etapa de outro chamado ou procedimento.

O smoke test automatizado pode ser executado na raiz do harness:

```powershell
./plugins/taskprocedure/tests/checklist-smoke.ps1
```

Ele valida lint, plugin habilitado, schema, campos de detalhes e ausência de etapas/logs órfãos.
