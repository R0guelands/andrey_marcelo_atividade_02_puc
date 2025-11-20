CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS livros (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(200) NOT NULL,
    autor VARCHAR(100) NOT NULL,
    isbn VARCHAR(20) UNIQUE,
    ano_publicacao INT,
    quantidade_total INT NOT NULL DEFAULT 1,
    quantidade_disponivel INT NOT NULL DEFAULT 1,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS emprestimos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    livro_id INT NOT NULL,
    usuario_id INT NOT NULL,
    data_emprestimo DATE NOT NULL,
    data_devolucao_prevista DATE NOT NULL,
    data_devolucao_real DATE,
    status ENUM('ativo', 'devolvido', 'atrasado') DEFAULT 'ativo',
    FOREIGN KEY (livro_id) REFERENCES livros(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

INSERT INTO usuarios (nome, email, senha) VALUES
('Admin', 'admin@biblioteca.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('João Silva', 'joao@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Maria Santos', 'maria@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

INSERT INTO livros (titulo, autor, isbn, ano_publicacao, quantidade_total, quantidade_disponivel) VALUES
('Clean Code', 'Robert C. Martin', '978-0132350884', 2008, 5, 5),
('Design Patterns', 'Gang of Four', '978-0201633610', 1994, 3, 2),
('The Pragmatic Programmer', 'Hunt & Thomas', '978-0135957059', 2019, 4, 4),
('Refactoring', 'Martin Fowler', '978-0134757599', 2018, 3, 3),
('Domain-Driven Design', 'Eric Evans', '978-0321125215', 2003, 2, 1);

INSERT INTO emprestimos (livro_id, usuario_id, data_emprestimo, data_devolucao_prevista, status) VALUES
(2, 2, '2025-11-10', '2025-11-24', 'ativo'),
(5, 3, '2025-11-15', '2025-11-29', 'ativo');
