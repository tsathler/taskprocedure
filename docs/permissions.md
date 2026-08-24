# Permissões

O MVP deverá integrar o sistema de Profiles/Rights do GLPI, sem usuários ou ACLs próprias. A proposta de direitos é:

- visualizar procedimentos;
- executar procedimentos;
- associar procedimentos;
- gerenciar procedimentos.

No Loop 1 esses direitos ainda não são registrados porque não existe tela ou itemtype executável. O próximo loop deverá criar a classe de Profile/Rights e inicializar direitos com as APIs nativas, mantendo acesso mínimo por padrão.
