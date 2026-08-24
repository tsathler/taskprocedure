# TaskProcedure

Plugin para GLPI 11 destinado a procedimentos e checklists reutilizáveis em chamados.

## Estado atual

O repositório contém o Loop 2: bootstrap compatível com GLPI 11 e uma aba nativa, vazia e somente leitura dentro de chamados. Ainda não há templates, CRUD, associação ou ações de execução.

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
