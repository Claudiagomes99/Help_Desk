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

## Capturas:
<img width="1917" height="627" alt="image" src="https://github.com/user-attachments/assets/50a8ed1e-a621-4c35-af35-ebdc6f4504d9" />
<img width="1917" height="452" alt="image" src="https://github.com/user-attachments/assets/ea463f41-efeb-48f5-ade6-57e20d35a1a1" />
<img width="1917" height="597" alt="image" src="https://github.com/user-attachments/assets/337a1957-9df3-4737-8aa0-c70393964962" />
<img width="678" height="733" alt="image" src="https://github.com/user-attachments/assets/ccb97d04-0f90-4f70-a145-f3037ac2cd36" />
<img width="682" height="578" alt="image" src="https://github.com/user-attachments/assets/5c3d5890-3a8c-43b8-b502-b5b9fcd25b11" />



##
LinkVideo:https://youtu.be/h-hl-yZxd4U
