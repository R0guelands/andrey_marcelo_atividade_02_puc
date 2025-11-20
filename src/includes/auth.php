<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function verificarLogin() {
    if (!isset($_SESSION['usuario_id'])) {
        header('Location: /public/login.php');
        exit;
    }
}

function realizarLogin($email, $senha, $pdo) {
    $stmt = $pdo->prepare("SELECT id, nome, email, senha, admin FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();

    if ($usuario && password_verify($senha, $usuario['senha'])) {
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nome'] = $usuario['nome'];
        $_SESSION['usuario_email'] = $usuario['email'];
        $_SESSION['usuario_admin'] = (bool)$usuario['admin'];
        return true;
    }
    return false;
}

function realizarLogout() {
    session_destroy();
    header('Location: /public/login.php');
    exit;
}

function getUsuarioLogado() {
    return [
        'id' => $_SESSION['usuario_id'] ?? null,
        'nome' => $_SESSION['usuario_nome'] ?? null,
        'email' => $_SESSION['usuario_email'] ?? null,
        'admin' => $_SESSION['usuario_admin'] ?? false
    ];
}

function verificarAdmin() {
    verificarLogin();
    if (!isset($_SESSION['usuario_admin']) || !$_SESSION['usuario_admin']) {
        http_response_code(403);
        die('Acesso negado. Apenas administradores podem acessar esta página.');
    }
}

function registrarUsuario($nome, $email, $senha, $pdo) {
    try {
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            return 'Este email já está cadastrado!';
        }

        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)");
        $stmt->execute([$nome, $email, $senhaHash]);

        return true;
    } catch (PDOException $e) {
        return 'Erro ao cadastrar usuário. Tente novamente.';
    }
}
?>
