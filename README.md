# TaskProcedure

Plugin para GLPI 11 destinado a procedimentos e checklists reutilizáveis em chamados.

## Estado atual

O plugin contém o CRUD administrativo de procedimentos e etapas, associação manual a chamados e execução de checklists na aba nativa do Ticket. A versão atual inclui progresso, conclusão por etapa, comentários, evidências textuais, reordenação e histórico de alterações.

## Estado atual

- Versão: `0.4.1`.
- Evidência é armazenada como texto ou link; upload de arquivos ainda não faz parte do plugin.
- O acesso administrativo usa o direito nativo `config` do GLPI.
- A execução da checklist usa o direito de atualização do chamado (`canUpdateItem()`).
- O histórico está persistido no banco, mas ainda não possui uma tela própria de consulta.

## Instalação de desenvolvimento

Copie este diretório para `plugins/taskprocedure` de uma instalação GLPI 11 e use a tela de Plugins ou:

```bash
php bin/console glpi:plugin:install taskprocedure -u glpi
php bin/console glpi:plugin:activate taskprocedure
```

Para validar a remoção:

```bash
php bin/console glpi:plugin:uninstall taskprocedure
```

## Documentação

- [Arquitetura](docs/architecture.md)
- [Banco de dados](docs/database.md)
- [Desenvolvimento](docs/development.md)
- [Permissões](docs/permissions.md)
- [Testes](docs/testing.md)
- [Roadmap](docs/roadmap.md)
