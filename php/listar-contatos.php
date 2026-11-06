
<?php

require_once __DIR__ . '/../config/database.php';

$pesquisa = trim($_GET['pesquisa'] ?? '');

try {

    if ($pesquisa !== '') {

        $sql = "SELECT * FROM contatos
                WHERE nome LIKE :nome
                OR email LIKE :email
                ORDER BY criado_em DESC";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ':nome' => "%$pesquisa%",
            ':email' => "%$pesquisa%"
        ]);

    } else {

        $sql = "SELECT * FROM contatos
                ORDER BY criado_em DESC";

        $stmt = $pdo->query($sql);

    }

    $contatos = $stmt->fetchAll();

} catch (PDOException $e) {

    error_log($e->getMessage());
    die("Não foi possível carregar os contatos.");

}

?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Lista de Contatos</title>

    <link rel="stylesheet" href="../css/style.css">
    <script src="../js/script.js" defer></script>
</head>

<body>

    <header class="topbar">
        <div class="container topbar-content">
            <div class="logo">RC<span>.</span> CONTATOS</div>

            <a href="../index.html">+ Novo contato</a>
        </div>
    </header>

    <main class="container">

        <div class="page-heading">
            <h1>Contatos cadastrados</h1>
            <p>Consulte e encontre os contatos registrados no sistema.</p>
        </div>

        <section class="card">

            <h2>Lista de contatos</h2>

            <form action="" method="GET" class="search-form">

                <input
                    type="text"
                    name="pesquisa"
                    class="search-input"
                    placeholder="Pesquisar por nome ou e-mail..."
                    value="<?= htmlspecialchars($pesquisa, ENT_QUOTES, 'UTF-8') ?>"
                >

                <button type="submit" class="btn btn-primary">
                    Pesquisar
                </button>

                <a href="listar-contatos.php" class="btn btn-light">
                    Limpar
                </a>

            </form>

            <?php if (count($contatos) > 0): ?>

                <div class="table-wrapper">

                    <table>

                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>E-mail</th>
                                <th>Telefone</th>
                                <th>Mensagem</th>
                                <th>Data</th>
                                <th>Ações</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php foreach ($contatos as $contato): ?>

                                <tr>

                                    <td>
                                        <?= htmlspecialchars((string) $contato['id']) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($contato['nome']) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($contato['email']) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($contato['telefone'] ?? '') ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($contato['mensagem'] ?? '') ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($contato['criado_em']) ?>
                                    </td>

                                    <td>
                                        <a
                                            class="action-link"
                                            href="visualizar-contato.php?id=<?= urlencode((string) $contato['id']) ?>"
                                        >
                                            Visualizar →
                                        </a>
                                        <a
                                            class="action-link"
                                            href="editar-contato.php?id=<?= urlencode((string) $contato['id']) ?>"
                                        >
                                            Editar →
                                        </a>
                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            <?php else: ?>

                <div class="empty-state">
                    <p>Nenhum contato encontrado.</p>
                    <a href="listar-contatos.php" class="btn btn-light">
                        Ver todos os contatos
                    </a>
                </div>

            <?php endif; ?>

        </section>

    </main>

    <footer class="footer">
        <div class="container">
            Sistema de Contatos · Robison Carvalho
        </div>
    </footer>

</body>
</html>
