<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';
exigirLogin();

// Filtros: valores fora da lista são simplesmente ignorados
$filtroStatus   = opcaoValida($_GET['status'] ?? null, STATUS);
$filtroUrgencia = opcaoValida($_GET['urgencia'] ?? null, URGENCIAS);

$sql = "SELECT c.*, u.nome AS solicitante_nome, a.nome AS atendente_nome
        FROM chamados c
        JOIN usuarios u ON u.id = c.usuario_id
        LEFT JOIN usuarios a ON a.id = c.atendente_id
        WHERE 1=1";
$params = [];

// Cliente só enxerga os próprios chamados; admin e atendente veem todos
if (!ehAtendente()) {
    $sql .= " AND c.usuario_id = ?";
    $params[] = $_SESSION['usuario_id'];
}
if ($filtroStatus !== '') {
    $sql .= " AND c.status = ?";
    $params[] = $filtroStatus;
}
if ($filtroUrgencia !== '') {
    $sql .= " AND c.urgencia = ?";
    $params[] = $filtroUrgencia;
}

// Os mais urgentes primeiro; dentro da mesma urgência, os mais recentes
$sql .= " ORDER BY
            CASE c.urgencia WHEN 'critica' THEN 1 WHEN 'alta' THEN 2 WHEN 'media' THEN 3 ELSE 4 END,
            c.data_abertura DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$chamados = $stmt->fetchAll();

$total       = count($chamados);
$colunas     = ehAtendente() ? 8 : 7;
$temFiltro   = ($filtroStatus !== '' || $filtroUrgencia !== '');

$tituloPagina = 'Chamados';
require_once __DIR__ . '/includes/header.php';
?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
  <div>
    <h3 class="mb-0">Chamados</h3>
    <small class="text-muted">
      <?= $total === 1 ? '1 chamado' : $total . ' chamados' ?><?= $temFiltro ? ' com esses filtros' : '' ?>
    </small>
  </div>
  <a href="novo_chamado.php" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Abrir chamado</a>
</div>

<form method="get" class="filtros row g-2 mb-4">
  <div class="col-12 col-sm-auto">
    <select name="status" class="form-select" aria-label="Filtrar por status">
      <option value="">Todos os status</option>
      <?php foreach (STATUS as $valor => $info): ?>
        <option value="<?= $valor ?>" <?= $filtroStatus === $valor ? 'selected' : '' ?>><?= h($info['label']) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-12 col-sm-auto">
    <select name="urgencia" class="form-select" aria-label="Filtrar por urgência">
      <option value="">Todas as urgências</option>
      <?php foreach (URGENCIAS as $valor => $info): ?>
        <option value="<?= $valor ?>" <?= $filtroUrgencia === $valor ? 'selected' : '' ?>><?= h($info['label']) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-auto">
    <button type="submit" class="btn btn-outline-primary"><i class="bi bi-funnel"></i> Filtrar</button>
  </div>
  <?php if ($temFiltro): ?>
    <div class="col-auto">
      <a href="index.php" class="btn btn-link text-decoration-none">Limpar filtros</a>
    </div>
  <?php endif; ?>
</form>

<div class="card shadow-sm">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead>
        <tr>
          <th>#</th>
          <th>Título</th>
          <?php if (ehAtendente()): ?><th>Solicitante</th><?php endif; ?>
          <th>Urgência</th>
          <th>Status</th>
          <th>Atendente</th>
          <th>Abertura</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php if (!$chamados): ?>
          <tr>
            <td colspan="<?= $colunas ?>" class="text-center text-muted py-5">
              <?php if ($temFiltro): ?>
                Nenhum chamado bate com esses filtros.
              <?php else: ?>
                Nenhum chamado por aqui ainda.
                <a href="novo_chamado.php">Abrir o primeiro</a>
              <?php endif; ?>
            </td>
          </tr>
        <?php endif; ?>

        <?php foreach ($chamados as $c): ?>
          <tr>
            <td class="text-muted">#<?= (int)$c['id'] ?></td>
            <td class="fw-semibold"><?= h($c['titulo']) ?></td>
            <?php if (ehAtendente()): ?><td><?= h($c['solicitante_nome']) ?></td><?php endif; ?>
            <td><?= badge(urgenciaInfo($c['urgencia'])) ?></td>
            <td><?= badge(statusInfo($c['status'])) ?></td>
            <td><?= h($c['atendente_nome'] ?? '—') ?></td>
            <td class="text-muted"><?= dataBR($c['data_abertura']) ?></td>
            <td class="text-end">
              <a href="chamado.php?id=<?= (int)$c['id'] ?>" class="btn btn-sm btn-outline-primary">Abrir</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
