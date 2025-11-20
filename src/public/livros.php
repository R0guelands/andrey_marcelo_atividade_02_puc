<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
verificarLogin();

$usuario = getUsuarioLogado();
$mensagem = '';
$tipoMensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$usuario['admin']) {
        http_response_code(403);
        die('Acesso negado. Apenas administradores podem gerenciar livros.');
    }

    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $stmt = $pdo->prepare("INSERT INTO livros (titulo, autor, isbn, ano_publicacao, quantidade_total, quantidade_disponivel) VALUES (?, ?, ?, ?, ?, ?)");
        $quantidade = intval($_POST['quantidade_total']);
        $stmt->execute([
            $_POST['titulo'],
            $_POST['autor'],
            $_POST['isbn'],
            $_POST['ano_publicacao'],
            $quantidade,
            $quantidade
        ]);
        $mensagem = 'Livro cadastrado com sucesso!';
        $tipoMensagem = 'success';
    } elseif ($action === 'update') {
        $stmt = $pdo->prepare("UPDATE livros SET titulo = ?, autor = ?, isbn = ?, ano_publicacao = ?, quantidade_total = ? WHERE id = ?");
        $stmt->execute([
            $_POST['titulo'],
            $_POST['autor'],
            $_POST['isbn'],
            $_POST['ano_publicacao'],
            $_POST['quantidade_total'],
            $_POST['id']
        ]);
        $mensagem = 'Livro atualizado com sucesso!';
        $tipoMensagem = 'success';
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    if (!$usuario['admin']) {
        http_response_code(403);
        die('Acesso negado. Apenas administradores podem gerenciar livros.');
    }

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM emprestimos WHERE livro_id = ? AND status = 'ativo'");
    $stmt->execute([$_GET['id']]);
    if ($stmt->fetchColumn() > 0) {
        $mensagem = 'Não é possível excluir um livro com empréstimos ativos!';
        $tipoMensagem = 'danger';
    } else {
        $stmt = $pdo->prepare("DELETE FROM livros WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $mensagem = 'Livro excluído com sucesso!';
        $tipoMensagem = 'success';
    }
}

$livros = $pdo->query("SELECT * FROM livros ORDER BY titulo")->fetchAll();

$livroEdit = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM livros WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $livroEdit = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Livros - Sistema de Biblioteca</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/public/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="/public/dashboard.php">Biblioteca</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/public/dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/public/livros.php">Livros</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/public/emprestimos.php">Empréstimos</a>
                    </li>
                </ul>
                <span class="navbar-text me-3">
                    Olá, <?php echo htmlspecialchars($usuario['nome']); ?>
                </span>
                <a href="/public/logout.php" class="btn btn-outline-light btn-sm">Sair</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Gerenciar Livros</h2>
            <?php if ($usuario['admin']): ?>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalLivro">
                    + Novo Livro
                </button>
            <?php endif; ?>
        </div>

        <?php if ($mensagem): ?>
            <div class="alert alert-<?php echo $tipoMensagem; ?> alert-dismissible fade show">
                <?php echo htmlspecialchars($mensagem); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        Lista de Livros
                    </div>
                    <div class="col-md-6">
                        <input type="text" id="searchInput" class="form-control form-control-sm"
                               placeholder="Buscar livro..."
                               onkeyup="filtrarTabela('searchInput', 'tabelaLivros')">
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="tabelaLivros">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Título</th>
                                <th>Autor</th>
                                <th>ISBN</th>
                                <th>Ano</th>
                                <th>Total</th>
                                <th>Disponível</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($livros as $livro): ?>
                                <tr>
                                    <td><?php echo $livro['id']; ?></td>
                                    <td><?php echo htmlspecialchars($livro['titulo']); ?></td>
                                    <td><?php echo htmlspecialchars($livro['autor']); ?></td>
                                    <td><?php echo htmlspecialchars($livro['isbn']); ?></td>
                                    <td><?php echo $livro['ano_publicacao']; ?></td>
                                    <td><?php echo $livro['quantidade_total']; ?></td>
                                    <td>
                                        <span class="badge <?php echo $livro['quantidade_disponivel'] > 0 ? 'bg-success' : 'bg-danger'; ?>">
                                            <?php echo $livro['quantidade_disponivel']; ?>
                                        </span>
                                    </td>
                                    <td class="action-buttons">
                                        <?php if ($usuario['admin']): ?>
                                            <a href="?edit=<?php echo $livro['id']; ?>" class="btn btn-sm btn-warning">Editar</a>
                                            <button onclick="confirmarExclusao('livros', <?php echo $livro['id']; ?>)"
                                                    class="btn btn-sm btn-danger">Excluir</button>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalLivro" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><?php echo $livroEdit ? 'Editar' : 'Novo'; ?> Livro</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" onsubmit="return validarFormularioLivro()">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="<?php echo $livroEdit ? 'update' : 'create'; ?>">
                        <?php if ($livroEdit): ?>
                            <input type="hidden" name="id" value="<?php echo $livroEdit['id']; ?>">
                        <?php endif; ?>

                        <div class="mb-3">
                            <label for="titulo" class="form-label">Título</label>
                            <input type="text" class="form-control" id="titulo" name="titulo"
                                   value="<?php echo $livroEdit ? htmlspecialchars($livroEdit['titulo']) : ''; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="autor" class="form-label">Autor</label>
                            <input type="text" class="form-control" id="autor" name="autor"
                                   value="<?php echo $livroEdit ? htmlspecialchars($livroEdit['autor']) : ''; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="isbn" class="form-label">ISBN</label>
                            <input type="text" class="form-control" id="isbn" name="isbn"
                                   value="<?php echo $livroEdit ? htmlspecialchars($livroEdit['isbn']) : ''; ?>">
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="ano_publicacao" class="form-label">Ano de Publicação</label>
                                    <input type="number" class="form-control" id="ano_publicacao" name="ano_publicacao"
                                           value="<?php echo $livroEdit ? $livroEdit['ano_publicacao'] : ''; ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="quantidade_total" class="form-label">Quantidade</label>
                                    <input type="number" class="form-control" id="quantidade_total" name="quantidade_total"
                                           value="<?php echo $livroEdit ? $livroEdit['quantidade_total'] : 1; ?>" min="1" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/public/js/main.js"></script>
    <?php if ($livroEdit): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                new bootstrap.Modal(document.getElementById('modalLivro')).show();
            });
        </script>
    <?php endif; ?>
</body>
</html>
