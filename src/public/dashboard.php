<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
verificarLogin();

$usuario = getUsuarioLogado();

$stmt = $pdo->query("SELECT COUNT(*) FROM livros");
$totalLivros = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM livros WHERE quantidade_disponivel > 0");
$livrosDisponiveis = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM emprestimos WHERE status = 'ativo'");
$emprestimosAtivos = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM usuarios");
$totalUsuarios = $stmt->fetchColumn();

$stmt = $pdo->query("
    SELECT e.*, l.titulo, u.nome as usuario_nome
    FROM emprestimos e
    JOIN livros l ON e.livro_id = l.id
    JOIN usuarios u ON e.usuario_id = u.id
    WHERE e.status = 'ativo'
    ORDER BY e.data_devolucao_prevista ASC
    LIMIT 5
");
$emprestimosRecentes = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema de Biblioteca</title>
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
                        <a class="nav-link active" href="/public/dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/public/livros.php">Livros</a>
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
        <h2 class="mb-4">Dashboard</h2>

        <div class="row dashboard-stats">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $totalLivros; ?></div>
                    <div class="stat-label">Total de Livros</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $livrosDisponiveis; ?></div>
                    <div class="stat-label">Livros Disponíveis</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $emprestimosAtivos; ?></div>
                    <div class="stat-label">Empréstimos Ativos</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $totalUsuarios; ?></div>
                    <div class="stat-label">Usuários Cadastrados</div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Empréstimos Ativos Recentes
            </div>
            <div class="card-body">
                <?php if (count($emprestimosRecentes) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Livro</th>
                                    <th>Usuário</th>
                                    <th>Data Empréstimo</th>
                                    <th>Devolução Prevista</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($emprestimosRecentes as $emp):
                                    $hoje = new DateTime();
                                    $dataDevolucao = new DateTime($emp['data_devolucao_prevista']);
                                    $status = $dataDevolucao < $hoje ? 'atrasado' : 'ativo';
                                ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($emp['titulo']); ?></td>
                                        <td><?php echo htmlspecialchars($emp['usuario_nome']); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($emp['data_emprestimo'])); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($emp['data_devolucao_prevista'])); ?></td>
                                        <td>
                                            <span class="badge badge-status-<?php echo $status; ?>">
                                                <?php echo ucfirst($status); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted">Nenhum empréstimo ativo no momento.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/public/js/main.js"></script>
</body>
</html>
