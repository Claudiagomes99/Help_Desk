<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';
exigirLogin();

$erro      = '';
$titulo    = '';
$descricao = '';
$urgencia  = 'media';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    exigirCsrf();

    $titulo    = trim($_POST['titulo'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $urgencia  = opcaoValida($_POST['urgencia'] ?? null, URGENCIAS);

    if ($titulo === '' || $descricao === '') {
        $erro = 'Preencha o título e a descrição do chamado.';
    } elseif (mb_strlen($titulo) > 150) {
        $erro = 'O título pode ter no máximo 150 caracteres.';
    } elseif (mb_strlen($descricao) > 5000) {
        $erro = 'A descrição pode ter no máximo 5000 caracteres.';
    } elseif ($urgencia === '') {
        $erro = 'Escolha um nível de urgência válido.';
    } else {
        // O status começa como "aberto" pelo padrão da tabela
        $stmt = $pdo->prepare(
            'INSERT INTO chamados (titulo, descricao, urgencia, usuario_id) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$titulo, $descricao, $urgencia, $_SESSION['usuario_id']]);
        $novoId = (int)$pdo->lastInsertId();

        $_SESSION['flash_sucesso'] = "Chamado #$novoId aberto! Já já alguém da equipe responde.";
        redirecionar('chamado.php?id=' . $novoId);
    }

    // Se deu erro e a urgência era inválida, volta para o padrão no formulário
    if ($urgencia === '') {
        $urgencia = 'media';
    }
}

$tituloPagina = 'Abrir chamado';
require_once __DIR__ . '/includes/header.php';
?>
<div class="row justify-content-center">
  <div class="col-lg-10 col-xl-9">
    <div class="card shadow-sm">
      <div class="card-body p-4">
        <h4 class="mb-1">Abrir novo chamado</h4>
        <p class="text-muted mb-4">Conte o que está acontecendo. Quanto mais detalhes, mais rápido a gente resolve.</p>

        <?php if ($erro): ?>
          <div class="alert alert-danger"><?= h($erro) ?></div>
        <?php endif; ?>

        <form method="post">
          <?= campoCsrf() ?>
          <div class="mb-3">
            <label class="form-label" for="titulo">Título</label>
            <input type="text" id="titulo" name="titulo" class="form-control" maxlength="150"
                   placeholder="Ex.: Não consigo acessar o e-mail" value="<?= h($titulo) ?>" required autofocus>
          </div>
          <div class="mb-3">
            <label class="form-label" for="descricao">Descrição</label>
            <textarea id="descricao" name="descricao" class="form-control" rows="6" maxlength="5000"
                      placeholder="O que aconteceu? Desde quando? Apareceu alguma mensagem de erro?" required><?= h($descricao) ?></textarea>
          </div>
          <div class="mb-4">
            <label class="form-label" for="urgencia">Urgência</label>
            <select id="urgencia" name="urgencia" class="form-select" required>
              <?php foreach (URGENCIAS as $valor => $info): ?>
                <option value="<?= $valor ?>" <?= $urgencia === $valor ? 'selected' : '' ?>><?= h($info['label']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary"><i class="bi bi-send"></i> Abrir chamado</button>
            <a href="index.php" class="btn btn-outline-secondary">Cancelar</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
