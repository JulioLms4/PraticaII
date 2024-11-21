<?php
// Conexão banco de dados
$conn = new mysqli("localhost", "root", "root", "praticaII");

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Funções auxiliares
function limparEntrada($dados) {
    return htmlspecialchars(trim($dados));
}

// Cadastro de cliente
if (isset($_POST['cadastrar_cliente'])) {
    $nome = limparEntrada($_POST['nome']);
    $cpf = limparEntrada($_POST['cpf']);
    $email = limparEntrada($_POST['email']);
    $telefone = limparEntrada($_POST['telefone']);

    // Validação de CPF simples
    if (strlen($cpf) != 11 || !ctype_digit($cpf)) {
        echo "CPF inválido!";
    } else {
        $stmt = $conn->prepare("INSERT INTO clientes (nome_completo, cpf, email, telefone) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nome, $cpf, $email, $telefone);
        if ($stmt->execute()) {
            echo "Cliente cadastrado com sucesso!";
        } else {
            echo "Erro ao cadastrar cliente: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Cadastro de solicitação
if (isset($_POST['cadastrar_solicitacao'])) {
    $cliente_id = limparEntrada($_POST['cliente_id']);
    $descricao = limparEntrada($_POST['descricao']);
    $urgencia = limparEntrada($_POST['urgencia']);

    $stmt = $conn->prepare("INSERT INTO solicitacoes (cliente_id, descricao, urgencia) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $cliente_id, $descricao, $urgencia);
    if ($stmt->execute()) {
        echo "Solicitação cadastrada com sucesso!";
    } else {
        echo "Erro ao cadastrar solicitação: " . $stmt->error;
    }
    $stmt->close();
}

// Atualização de status
if (isset($_POST['atualizar_status'])) {
    $solicitacao_id = limparEntrada($_POST['solicitacao_id']);
    $novo_status = limparEntrada($_POST['status']);

    $stmt = $conn->prepare("UPDATE solicitacoes SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $novo_status, $solicitacao_id);
    if ($stmt->execute()) {
        echo "Status atualizado com sucesso!";
    } else {
        echo "Erro ao atualizar status: " . $stmt->error;
    }
    $stmt->close();
}

// Listagem de clientes e solicitações
$clientes = $conn->query("SELECT id, nome_completo FROM clientes");
$solicitacoes = $conn->query("SELECT solicitacoes.id, clientes.nome_completo, solicitacoes.descricao, solicitacoes.urgencia, solicitacoes.status 
                              FROM solicitacoes 
                              JOIN clientes ON solicitacoes.cliente_id = clientes.id");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento de Solicitações</title>
</head>
<body>
    <h1>Gerenciamento de Solicitações</h1>

    <!-- Cadastro de Cliente -->
    <h2>Cadastrar Cliente</h2>
    <form method="POST">
        <input type="text" name="nome" placeholder="Nome Completo" required>
        <input type="text" name="cpf" placeholder="CPF" required>
        <input type="email" name="email" placeholder="E-mail" required>
        <input type="text" name="telefone" placeholder="Telefone">
        <button type="submit" name="cadastrar_cliente">Cadastrar</button>
    </form>

    <!-- Cadastro de Solicitação -->
    <h2>Cadastrar Solicitação</h2>
    <form method="POST">
        <select name="cliente_id" required>
            <option value="">Selecione um Cliente</option>
            <?php while ($cliente = $clientes->fetch_assoc()): ?>
                <option value="<?= $cliente['id'] ?>"><?= $cliente['nome_completo'] ?></option>
            <?php endwhile; ?>
        </select>
        <textarea name="descricao" placeholder="Descrição do Serviço" required></textarea>
        <select name="urgencia" required>
            <option value="baixa">Baixa</option>
            <option value="media">Média</option>
            <option value="alta">Alta</option>
        </select>
        <button type="submit" name="cadastrar_solicitacao">Cadastrar</button>
    </form>

    <!-- Listagem de Solicitações -->
    <h2>Solicitações</h2>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Descrição</th>
                <th>Urgência</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($solicitacao = $solicitacoes->fetch_assoc()): ?>
                <tr>
                    <td><?= $solicitacao['id'] ?></td>
                    <td><?= $solicitacao['nome_completo'] ?></td>
                    <td><?= $solicitacao['descricao'] ?></td>
                    <td><?= $solicitacao['urgencia'] ?></td>
                    <td><?= $solicitacao['status'] ?></td>
                    <td>
                        <form method="POST" style="display:inline-block;">
                            <input type="hidden" name="solicitacao_id" value="<?= $solicitacao['id'] ?>">
                            <select name="status">
                                <option value="pendente" <?= $solicitacao['status'] === 'pendente' ? 'selected' : '' ?>>Pendente</option>
                                <option value="em andamento" <?= $solicitacao['status'] === 'em andamento' ? 'selected' : '' ?>>Em Andamento</option>
                                <option value="finalizada" <?= $solicitacao['status'] === 'finalizada' ? 'selected' : '' ?>>Finalizada</option>
                            </select>
                            <button type="submit" name="atualizar_status">Atualizar</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
