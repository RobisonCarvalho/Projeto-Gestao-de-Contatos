<?php

require_once __DIR__ . '/../config/database.php';

// Permitir apenas requisições POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Método de requisição não permitido.');
}

// Validar o ID recebido
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {
    exit('ID de contato inválido.');
}

try {
    // Excluir contato do banco de dados
    $sql = "DELETE FROM contatos WHERE id = :id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':id' => $id
    ]);

    // Voltar para a lista de contatos
    header('Location: listar-contatos.php?excluido=1');
    exit;

} catch (PDOException $e) {
    error_log('Erro ao excluir contato: ' . $e->getMessage());

    exit('Não foi possível excluir o contato.');
}