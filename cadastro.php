<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';

apenasVisitante();

// Tipos de conta que a pessoa pode escolher ao se cadastrar ("admin" nunca entra aqui)
$tiposDeConta = [
    'cliente'   => ['icone' => 'bi-person',  'titulo' => 'Cliente',   'descricao' => 'Quero abrir chamados'],
    'atendente' => ['icone' => 'bi-headset', 'titulo' => 'Atendente', 'descricao' => 'Vou atender chamados'],
];

$erro          = '';
$nome          = '';
$email         = '';
$tipoEscolhido = 'cliente';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    exigirCsrf();

    $nome  = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $tipoEscolhido = opcaoValida($_POST['tipo'] ?? null, $tiposDeConta);

    if ($nome === '' || $email === '' || $senha === '') {
        $erro = 'Preencha todos os campos.';
    } elseif (mb_strlen($nome) > 100 || mb_strlen($email) > 100) {
        $erro = 'Nome e e-mail devem ter no máximo 100 caracteres.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'Esse e-mail não parece válido.';
    } elseif ($tipoEscolhido === '') {
        $erro = 'Escolha o tipo de conta.';
    } elseif (strlen($senha) < 6) {
        $erro = 'A senha precisa ter pelo menos 6 caracteres.';
    } elseif (strlen($senha) > 72) {
        // Limite do bcrypt: o que passa de 72 bytes seria ignorado
        $erro = 'A senha pode ter no máximo 72 caracteres.';
    } else {
        // Quem cria a primeira conta do sistema vira administrador, seja qual for a escolha;
        // nas demais vale o tipo escolhido no formulário
        $primeiraConta = (int)$pdo->query('SELECT COUNT(*) FROM usuarios')->fetchColumn() === 0;
        $tipo = $primeiraConta ? 'admin' : $tipoEscolhido;

        try {
            $stmt = $pdo->prepare('INSERT INTO usuarios (nome, email, senha, tipo) VALUES (?, ?, ?, ?)');
            $stmt->execute([$nome, $email, password_hash($senha, PASSWORD_BCRYPT), $tipo]);

            $_SESSION['flash_sucesso'] = 'Conta criada! Agora é só entrar.';
            redirecionar('login.php');
        } catch (PDOException $e) {
            // 23000 = violação de chave única: o e-mail já existe
            if ($e->getCode() !== '23000') {
                throw $e;
            }
            $erro = 'Já existe uma conta com esse e-mail.';
        }
    }
}

if ($tipoEscolhido === '') {
    $tipoEscolhido = 'cliente';
}

$tituloPagina = 'Criar conta';
$classeBody   = 'pagina-auth';
require_once __DIR__ . '/includes/header.php';
?>
<div class="row justify-content-center">
  <div class="col-md-8 col-lg-6 col-xl-5">
    <div class="card card-auth shadow-lg">
      <div class="card-body p-4 p-md-5">
        <div class="auth-icone"><i class="bi bi-person-plus"></i></div>
        <h4 class="text-center mb-1">Crie sua conta</h4>
        <p class="text-center text-muted mb-4">Leva menos de um minuto.</p>

        <?php if ($erro): ?>
          <div class="alert alert-danger"><?= h($erro) ?></div>
        <?php endif; ?>

        <form method="post">
          <?= campoCsrf() ?>
          <div class="mb-3">
            <label class="form-label" for="nome">Nome</label>
            <input type="text" id="nome" name="nome" class="form-control form-control-lg"
                   maxlength="100" value="<?= h($nome) ?>" required autofocus>
          </div>
          <div class="mb-3">
            <label class="form-label" for="email">E-mail</label>
            <input type="email" id="email" name="email" class="form-control form-control-lg"
                   maxlength="100" value="<?= h($email) ?>" required>
          </div>
          <div class="mb-3">
            <label class="form-label d-block">Tipo de conta</label>
            <div class="row g-2">
              <?php foreach ($tiposDeConta as $valor => $tipoInfo): ?>
                <div class="col-6">
                  <input type="radio" class="btn-check" name="tipo" id="tipo-<?= $valor ?>"
                         value="<?= $valor ?>" <?= $tipoEscolhido === $valor ? 'checked' : '' ?> required>
                  <label class="btn btn-outline-primary w-100 py-3" for="tipo-<?= $valor ?>">
                    <i class="bi <?= $tipoInfo['icone'] ?> fs-4 d-block"></i>
                    <?= h($tipoInfo['titulo']) ?>
                    <small class="d-block fw-normal"><?= h($tipoInfo['descricao']) ?></small>
                  </label>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
          <div class="mb-4">
            <label class="form-label" for="senha">Senha</label>
            <input type="password" id="senha" name="senha" class="form-control form-control-lg"
                   minlength="6" maxlength="72" required>
            <div class="form-text">Use pelo menos 6 caracteres.</div>
          </div>
          <button type="submit" class="btn btn-primary btn-lg w-100">Criar conta</button>
        </form>

        <p class="text-center mt-4 mb-0">
          Já tem conta? <a href="login.php">Entrar</a>
        </p>
      </div>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>