
<?php

require_once __DIR__ . '/../config/database.php';

// Identificar o contato pela URL
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {
    exit('ID de contato inválido.');
}

$erro = '';
$sucesso = isset($_GET['atualizado']);

try {

    // Se o formulário foi enviado, atualizar o contato
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $nome = trim($_POST['nome'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $telefone = trim($_POST['telefone'] ?? '');
        $mensagem = trim($_POST['mensagem'] ?? '');

        if ($nome === '' || $email === '') {
            $erro = 'Nome e e-mail são obrigatórios.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $erro = 'Informe um e-mail válido.';
        } else {

            $sql = "UPDATE contatos
                    SET nome = :nome,
                        email = :email,
                        telefone = :telefone,
                        mensagem = :mensagem
                    WHERE id = :id";

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                ':nome' => $nome,
                ':email' => $email,
                ':telefone' => $telefone,
                ':mensagem' => $mensagem,
                ':id' => $id
            ]);

            header("Location: editar-contato.php?id=$id&atualizado=1");
            exit;
        }
    }

    // Buscar os dados atuais do contato
    $sql = "SELECT * FROM contatos WHERE id = :id";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':id' => $id
    ]);

    $contato = $stmt->fetch();

    if (!$contato) {
        exit('Contato não encontrado.');
    }

    // Preservar os valores se houver erro de validação
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $erro !== '') {
        $contato['nome'] = $nome;
        $contato['email'] = $email;
        $contato['telefone'] = $telefone;
        $contato['mensagem'] = $mensagem;
    }

} catch (PDOException $e) {

    error_log($e->getMessage());
    exit('Não foi possível processar a edição do contato.');

}

?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Editar Contato</title>

    <link rel="stylesheet" href="../css/style.css">
    <script src="../js/script.js" defer></script>
</head>

<body>

    <header class="topbar">
        <div class="container topbar-content">
            <div class="logo">RC<span>.</span> CONTATOS</div>
            <a href="listar-contatos.php">Voltar para contatos →</a>
        </div>
    </header>

    <main class="container">

        <div class="page-heading">
            <h1>Editar contato</h1>
            <p>Atualize as informações deste cadastro.</p>
        </div>

        <section class="card">

            <h2>Informações do contato</h2>

            <?php if ($sucesso): ?>
                <p class="success-message">
                    Contato atualizado com sucesso!
                </p>
            <?php endif; ?>

            <?php if ($erro !== ''): ?>
                <p class="error-message">
                    <?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?>
                </p>
            <?php endif; ?>

            <form method="POST" action="editar-contato.php?id=<?= $id ?>">

                <div class="form-group">
                    <label for="nome">Nome completo</label>
                    <input
                        type="text"
                        id="nome"
                        name="nome"
                        value="<?= htmlspecialchars($contato['nome'], ENT_QUOTES, 'UTF-8') ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="email">E-mail</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="<?= htmlspecialchars($contato['email'], ENT_QUOTES, 'UTF-8') ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="telefone">Telefone</label>
                    <input
                        type="text"
                        id="telefone"
                        name="telefone"
                        value="<?= htmlspecialchars($contato['telefone'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    >
                </div>

                <div class="form-group">
                    <label for="mensagem">Observações</label>
                    <textarea
                        id="mensagem"
                        name="mensagem"
                    ><?= htmlspecialchars($contato['mensagem'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary">
                    Salvar alterações
                </button>

                <a href="listar-contatos.php" class="btn btn-light">
                    Cancelar
                </a>

            </form>

        </section>

    </main>

    <footer class="footer">
        <div class="container">
            Sistema de Contatos · Robison Carvalho
        </div>
    </footer>

</body>

</html>
