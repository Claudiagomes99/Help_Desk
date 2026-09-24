<?php
// Funções auxiliares: sessão, permissões, proteção de formulários e apresentação.

// ---------------------------------------------------------------------------
// Opções usadas na listagem, nos formulários e na validação
// ---------------------------------------------------------------------------

// A "classe" é o nome da cor do Bootstrap usada nos selos (badges)
const URGENCIAS = [
    'baixa'   => ['label' => 'Baixa',   'classe' => 'success'],
    'media'   => ['label' => 'Média',   'classe' => 'info'],
    'alta'    => ['label' => 'Alta',    'classe' => 'warning'],
    'critica' => ['label' => 'Crítica', 'classe' => 'danger'],
];

const STATUS = [
    'aberto'       => ['label' => 'Aberto',       'classe' => 'primary'],
    'em_andamento' => ['label' => 'Em andamento', 'classe' => 'warning'],
    'fechado'      => ['label' => 'Fechado',      'classe' => 'secondary'],
];

// ---------------------------------------------------------------------------
// Sessão e permissões
// ---------------------------------------------------------------------------

function redirecionar($url) {
    header('Location: ' . $url);
    exit;
}

function estaLogado() {
    return isset($_SESSION['usuario_id']);
}

function exigirLogin() {
    if (!estaLogado()) {
        redirecionar('login.php');
    }
}

// Usado em login e cadastro: quem já entrou não precisa ver essas telas
function apenasVisitante() {
    if (estaLogado()) {
        redirecionar('index.php');
    }
}

function usuarioTipo() {
    return $_SESSION['usuario_tipo'] ?? null;
}

function ehAdmin() {
    return usuarioTipo() === 'admin';
}

function ehAtendente() {
    return in_array(usuarioTipo(), ['admin', 'atendente'], true);
}

function exigirAtendente() {
    exigirLogin();
    if (!ehAtendente()) {
        redirecionar('index.php');
    }
}

// ---------------------------------------------------------------------------
// Proteção dos formulários (CSRF)
// ---------------------------------------------------------------------------

function tokenCsrf() {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

// Coloque dentro de todo <form method="post">
function campoCsrf() {
    return '<input type="hidden" name="csrf" value="' . h(tokenCsrf()) . '">';
}

// Chame no começo do tratamento de qualquer POST
function exigirCsrf() {
    $enviado = $_POST['csrf'] ?? '';

    if (!is_string($enviado) || !hash_equals(tokenCsrf(), $enviado)) {
        $_SESSION['flash_erro'] = 'Não foi possível confirmar o envio. Atualize a página e tente novamente.';
        redirecionar(estaLogado() ? 'index.php' : 'login.php');
    }
}

// ---------------------------------------------------------------------------
// Apresentação
// ---------------------------------------------------------------------------

function h($texto) {
    return htmlspecialchars($texto ?? '', ENT_QUOTES, 'UTF-8');
}

function urgenciaInfo($urgencia) {
    return URGENCIAS[$urgencia] ?? ['label' => $urgencia, 'classe' => 'secondary'];
}

function statusInfo($status) {
    return STATUS[$status] ?? ['label' => $status, 'classe' => 'secondary'];
}

// Selo colorido e suave (usa as variações "subtle" do Bootstrap 5.3)
function badge(array $info, $prefixo = '') {
    $cor = $info['classe'];
    return '<span class="badge rounded-pill bg-' . $cor . '-subtle text-' . $cor . '-emphasis">'
         . h($prefixo . $info['label']) . '</span>';
}

// Devolve o valor só se ele existir na lista de opções; senão, o padrão.
// Serve para filtros e formulários, onde não dá para confiar no que chega.
function opcaoValida($valor, array $opcoes, $padrao = '') {
    return (is_string($valor) && isset($opcoes[$valor])) ? $valor : $padrao;
}

function dataBR($data) {
    return $data ? date('d/m/Y H:i', strtotime($data)) : '—';
}

function primeiroNome($nome) {
    return explode(' ', trim($nome))[0];
}
