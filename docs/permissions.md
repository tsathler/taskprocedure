# Permissões

O plugin usa o sistema de Profiles/Rights do GLPI, sem usuários ou ACLs próprias. O estado atual é:

- administração de procedimentos: direito nativo `config` com `UPDATE`;
- execução, associação, reordenação e atualização de detalhes: permissão de atualização do chamado (`Ticket::canUpdateItem()`);
- visualização: usuários autenticados que tenham acesso ao chamado.

Uma classe de direito específica para separar administração, associação e execução continua sendo uma melhoria futura.
