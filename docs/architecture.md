# Arquitetura

## Loop 1

O plugin segue o contrato oficial de plugins do GLPI: `setup.php` declara metadados e hooks; `hook.php` implementa instalação e desinstalação. Nenhum hook de Ticket ou entrada de menu é registrado no bootstrap.

## Template → instância

O modelo é separado da execução:

```text
Procedure + ProcedureStep
          ↓
ProcedureAssignmentService
          ↓
TicketProcedure + TicketProcedureStep
```

Ao associar, as etapas e seus textos serão copiados para a instância. Essa decisão de snapshot evita que uma edição posterior do template altere silenciosamente um checklist já iniciado. `procedure_version` permite evoluir para versionamento explícito sem quebrar instâncias existentes.

## Associação futura

Associação manual e regras automáticas devem chamar o mesmo `ProcedureAssignmentService::assign()`. O serviço deverá aceitar contexto, política de idempotência e a origem da associação; a regra de negócio não ficará em controllers ou hooks.

## Hooks previstos

Para os próximos loops, o candidato principal para a integração visual é `pre_itil_info_section` (disponível no GLPI 11), enquanto eventos de negócio poderão usar `item_add` e `item_update` para `Ticket`. A ativação desses hooks ficará para os loops correspondentes.
