# 📝 Task Manager PHP

![PHP Version](https://img.shields.io/badge/PHP-8.4-blue?style=for-the-badge&logo=php)
![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)
![Status](https://img.shields.io/badge/Status-Stable-brightgreen?style=for-the-badge)

Um sistema simples e leve de gerenciamento de tarefas (To-Do List) usando **PHP**, **PostgreSQL** e **Bootstrap**, com autenticação segura, filtros, prioridades e CRUD completo.

NOTA: Vale lembrar que este projeto foi para relembrar os conceitos novos em PHP e para colocar-los em prática.

---

## Captura de tela

![captura de tela](.github/images/Captura%20de%20tela%20de%202025-11-30%2020-08-28.png)

---

## 🚀 Funcionalidades

- 👤 Login e registro com senha criptografada (`password_hash`)
- 🔒 Proteção CSRF (isso protege contra vulnerabilidades) e validação server-side
- 🛠 CRUD de tarefas:
  - Criar
  - Editar
  - Excluir
  - Listar com filtros
- 🔍 Filtros por:
  - Texto (título)
  - Prioridade
  - Status
  - Paginação
- ⭐ Priorização de 1 a 5
- ⏳ Datas formatadas
- ⚠ Notificações com Bootstrap Toast e Flash Messages
- 🌘 Tema escuro e claro com base na configuração do navegador

---

## Segurança

| Recurso                              | Implementado |
| ------------------------------------ | ------------ |
| CSRF Token                           | ✔            |
| Hash com `password_hash()`           | ✔            |
| PHP Data Objects (PHP PDO) evitando prática de SQL Inject            | ✔            |
| Sanitização com `htmlspecialchars()` | ✔            |
| Validação de entrada no backend      | ✔            |


## Execução

Execute:

`php -S localhost:8000 -t public`  

Isso executará o projeto em [http://localhost:8000](https://www.exemplo.com)

## 🛠 Tecnologias

| Tecnologia | Uso |
|-----------|------|
| **PHP 8.4+** | Backend |
| **PostgreSQL** | Persistência |
| **PDO** | Consultas seguras |
| **Bootstrap 5** | UI |
| **Sessions + CSRF Token** | Segurança |
