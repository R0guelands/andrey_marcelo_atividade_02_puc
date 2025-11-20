SELECT * FROM usuarios;

SELECT * FROM livros;

SELECT * FROM emprestimos;

SELECT
    e.id AS 'ID Empréstimo',
    l.titulo AS 'Livro',
    l.autor AS 'Autor',
    u.nome AS 'Usuário',
    u.email AS 'Email',
    e.data_emprestimo AS 'Data Empréstimo',
    e.data_devolucao_prevista AS 'Devolução Prevista',
    e.data_devolucao_real AS 'Devolução Real',
    e.status AS 'Status'
FROM emprestimos e
INNER JOIN livros l ON e.livro_id = l.id
INNER JOIN usuarios u ON e.usuario_id = u.id
ORDER BY e.data_emprestimo DESC;

SELECT
    l.id,
    l.titulo AS 'Título',
    l.autor AS 'Autor',
    l.isbn AS 'ISBN',
    l.ano_publicacao AS 'Ano',
    l.quantidade_total AS 'Total',
    l.quantidade_disponivel AS 'Disponível',
    (l.quantidade_total - l.quantidade_disponivel) AS 'Emprestados'
FROM livros l
ORDER BY l.titulo;

SELECT
    u.nome AS 'Usuário',
    COUNT(e.id) AS 'Total de Empréstimos',
    SUM(CASE WHEN e.status = 'ativo' THEN 1 ELSE 0 END) AS 'Empréstimos Ativos',
    SUM(CASE WHEN e.status = 'devolvido' THEN 1 ELSE 0 END) AS 'Devolvidos',
    SUM(CASE WHEN e.status = 'atrasado' THEN 1 ELSE 0 END) AS 'Atrasados'
FROM usuarios u
LEFT JOIN emprestimos e ON u.id = e.usuario_id
GROUP BY u.id, u.nome
ORDER BY COUNT(e.id) DESC;
