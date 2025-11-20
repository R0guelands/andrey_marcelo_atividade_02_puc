# Instruções de Uso - Sistema de Biblioteca

## Sistema Funcionando!

Seu sistema está pronto e rodando! Aqui estão as informações de acesso:

### URLs de Acesso

1. **Aplicação Principal (Sistema de Biblioteca)**
   - URL: http://localhost:8080
   - Faz login, gerencia livros e empréstimos

2. **phpMyAdmin (Gerenciamento do Banco de Dados)**
   - URL: http://localhost:8081
   - Usuário: `biblioteca_user`
   - Senha: `biblioteca_pass`

### Credenciais de Login

A aplicação já vem com usuários pré-cadastrados para teste:

**Usuário 1 (Admin):**
- Email: `admin@biblioteca.com`
- Senha: `password`

**Usuário 2 (João):**
- Email: `joao@email.com`
- Senha: `password`

**Usuário 3 (Maria):**
- Email: `maria@email.com`
- Senha: `password`

## Comandos Docker

### Iniciar o Sistema
```bash
cd /home/marcelo/puc/projeto02
docker compose up -d
```

### Parar o Sistema
```bash
docker compose down
```

### Ver Logs em Tempo Real
```bash
docker compose logs -f web

docker compose logs -f db
```

### Reiniciar os Containers
```bash
docker compose restart
```

### Reconstruir do Zero (caso faça alterações)
```bash
docker compose down -v
docker compose up -d --build
```

### Acessar o Container Web
```bash
docker exec -it biblioteca_web bash
```

### Acessar o MySQL via CLI
```bash
docker exec -it biblioteca_db mysql -ubiblioteca_user -pbiblioteca_pass biblioteca_db
```

## Estrutura da Aplicação

### Páginas Disponíveis

1. **Login** (`/public/login.php`)
   - Tela de autenticação
   - Valida usuário e senha

2. **Dashboard** (`/public/dashboard.php`)
   - Visão geral do sistema
   - Estatísticas em cards
   - Empréstimos recentes

3. **Livros** (`/public/livros.php`)
   - CRUD completo de livros
   - Busca em tempo real
   - Modal para adicionar/editar

4. **Empréstimos** (`/public/emprestimos.php`)
   - CRUD completo de empréstimos
   - Realizar empréstimos
   - Devolver livros
   - Status automático (ativo/atrasado/devolvido)

### Funcionalidades Implementadas

**CRUD de Livros:**
- ✅ Create (Cadastrar novo livro)
- ✅ Read (Listar todos os livros)
- ✅ Update (Editar livro existente)
- ✅ Delete (Excluir livro - com validação)

**CRUD de Empréstimos:**
- ✅ Create (Realizar empréstimo)
- ✅ Read (Listar todos os empréstimos)
- ✅ Update (Devolver livro)
- ✅ Delete (Excluir registro)

**Segurança:**
- ✅ Autenticação com sessões
- ✅ Senhas criptografadas (bcrypt)
- ✅ Proteção contra SQL Injection (PDO)
- ✅ Proteção contra XSS (htmlspecialchars)

**Extras:**
- ✅ Interface responsiva (Bootstrap 5)
- ✅ Busca em tempo real
- ✅ Validação client-side e server-side
- ✅ Mensagens de feedback
- ✅ Controle automático de disponibilidade
- ✅ Status automático de empréstimos

## Testando o Sistema

### 1. Testar Login
1. Acesse http://localhost:8080
2. Use: `admin@biblioteca.com` / `password`
3. Você será redirecionado para o Dashboard

### 2. Testar CRUD de Livros
1. No menu, clique em "Livros"
2. Clique em "+ Novo Livro"
3. Preencha os dados e clique em "Salvar"
4. Para editar: clique em "Editar" em qualquer livro
5. Para excluir: clique em "Excluir" (não funcionará se houver empréstimos ativos)

### 3. Testar CRUD de Empréstimos
1. No menu, clique em "Empréstimos"
2. Clique em "+ Novo Empréstimo"
3. Selecione um livro disponível
4. Clique em "Realizar Empréstimo"
5. Para devolver: clique em "Devolver"
6. Para excluir: clique em "Excluir"

### 4. Verificar Banco de Dados
1. Acesse http://localhost:8081 (phpMyAdmin)
2. Login: `biblioteca_user` / `biblioteca_pass`
3. Selecione o banco `biblioteca_db`
4. Explore as tabelas: `usuarios`, `livros`, `emprestimos`

## Dados Pré-cadastrados

**Livros:**
- Clean Code - Robert C. Martin
- Design Patterns - Gang of Four
- The Pragmatic Programmer - Hunt & Thomas
- Refactoring - Martin Fowler
- Domain-Driven Design - Eric Evans

**Empréstimos:**
- 2 empréstimos já realizados para demonstração

## Resolução de Problemas

### Problema: Containers não iniciam
```bash
docker compose down
docker compose up -d --build
```

### Problema: Erro de conexão com banco
```bash
docker compose logs db

sleep 10 && curl http://localhost:8080
```

### Problema: Permissões negadas
```bash
chmod -R 755 src/
docker compose restart web
```

### Problema: Tabelas não criadas
```bash
docker exec -i biblioteca_db mysql -uroot -proot_password biblioteca_db < init.sql
```

## Requisitos Atendidos

✅ **1. Área de Negócio Definida**
- Sistema de Biblioteca com gerenciamento de livros e empréstimos

✅ **2. Base de Dados MySQL**
- 3 tabelas criadas: `usuarios`, `livros`, `emprestimos`
- Relacionamento 1:N entre `livros` e `emprestimos`
- Relacionamento 1:N entre `usuarios` e `emprestimos`

✅ **3. Autenticação**
- Tabela `usuarios` com senha criptografada (bcrypt)
- Sistema de login funcional

✅ **4. Controle de Acesso**
- Todas as páginas protegidas exceto login
- Verificação de sessão em cada página

✅ **5. Interface Padronizada**
- HTML5 + CSS3 + JavaScript
- Framework Bootstrap 5
- Design responsivo

✅ **6. CRUD Completo**
- INSERT, SELECT, UPDATE, DELETE
- Implementado para livros e empréstimos
- Apenas usuários autenticados têm acesso

## Observações Importantes

- O sistema usa **Docker** exclusivamente
- Não há Nginx, apenas Apache (embutido no PHP)
- Todas as senhas dos usuários de teste são: `password`
- As senhas são criptografadas com `bcrypt` (hash: `$2y$10$...`)
- O banco de dados persiste mesmo após reiniciar containers (volume Docker)

## Próximos Passos (Opcional)

Se quiser expandir o projeto:

1. Adicionar página de cadastro de novos usuários
2. Implementar perfis de usuário (admin, bibliotecário, leitor)
3. Adicionar sistema de multas por atraso
4. Criar relatórios em PDF
5. Implementar sistema de reservas
6. Adicionar fotos dos livros
7. Criar API RESTful

## Suporte

Em caso de dúvidas ou problemas:

1. Verifique os logs: `docker compose logs -f`
2. Reinicie os containers: `docker compose restart`
3. Reconstrua tudo: `docker compose down -v && docker compose up -d --build`

---

**Desenvolvido para avaliação acadêmica da PUC**
**Data: 20/11/2025**
