# Sistema de Biblioteca - Projeto Full-Stack

Sistema completo de gerenciamento de biblioteca desenvolvido com HTML, CSS, JavaScript, PHP e MySQL.

## Tecnologias Utilizadas

- **Frontend**: HTML5, CSS3, JavaScript, Bootstrap 5
- **Backend**: PHP 8.2 com PDO
- **Banco de Dados**: MySQL 8.0
- **Infraestrutura**: Docker, Docker Compose
- **Servidor Web**: Apache

## Funcionalidades

### Autenticação e Controle de Acesso
- Login com email e senha criptografada (bcrypt)
- Sistema de sessões
- Proteção de rotas (apenas usuários autenticados)
- Sistema de permissões (usuários admin e usuários comuns)
- Apenas administradores podem:
  - Cadastrar, editar e excluir livros
  - Devolver livros (encerrar empréstimos)
  - Excluir registros de empréstimos

### CRUD de Livros
- Cadastrar novos livros
- Listar todos os livros
- Editar informações dos livros
- Excluir livros (com validação de empréstimos ativos)
- Busca em tempo real
- Controle de quantidade total e disponível

### CRUD de Empréstimos
- Realizar empréstimos
- Listar todos os empréstimos
- Devolver livros
- Excluir registros de empréstimos
- Status automático (ativo, devolvido, atrasado)
- Atualização automática de disponibilidade

### Dashboard
- Estatísticas gerais do sistema
- Total de livros
- Livros disponíveis
- Empréstimos ativos
- Usuários cadastrados
- Lista de empréstimos recentes

## Estrutura do Banco de Dados

### Tabela: usuarios
- id (PK, AUTO_INCREMENT)
- nome
- email (UNIQUE)
- senha (criptografada)
- admin (BOOLEAN, padrão FALSE)
- data_cadastro

### Tabela: livros
- id (PK, AUTO_INCREMENT)
- titulo
- autor
- isbn (UNIQUE)
- ano_publicacao
- quantidade_total
- quantidade_disponivel
- data_cadastro

### Tabela: emprestimos (Relacionamento 1:N com livros e usuarios)
- id (PK, AUTO_INCREMENT)
- livro_id (FK -> livros.id)
- usuario_id (FK -> usuarios.id)
- data_emprestimo
- data_devolucao_prevista
- data_devolucao_real
- status (ativo, devolvido, atrasado)

## Instalação e Execução

### Pré-requisitos
- Docker
- Docker Compose

### Passo a Passo

1. Clone ou navegue até o diretório do projeto:
```bash
cd /home/marcelo/puc/projeto02
```

2. Inicie os containers:
```bash
docker-compose up -d --build
```

3. Aguarde alguns segundos para o MySQL inicializar completamente.

4. Acesse a aplicação:
- **Aplicação Principal**: http://localhost:8080
- **phpMyAdmin**: http://localhost:8081

### Credenciais de Acesso

**Usuário Administrador:**
- Email: `admin@biblioteca.com`
- Senha: `password`
- Permissões: Gerenciar livros e empréstimos

**Usuários Comuns:**
- Email: `joao@email.com` ou `maria@email.com`
- Senha: `password`
- Permissões: Apenas realizar empréstimos e visualizar dados

**Banco de Dados (phpMyAdmin):**
- Usuário: `biblioteca_user`
- Senha: `biblioteca_pass`

## Comandos Docker Úteis

```bash
docker-compose up -d

docker-compose down

docker-compose logs -f web

docker-compose logs -f db

docker-compose restart

docker-compose ps

docker exec -it biblioteca_db mysql -u biblioteca_user -p
```

## Estrutura de Arquivos

```
projeto02/
├── docker-compose.yml
├── Dockerfile
├── init.sql
├── README.md
└── src/
    ├── config/
    │   └── database.php
    ├── includes/
    │   └── auth.php
    ├── public/
    │   ├── css/
    │   │   └── style.css
    │   ├── js/
    │   │   └── main.js
    │   ├── dashboard.php
    │   ├── emprestimos.php
    │   ├── livros.php
    │   ├── login.php
    │   └── logout.php
    └── index.php
```

## Dados de Teste

O sistema já vem com dados pré-cadastrados:

**Usuários:**
- Admin (admin@biblioteca.com) - Administrador com todas as permissões
- João Silva (joao@email.com) - Usuário comum
- Maria Santos (maria@email.com) - Usuário comum

Senha para todos: `password`

**Livros:**
- Clean Code - Robert C. Martin
- Design Patterns - Gang of Four
- The Pragmatic Programmer - Hunt & Thomas
- Refactoring - Martin Fowler
- Domain-Driven Design - Eric Evans

**Empréstimos:**
- 2 empréstimos ativos para demonstração

## Segurança Implementada

- Senhas criptografadas com bcrypt
- Proteção contra SQL Injection (PDO com prepared statements)
- Validação de sessões
- Proteção XSS com htmlspecialchars
- Transações SQL para integridade de dados
- Validação de formulários (client e server-side)
- Sistema de permissões por função (admin/usuário comum)
- Controle de acesso a operações críticas (CRUD de livros)

## Funcionalidades Extras

- Interface responsiva (mobile-friendly)
- Busca em tempo real nas tabelas
- Mensagens de feedback para o usuário
- Validação de dados no frontend e backend
- Atualização automática de status de empréstimos
- Controle de disponibilidade automático
- Modal para formulários
- Animações e transições suaves

## Autores

**Feito por:**
- Marcelo Granzotto
- Andrey Vintem Valmorbida

**Trabalho:**
- PUC - Fundamentos de Programação Web
- Atividade Somativa 02

Projeto desenvolvido para avaliação acadêmica da PUC.
