<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

if (isset($_SESSION['usuario_id'])) {
    header('Location: /public/dashboard.php');
    exit;
}

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $confirmar_senha = $_POST['confirmar_senha'] ?? '';

    if (empty($nome) || empty($email) || empty($senha)) {
        $erro = 'Todos os campos são obrigatórios!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'Email inválido!';
    } elseif (strlen($senha) < 6) {
        $erro = 'A senha deve ter no mínimo 6 caracteres!';
    } elseif ($senha !== $confirmar_senha) {
        $erro = 'As senhas não coincidem!';
    } else {
        $resultado = registrarUsuario($nome, $email, $senha, $pdo);
        if ($resultado === true) {
            $sucesso = 'Usuário cadastrado com sucesso! Você pode fazer login agora.';
        } else {
            $erro = $resultado;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - Sistema de Biblioteca</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/public/css/style.css">
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="card">
                <div class="card-header text-center">
                    <h4>Sistema de Biblioteca</h4>
                    <p class="mb-0">Cadastro de Novo Usuário</p>
                </div>
                <div class="card-body">
                    <?php if ($erro): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($erro); ?></div>
                    <?php endif; ?>

                    <?php if ($sucesso): ?>
                        <div class="alert alert-success"><?php echo htmlspecialchars($sucesso); ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome Completo</label>
                            <input type="text" class="form-control" id="nome" name="nome"
                                   value="<?php echo htmlspecialchars($_POST['nome'] ?? ''); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email"
                                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="senha" class="form-label">Senha</label>
                            <input type="password" class="form-control" id="senha" name="senha"
                                   minlength="6" required>
                            <small class="text-muted">Mínimo de 6 caracteres</small>
                        </div>
                        <div class="mb-3">
                            <label for="confirmar_senha" class="form-label">Confirmar Senha</label>
                            <input type="password" class="form-control" id="confirmar_senha"
                                   name="confirmar_senha" minlength="6" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Cadastrar</button>
                    </form>

                    <div class="mt-3 text-center">
                        <a href="/public/login.php" class="text-decoration-none">
                            Já tem uma conta? Faça login
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
