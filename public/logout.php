<?php
session_start();

// Destrói todas as variáveis de sessão
$_SESSION = [];

// Se deseja destruir a sessão, também use a função session_destroy()
session_destroy();

// Redireciona para a página de login ou inicial
header("Location: login.php");
exit();
?>
