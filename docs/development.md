# Desenvolvimento

## Loops

O projeto usa loops incrementais. O Loop 1 entrega somente bootstrap, schema vazio e validações estáticas. A integração de chamado começa no Loop 2; CRUD e execução ficam para loops posteriores.

## Padrões

- Não modificar o core do GLPI.
- Usar APIs e hooks oficiais.
- Escapar e validar toda entrada nas telas futuras.
- Usar `__()`/`__s()` para strings de interface.
- Controllers futuros devem delegar associação ao serviço de domínio.

## Referências consultadas

- [GLPI Help Center — Plugins](https://help.glpi-project.org/documentation/modules/configuration/plugins): convenções de diretório, `hook.php`/`setup.php` e ciclo de instalação.
- [GLPI developer documentation — Hooks](https://github.com/glpi-project/docdev/blob/master/source/plugins/hooks.rst): hooks de negócio e apresentação, incluindo `pre_itil_info_section` no GLPI 11.
- [Plugin Example oficial](https://github.com/pluginsGLPI/example): padrão de metadados, compatibilidade, `Migration`, `DBConnection` e hooks de instalação.
- [GLPI CLI documentation](https://github.com/glpi-project/doc/blob/master/source/cli.rst): comandos `glpi:plugin:install`, `activate` e `uninstall` usados pelo harness.
