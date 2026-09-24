<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';

// Sair só vale via formulário (POST), para que um link de terceiros não deslogue ninguém
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirecionar('index.php');
}
exigirCsrf();

// Limpa os dados da pessoa e troca o ID da sessão
$_SESSION = [];
session_regenerate_id(true);
$_SESSION['flash_sucesso'] = 'Você saiu da sua conta. Até logo!';

redirecionar('login.php');
