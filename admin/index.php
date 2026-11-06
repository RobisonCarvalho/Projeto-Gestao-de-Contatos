<?php

session_start();

// Verificar se o usuário está autenticado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../php/login.php');
    exit;
}

require_once __DIR__ . '/../config/database.php';

$sql = "SELECT COUNT(*) FROM contatos";
$stmt = $pdo->query($sql);

$totalContatos = $stmt->fetchColumn();

$sql = "SELECT id, nome, email, criado_em
        FROM contatos
        ORDER BY criado_em DESC
        LIMIT 5";

$stmt = $pdo->query($sql);
$contatosRecentes = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>


<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Painel Administrativo</title>

    <link rel="stylesheet" href="../css/style.css">
</head>

<body>

    <main class="admin-container">

        <h1>Painel Administrativo</h1>

        <p>
            Bem-vindo,
            <?= htmlspecialchars($_SESSION['usuario_nome'], ENT_QUOTES, 'UTF-8') ?>!
        </p>

        <p>Você está autenticado no sistema.</p>

        <div class="dashboard-card">
            <h2>Total de contatos</h2>

            <p class="dashboard-number">
                <?= (int) $totalContatos ?>
            </p>

            <a href="../php/listar-contatos.php" class="action-link">
                Gerenciar contatos →
            </a>
        </div>

        <section class="recent-contacts">

    <h2>Contatos recentes</h2>

    <?php if (count($contatosRecentes) > 0): ?>

        <div class="table-container">

            <table>

                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Data de cadastro</th>
                        <th>Ação</th>
                    </tr>
                </thead>

                <tbody>

                    <?php foreach ($contatosRecentes as $contato): ?>

                        <tr>
                            <td>
                                <?= htmlspecialchars($contato['nome'], ENT_QUOTES, 'UTF-8') ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($contato['email'], ENT_QUOTES, 'UTF-8') ?>
                            </td>

                            <td>
                                <?= date('d/m/Y H:i', strtotime($contato['criado_em'])) ?>
                            </td>

                            <td>
                                <a
                                    href="../php/visualizar-contato.php?id=<?= (int) $contato['id'] ?>"
                                    class="action-link">
                                    Visualizar →
                                </a>
                            </td>
                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    <?php else: ?>

        <p>Nenhum contato cadastrado ainda.</p>

    <?php endif; ?>

</section>

        <a href="../php/logout.php" class="action-link">
            Sair do painel →
        </a>

    </main>

</body>
</html>