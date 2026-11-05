

<?php

require_once __DIR__ . '/../config/database.php';

// Verificar método da requisição
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('Método de requisição não permitido.');
}

// Receber os dados
$nome = trim($_POST['nome'] ?? '');
$email = trim($_POST['email'] ?? '');
$telefone = trim($_POST['telefone'] ?? '');
$mensagem = trim($_POST['mensagem'] ?? '');

// Validar campos obrigatórios
if ($nome === '' || $email === '') {
    exit('Preencha o nome e o e-mail.');
}

// Validar e-mail
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    exit('E-mail inválido.');
}

try {

    $sql = "INSERT INTO contatos
            (nome, email, telefone, mensagem)
            VALUES
            (:nome, :email, :telefone, :mensagem)";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':nome' => $nome,
        ':email' => $email,
        ':telefone' => $telefone,
        ':mensagem' => $mensagem
    ]);

    echo "Contato cadastrado com sucesso!";

} catch (PDOException $e) {

    error_log($e->getMessage());
    echo "Não foi possível cadastrar o contato.";

}

