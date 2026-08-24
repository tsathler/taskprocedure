# TaskProcedure

Plugin para GLPI 11 destinado a procedimentos e checklists reutilizáveis em chamados.

## Estado atual

O repositório contém o CRUD administrativo de procedimentos e etapas, além da aba nativa no Ticket. Associação e execução do checklist ainda serão implementadas nos próximos loops.

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
