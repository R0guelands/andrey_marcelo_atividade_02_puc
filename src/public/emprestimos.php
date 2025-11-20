<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
verificarLogin();

$usuario = getUsuarioLogado();
$mensagem = '';
$tipoMensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare("SELECT quantidade_disponivel FROM livros WHERE id = ?");
            $stmt->execute([$_POST['livro_id']]);
            $disponivel = $stmt->fetchColumn();

            if ($disponivel > 0) {
                $stmt = $pdo->prepare("INSERT INTO emprestimos (livro_id, usuario_id, data_emprestimo, data_devolucao_prevista, status) VALUES (?, ?, ?, ?, 'ativo')");
                $stmt->execute([
                    $_POST['livro_id'],
                    $usuario['id'],
                    $_POST['data_emprestimo'],
                    $_POST['data_devolucao_prevista']
                ]);

                $stmt = $pdo->prepare("UPDATE livros SET quantidade_disponivel = quantidade_disponivel - 1 WHERE id = ?");
                $stmt->execute([$_POST['livro_id']]);

                $pdo->commit();
                $mensagem = 'Empréstimo realizado com sucesso!';
                $tipoMensagem = 'success';
            } else {
                $pdo->rollBack();
                $mensagem = 'Livro não disponível para empréstimo!';
                $tipoMensagem = 'danger';
            }
        } catch (Exception $e) {
            $pdo->rollBack();
            $mensagem = 'Erro ao realizar empréstimo!';
            $tipoMensagem = 'danger';
        }
    } elseif ($action === 'devolver') {
        if (!$usuario['admin']) {
            http_response_code(403);
            die('Acesso negado. Apenas administradores podem encerrar empréstimos.');
        }

        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare("UPDATE emprestimos SET status = 'devolvido', data_devolucao_real = CURDATE() WHERE id = ?");
            $stmt->execute([$_POST['id']]);

            $stmt = $pdo->prepare("SELECT livro_id FROM emprestimos WHERE id = ?");
            $stmt->execute([$_POST['id']]);
            $livroId = $stmt->fetchColumn();

            $stmt = $pdo->prepare("UPDATE livros SET quantidade_disponivel = quantidade_disponivel + 1 WHERE id = ?");
            $stmt->execute([$livroId]);

            $pdo->commit();
            $mensagem = 'Livro devolvido com sucesso!';
            $tipoMensagem = 'success';
        } catch (Exception $e) {
            $pdo->rollBack();
            $mensagem = 'Erro ao devolver livro!';
            $tipoMensagem = 'danger';
        }
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare("SELECT livro_id, status FROM emprestimos WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $emprestimo = $stmt->fetch();

        if ($emprestimo['status'] === 'ativo') {
            $stmt = $pdo->prepare("UPDATE livros SET quantidade_disponivel = quantidade_disponivel + 1 WHERE id = ?");
            $stmt->execute([$emprestimo['livro_id']]);
        }

        $stmt = $pdo->prepare("DELETE FROM emprestimos WHERE id = ?");
        $stmt->execute([$_GET['id']]);

        $pdo->commit();
        $mensagem = 'Empréstimo excluído com sucesso!';
        $tipoMensagem = 'success';
    } catch (Exception $e) {
        $pdo->rollBack();
        $mensagem = 'Erro ao excluir empréstimo!';
        $tipoMensagem = 'danger';
    }
}

$stmt = $pdo->query("
    SELECT e.*, l.titulo, l.autor, u.nome as usuario_nome
    FROM emprestimos e
    JOIN livros l ON e.livro_id = l.id
    JOIN usuarios u ON e.usuario_id = u.id
    ORDER BY e.data_emprestimo DESC
");
$emprestimos = $stmt->fetchAll();

$livrosDisponiveis = $pdo->query("SELECT * FROM livros WHERE quantidade_disponivel > 0 ORDER BY titulo")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Empréstimos - Sistema de Biblioteca</title>
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
                        <a class="nav-link" href="/public/livros.php">Livros</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/public/emprestimos.php">Empréstimos</a>
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
            <h2>Gerenciar Empréstimos</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalEmprestimo">
                + Novo Empréstimo
            </button>
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
                        Lista de Empréstimos
                    </div>
                    <div class="col-md-6">
                        <input type="text" id="searchInput" class="form-control form-control-sm"
                               placeholder="Buscar empréstimo..."
                               onkeyup="filtrarTabela('searchInput', 'tabelaEmprestimos')">
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="tabelaEmprestimos">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Livro</th>
                                <th>Autor</th>
                                <th>Usuário</th>
                                <th>Data Empréstimo</th>
                                <th>Devolução Prevista</th>
                                <th>Devolução Real</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($emprestimos as $emp):
                                $hoje = new DateTime();
                                $dataDevolucao = new DateTime($emp['data_devolucao_prevista']);
                                $statusReal = $emp['status'];
                                if ($statusReal === 'ativo' && $dataDevolucao < $hoje) {
                                    $statusReal = 'atrasado';
                                }
                            ?>
                                <tr>
                                    <td><?php echo $emp['id']; ?></td>
                                    <td><?php echo htmlspecialchars($emp['titulo']); ?></td>
                                    <td><?php echo htmlspecialchars($emp['autor']); ?></td>
                                    <td><?php echo htmlspecialchars($emp['usuario_nome']); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($emp['data_emprestimo'])); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($emp['data_devolucao_prevista'])); ?></td>
                                    <td><?php echo $emp['data_devolucao_real'] ? date('d/m/Y', strtotime($emp['data_devolucao_real'])) : '-'; ?></td>
                                    <td>
                                        <span class="badge badge-status-<?php echo $statusReal; ?>">
                                            <?php echo ucfirst($statusReal); ?>
                                        </span>
                                    </td>
                                    <td class="action-buttons">
                                        <?php if ($usuario['admin']): ?>
                                            <?php if ($emp['status'] === 'ativo'): ?>
                                                <form method="POST" style="display:inline;">
                                                    <input type="hidden" name="action" value="devolver">
                                                    <input type="hidden" name="id" value="<?php echo $emp['id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-success">Devolver</button>
                                                </form>
                                            <?php endif; ?>
                                            <button onclick="confirmarExclusao('emprestimos', <?php echo $emp['id']; ?>)"
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

    <div class="modal fade" id="modalEmprestimo" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Novo Empréstimo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" onsubmit="return validarFormularioEmprestimo()">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="create">

                        <div class="mb-3">
                            <label for="livro_id" class="form-label">Livro</label>
                            <select class="form-select" id="livro_id" name="livro_id" required>
                                <option value="">Selecione um livro</option>
                                <?php foreach ($livrosDisponiveis as $livro): ?>
                                    <option value="<?php echo $livro['id']; ?>">
                                        <?php echo htmlspecialchars($livro['titulo']); ?> - <?php echo htmlspecialchars($livro['autor']); ?>
                                        (Disponível: <?php echo $livro['quantidade_disponivel']; ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="data_emprestimo" class="form-label">Data do Empréstimo</label>
                            <input type="date" class="form-control" id="data_emprestimo" name="data_emprestimo"
                                   value="<?php echo date('Y-m-d'); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="data_devolucao_prevista" class="form-label">Data de Devolução Prevista</label>
                            <input type="date" class="form-control" id="data_devolucao_prevista" name="data_devolucao_prevista"
                                   value="<?php echo date('Y-m-d', strtotime('+14 days')); ?>" required>
                        </div>

                        <div class="alert alert-info">
                            <strong>Usuário:</strong> <?php echo htmlspecialchars($usuario['nome']); ?>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Realizar Empréstimo</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/public/js/main.js"></script>
</body>
</html>
