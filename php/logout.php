<?php

session_start();

// Limpar todas as variáveis da sessão
$_SESSION = [];

// Destruir a sessão
session_destroy();

// Voltar para a tela de login
header('Location: login.php');
exit;