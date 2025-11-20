<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

if (isset($_SESSION['usuario_id'])) {
    header('Location: /public/dashboard.php');
    exit;
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';

    if (realizarLogin($email, $senha, $pdo)) {
        header('Location: /public/dashboard.php');
        exit;
    } else {
        $erro = 'Email ou senha inválidos!';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema de Biblioteca</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/public/css/style.css">
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="card">
                <div class="card-header text-center">
                    <h4>Sistema de Biblioteca</h4>
                </div>
                <div class="card-body">
                    <?php if ($erro): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($erro); ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="senha" class="form-label">Senha</label>
                            <input type="password" class="form-control" id="senha" name="senha" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Entrar</button>
                    </form>

                    <div class="mt-3 text-center">
                        <a href="/public/register.php" class="text-decoration-none">
                            Não tem uma conta? Cadastre-se
                        </a>
                    </div>

                    <div class="mt-2 text-center">
                        <small class="text-muted">
                            Usuário teste: admin@biblioteca.com<br>
                            Senha: password
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
