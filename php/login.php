
<?php

session_start();

require_once __DIR__ . '/../config/database.php';

// Se já estiver autenticado, encaminhar para o painel
if (isset($_SESSION['usuario_id'])) {
    header('Location: ../admin/index.php');
    exit;
}

$erro = '';
$email = '';

// Configurações de proteção
$limiteTentativas = 5;
$tempoBloqueio = 15;
$tempoJanela = 15;

// Identificar o IP
$ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'desconhecido';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if ($email === '' || $senha === '') {

        $erro = 'Preencha todos os campos.';

    } else {

        try {

            // Consultar tentativas anteriores
            $sql = "SELECT tentativas, primeira_tentativa, bloqueado_ate
                    FROM tentativas_login
                    WHERE email = :email
                    AND ip_address = :ip";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':email' => $email,
                ':ip' => $ipAddress
            ]);

            $tentativa = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verificar bloqueio ativo
            if (
                $tentativa &&
                $tentativa['bloqueado_ate'] !== null &&
                strtotime($tentativa['bloqueado_ate']) > time()
            ) {

                $erro = 'Muitas tentativas incorretas. Tente novamente mais tarde.';

            } else {

                // Buscar usuário
                $sql = "SELECT id, nome, email, senha
                        FROM usuarios
                        WHERE email = :email
                        LIMIT 1";

                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':email' => $email
                ]);

                $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($usuario && password_verify($senha, $usuario['senha'])) {

                    // Limpar tentativas após login bem-sucedido
                    $sql = "DELETE FROM tentativas_login
                            WHERE email = :email
                            AND ip_address = :ip";

                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([
                        ':email' => $email,
                        ':ip' => $ipAddress
                    ]);

                    session_regenerate_id(true);

                    $_SESSION['usuario_id'] = $usuario['id'];
                    $_SESSION['usuario_nome'] = $usuario['nome'];
                    $_SESSION['usuario_email'] = $usuario['email'];

                    header('Location: ../admin/index.php');
                    exit;

                } else {

                    $erro = 'E-mail ou senha inválidos.';

                    // A contagem das tentativas será implementada
                    // no próximo passo.
                }
            }

        } catch (PDOException $e) {

            error_log('Erro no login: ' . $e->getMessage());
            $erro = 'Não foi possível realizar o login.';
        }
    }
}
?>


<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login Administrativo</title>

    <link rel="stylesheet" href="../css/style.css">
</head>

<body>

    <main class="login-container">

        <div class="login-card">

            <h1>Acesso administrativo</h1>

            <p>Entre com suas credenciais para continuar.</p>

            <?php if ($erro !== ''): ?>
                <div class="error-message">
                    <?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST">

                <div class="form-group">
                    <label for="email">E-mail</label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        required
                        value="<?= htmlspecialchars($email ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    >
                </div>

                <div class="form-group">
                    <label for="senha">Senha</label>

                    <input
                        type="password"
                        id="senha"
                        name="senha"
                        required
                    >
                </div>

                <button type="submit" class="submit-button">
                    Entrar
                </button>

            </form>

        </div>

    </main>

</body>
</html>