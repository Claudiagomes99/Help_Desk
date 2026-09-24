# Sistema de Chamados (Help Desk)

Um sistema simples para abrir, acompanhar e encerrar chamados de suporte técnico,
com níveis de urgência, conversa dentro de cada chamado e acesso por perfil de usuário.

**Feito com:** PHP + Bootstrap 5 + JavaScript + MySQL (PDO).

## O que ele faz

- Cadastro e login: a primeira conta criada vira **admin** automaticamente; nas demais, a pessoa escolhe entre **cliente** e **atendente**
- Três perfis: **admin**, **atendente** e **cliente**
- Abertura de chamados com título, descrição e urgência (baixa, média, alta, crítica)
- Lista com filtros por status e urgência, com os mais urgentes no topo
- Cliente vê só os próprios chamados; atendente e admin veem todos
- Atendente altera status e urgência, e encerra chamados (com confirmação)


## Estrutura

```
sistema_chamados/
├── config.php              Conexão com o banco, fuso horário e sessão
├── database.sql            Criação das tabelas
├── login.php / cadastro.php / logout.php
├── index.php               Lista de chamados e filtros
├── novo_chamado.php        Abertura de chamado
├── chamado.php             Detalhe, edição e encerramento
├── includes/
│   ├── auth.php            Sessão, permissões, proteção de formulários e helpers
│   ├── header.php
│   └── footer.php
└── assets/
    ├── css/style.css       Tema lilás
    └── js/script.js        Confirmações e avisos
```

## Perfis

| Perfil    | Pode                                                                   |
|-----------|------------------------------------------------------------------------|
| cliente   | Abrir chamados e ver os próprios chamados                             |
| atendente | Tudo do cliente + ver todos os chamados, alterar status/urgência e encerrar |
| admin     | Mesmas permissões do atendente                                         |


##
LinkVideo:https://youtu.be/h-hl-yZxd4U
