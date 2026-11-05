
<?php

require_once __DIR__ . '/../config/database.php';

// Receber o ID pela URL
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

// Validar o ID
if (!$id || $id <= 0) {
    exit('ID de contato inválido.');
}

try {

    // Buscar contato específico
    $sql = "SELECT * FROM contatos WHERE id = :id";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':id' => $id
    ]);

    $contato = $stmt->fetch();

    // Verificar se o contato existe
    if (!$contato) {
        exit('Contato não encontrado.');
    }

} catch (PDOException $e) {

    error_log($e->getMessage());
    exit('Não foi possível carregar o contato.');

}

?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Detalhes do Contato</title>

    <link rel="stylesheet" href="../css/style.css">
    <script src="../js/script.js" defer></script>
</head>

<body>

    <header class="topbar">
        <div class="container topbar-content">

            <div class="logo">RC<span>.</span> CONTATOS</div>

            <a href="listar-contatos.php">Ver todos os contatos →</a>

        </div>
    </header>

    <main class="container">

        <div class="page-heading">
            <h1>Detalhes do contato</h1>
            <p>Visualize todas as informações deste cadastro.</p>
        </div>

        <section class="card">

            <h2>
                <?= htmlspecialchars($contato['nome'], ENT_QUOTES, 'UTF-8') ?>
            </h2>

            <div class="contact-details">

                <div class="form-group">
                    <label>ID do contato</label>
                    <p>
                        #<?= htmlspecialchars((string) $contato['id']) ?>
                    </p>
                </div>

                <div class="form-group">
                    <label>Nome completo</label>
                    <p>
                        <?= htmlspecialchars($contato['nome']) ?>
                    </p>
                </div>

                <div class="form-group">
                    <label>E-mail</label>
                    <p>
                        <?= htmlspecialchars($contato['email']) ?>
                    </p>
                </div>

                <div class="form-group">
                    <label>Telefone</label>
                    <p>
                        <?= htmlspecialchars($contato['telefone'] ?? '') ?: 'Não informado' ?>
                    </p>
                </div>

                <div class="form-group">
                    <label>Observações</label>
                    <p>
                        <?= nl2br(htmlspecialchars($contato['mensagem'] ?? '')) ?: 'Nenhuma observação cadastrada.' ?>
                    </p>
                </div>

                <div class="form-group">
                    <label>Data de cadastro</label>
                    <p>
                        <?= htmlspecialchars($contato['criado_em']) ?>
                    </p>
                </div>

            </div>

            <a href="listar-contatos.php" class="btn btn-primary">
                ← Voltar para contatos
            </a>

        </section>

    </main>

    <footer class="footer">
        <div class="container">
            Sistema de Contatos · Robison Carvalho
        </div>
    </footer>

</body>

</html>
