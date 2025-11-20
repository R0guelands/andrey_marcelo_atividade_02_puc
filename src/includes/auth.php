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
    $stmt = $pdo->prepare("SELECT id, nome, email, senha FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();

    if ($usuario && password_verify($senha, $usuario['senha'])) {
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nome'] = $usuario['nome'];
        $_SESSION['usuario_email'] = $usuario['email'];
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
        'email' => $_SESSION['usuario_email'] ?? null
    ];
}
?>
