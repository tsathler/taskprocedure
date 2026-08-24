# Arquitetura

## Estado atual

O plugin segue o contrato oficial de plugins do GLPI: `setup.php` declara metadados, classes e hooks; `hook.php` implementa instalação, migração idempotente e desinstalação. A aba nativa de `Ticket` é registrada por `addtabon`.

## Template → instância

O modelo é separado da execução:

```text
Procedure + ProcedureStep
          ↓ associação manual
TicketProcedure + TicketProcedureStep
          ↓
execução, progresso e auditoria
```

Ao associar, as etapas e seus textos são copiados para a instância. Essa decisão de snapshot evita que uma edição posterior do template altere silenciosamente um checklist já iniciado. `procedure_version` permite evoluir para versionamento explícito sem quebrar instâncias existentes.

## Próxima evolução de domínio

Associação manual e regras automáticas devem passar a chamar o mesmo `ProcedureAssignmentService::assign()`. O serviço deverá aceitar contexto, política de idempotência e a origem da associação; a regra de negócio atualmente está no controller e deve ser extraída em uma próxima etapa.

## Hooks previstos

Para os próximos loops, o candidato principal para a integração visual é `pre_itil_info_section` (disponível no GLPI 11), enquanto eventos de negócio poderão usar `item_add` e `item_update` para `Ticket`. A ativação desses hooks ficará para os loops correspondentes.
