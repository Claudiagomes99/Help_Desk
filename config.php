<?php
// Configurações gerais: banco de dados, fuso horário e sessão.
// Altere apenas as constantes abaixo para o seu ambiente.

define('DB_HOST', 'localhost');
define('DB_NAME', 'chamados_db');
define('DB_USER', 'root');
define('DB_PASS', '');

// O fuso horário vem primeiro para que o PHP e o MySQL falem a mesma hora
date_default_timezone_set('America/Sao_Paulo');

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            // Faz o NOW() do MySQL usar o mesmo fuso do PHP (ex.: -03:00)
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET time_zone = '" . date('P') . "'",
        ]
    );
} catch (PDOException $e) {
    // O detalhe técnico vai para o log do servidor, não para a tela do usuário
    error_log('Falha ao conectar no banco: ' . $e->getMessage());
    http_response_code(500);
    die('Não foi possível conectar ao banco de dados. Confira as credenciais no config.php.');
}

// Cookie de sessão mais seguro: não acessível por JavaScript
session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();
