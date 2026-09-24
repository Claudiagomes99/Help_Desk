<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';
exigirLogin();

$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare(
    "SELECT c.*, u.nome AS solicitante_nome, a.nome AS atendente_nome
     FROM chamados c
     JOIN usuarios u ON u.id = c.usuario_id
     LEFT JOIN usuarios a ON a.id = c.atendente_id
     WHERE c.id = ?"
);
$stmt->execute([$id]);
$chamado = $stmt->fetch();

if (!$chamado) {
    $_SESSION['flash_erro'] = 'Não encontramos esse chamado.';
    redirecionar('index.php');
}

// Atendente e admin veem qualquer chamado; cliente só os seus
if (!ehAtendente() && (int)$chamado['usuario_id'] !== (int)$_SESSION['usuario_id']) {
    $_SESSION['flash_erro'] = 'Você não tem permissão para ver esse chamado.';
    redirecionar('index.php');
}

$fechado = $chamado['status'] === 'fechado';

// ---------------------------------------------------------------------------
// Ações (todas terminam em redirecionamento, então a página nunca "reenvia")
// ---------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    exigirCsrf();
    $acao = $_POST['acao'] ?? '';
    $voltar = 'chamado.php?id=' . $id;

    if ($acao === 'atualizar' && ehAtendente()) {
        $novoStatus   = opcaoValida($_POST['status'] ?? null, STATUS);
        $novaUrgencia = opcaoValida($_POST['urgencia'] ?? null, URGENCIAS);

        if ($novoStatus === '' || $novaUrgencia === '') {
            $_SESSION['flash_erro'] = 'Status ou urgência inválidos.';
            redirecionar($voltar);
        }

        // - Quem mexe primeiro assume o chamado; depois disso o atendente não muda sozinho
        // - Ao fechar, guarda a data (sem sobrescrever uma data anterior); ao reabrir, limpa
        $stmt = $pdo->prepare(
            "UPDATE chamados
             SET status = ?,
                 urgencia = ?,
                 atendente_id = COALESCE(atendente_id, ?),
                 data_fechamento = CASE WHEN ? = 'fechado' THEN COALESCE(data_fechamento, NOW()) ELSE NULL END
             WHERE id = ?"
        );
        $stmt->execute([$novoStatus, $novaUrgencia, $_SESSION['usuario_id'], $novoStatus, $id]);

        $_SESSION['flash_sucesso'] = 'Alterações salvas.';
        redirecionar($voltar);
    }

    if ($acao === 'fechar' && ehAtendente()) {
        $stmt = $pdo->prepare(
            "UPDATE chamados
             SET status = 'fechado',
                 data_fechamento = NOW(),
                 atendente_id = COALESCE(atendente_id, ?)
             WHERE id = ? AND status <> 'fechado'"
        );
        $stmt->execute([$_SESSION['usuario_id'], $id]);

        $_SESSION['flash_sucesso'] = "Chamado #$id encerrado.";
        redirecionar($voltar);
    }
}

$tituloPagina = 'Chamado #' . $chamado['id'];
require_once __DIR__ . '/includes/header.php';
?>

<div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-4">
  <div>
    <h3 class="mb-2"><span class="text-muted">#<?= (int)$chamado['id'] ?></span> <?= h($chamado['titulo']) ?></h3>
    <?= badge(urgenciaInfo($chamado['urgencia']), 'Urgência ') ?>
    <?= badge(statusInfo($chamado['status'])) ?>
  </div>
  <a href="index.php" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Voltar</a>
</div>

<div class="row g-4">
  <div class="<?= ehAtendente() ? 'col-lg-8' : 'col-12' ?>">

    <div class="card shadow-sm">
      <div class="card-body p-4">
        <h6 class="text-muted mb-2">Descrição</h6>
        <p class="texto-livre mb-0"><?= h($chamado['descricao']) ?></p>
      </div>
      <div class="card-footer row g-2 mx-0 text-muted">
        <div class="col-sm-6"><i class="bi bi-person"></i> Solicitante: <?= h($chamado['solicitante_nome']) ?></div>
        <div class="col-sm-6"><i class="bi bi-headset"></i> Atendente: <?= h($chamado['atendente_nome'] ?? 'ainda sem atendente') ?></div>
        <div class="col-sm-6"><i class="bi bi-calendar-plus"></i> Aberto em <?= dataBR($chamado['data_abertura']) ?></div>
        <div class="col-sm-6"><i class="bi bi-calendar-check"></i> Encerrado em <?= dataBR($chamado['data_fechamento']) ?></div>
      </div>
    </div>
  </div>

  <?php if (ehAtendente()): ?>
  <div class="col-lg-4">
    <div class="card shadow-sm">
      <div class="card-body p-4">
        <h6 class="text-muted mb-3"><i class="bi bi-gear"></i> Gerenciar chamado</h6>

        <form method="post">
          <?= campoCsrf() ?>
          <input type="hidden" name="acao" value="atualizar">
          <div class="mb-3">
            <label class="form-label" for="status">Status</label>
            <select id="status" name="status" class="form-select">
              <?php foreach (STATUS as $valor => $info): ?>
                <option value="<?= $valor ?>" <?= $chamado['status'] === $valor ? 'selected' : '' ?>><?= h($info['label']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label" for="urgencia">Urgência</label>
            <select id="urgencia" name="urgencia" class="form-select">
              <?php foreach (URGENCIAS as $valor => $info): ?>
                <option value="<?= $valor ?>" <?= $chamado['urgencia'] === $valor ? 'selected' : '' ?>><?= h($info['label']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <button type="submit" class="btn btn-primary w-100">Salvar alterações</button>
        </form>

        <?php if (!$fechado): ?>
          <hr>
          <form method="post" class="js-confirmar-fechamento">
            <?= campoCsrf() ?>
            <input type="hidden" name="acao" value="fechar">
            <button type="submit" class="btn btn-outline-danger w-100">
              <i class="bi bi-x-circle"></i> Encerrar chamado
            </button>
          </form>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
