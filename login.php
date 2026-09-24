<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';

apenasVisitante();

$erro  = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    exigirCsrf();

    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if ($email === '' || $senha === '') {
        $erro = 'Informe seu e-mail e sua senha.';
    } else {
        $stmt = $pdo->prepare('SELECT id, nome, senha, tipo FROM usuarios WHERE email = ?');
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();

        if ($usuario && password_verify($senha, $usuario['senha'])) {
            // Novo ID de sessão a cada login evita o roubo de sessão (session fixation)
            session_regenerate_id(true);

            $_SESSION['usuario_id']   = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            $_SESSION['usuario_tipo'] = $usuario['tipo'];

            redirecionar('index.php');
        }

        // A mesma mensagem para e-mail inexistente e senha errada, de propósito
        $erro = 'E-mail ou senha incorretos.';
    }
}

$tituloPagina = 'Entrar';
$classeBody   = 'pagina-auth';
require_once __DIR__ . '/includes/header.php';
?>
<div class="row justify-content-center">
  <div class="col-md-8 col-lg-6 col-xl-5">
    <div class="card card-auth shadow-lg">
      <div class="card-body p-4 p-md-5">
        <div class="auth-icone"><i class="bi bi-headset"></i></div>
        <h4 class="text-center mb-1">Bem-vindo de volta</h4>
        <p class="text-center text-muted mb-4">Entre para acompanhar seus chamados.</p>

        <?php if ($erro): ?>
          <div class="alert alert-danger"><?= h($erro) ?></div>
        <?php endif; ?>

        <form method="post">
          <?= campoCsrf() ?>
          <div class="mb-3">
            <label class="form-label" for="email">E-mail</label>
            <input type="email" id="email" name="email" class="form-control form-control-lg"
                   value="<?= h($email) ?>" required autofocus>
          </div>
          <div class="mb-4">
            <label class="form-label" for="senha">Senha</label>
            <input type="password" id="senha" name="senha" class="form-control form-control-lg" required>
          </div>
          <button type="submit" class="btn btn-primary btn-lg w-100">Entrar</button>
        </form>

        <p class="text-center mt-4 mb-0">
          Ainda não tem conta? <a href="cadastro.php">Criar conta</a>
        </p>
      </div>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
