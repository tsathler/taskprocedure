# Banco de dados

O Loop 1 cria quatro tabelas normalizadas, sem dados iniciais:

| Tabela | Papel |
| --- | --- |
| `glpi_plugin_taskprocedure_procedures` | Templates de procedimento |
| `glpi_plugin_taskprocedure_steps` | Etapas do template |
| `glpi_plugin_taskprocedure_ticketprocedures` | Instâncias associadas a chamados |
| `glpi_plugin_taskprocedure_ticketsteps` | Snapshot das etapas executáveis |

O schema usa charset, collation e assinatura de chave fornecidos por `DBConnection`. A remoção ocorre em ordem reversa de dependência. As referências aos IDs do GLPI são indexadas; constraints físicas não são adicionadas no bootstrap para seguir a compatibilidade histórica dos plugins GLPI e permitir purga controlada pelo domínio.

Não há JSON para representar o checklist. Isso preserva consultas, auditoria, migrações e futuras regras.
