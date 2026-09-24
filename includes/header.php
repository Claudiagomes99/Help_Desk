<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/auth.php';

// Serve para marcar o item do menu da página atual
$paginaAtual = basename($_SERVER['SCRIPT_NAME']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($tituloPagina) ? h($tituloPagina) . ' - ' : '' ?>Sistema de Chamados</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="<?= h($classeBody ?? '') ?>">

<?php if (estaLogado()): ?>
<nav class="navbar navbar-expand-lg navbar-dark navbar-lilas mb-4">
  <div class="container">
    <a class="navbar-brand" href="index.php"><i class="bi bi-headset"></i> Sistema de Chamados</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu" aria-label="Abrir menu">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMenu">
      <ul class="navbar-nav me-auto">
        <li class="nav-item">
          <a class="nav-link <?= in_array($paginaAtual, ['index.php', 'chamado.php']) ? 'active' : '' ?>" href="index.php">
            <i class="bi bi-list-task"></i> Chamados
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= $paginaAtual === 'novo_chamado.php' ? 'active' : '' ?>" href="novo_chamado.php">
            <i class="bi bi-plus-circle"></i> Abrir chamado
          </a>
        </li>
      </ul>

      <span class="navbar-text text-white me-3">
        Olá, <strong><?= h(primeiroNome($_SESSION['usuario_nome'])) ?></strong>
        <span class="badge rounded-pill bg-white bg-opacity-25 ms-1"><?= h($_SESSION['usuario_tipo']) ?></span>
      </span>

      <form action="logout.php" method="post" class="d-inline">
        <?= campoCsrf() ?>
        <button type="submit" class="btn btn-outline-light btn-sm"><i class="bi bi-box-arrow-right"></i> Sair</button>
      </form>
    </div>
  </div>
</nav>
<?php endif; ?>

<div class="container mb-5">
<?php foreach (['sucesso' => 'success', 'erro' => 'danger'] as $chave => $classe): ?>
  <?php if (!empty($_SESSION['flash_' . $chave])): ?>
    <div class="alert alert-<?= $classe ?> alert-dismissible fade show" role="alert">
      <?= h($_SESSION['flash_' . $chave]) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
    </div>
    <?php unset($_SESSION['flash_' . $chave]); ?>
  <?php endif; ?>
<?php endforeach; ?>
