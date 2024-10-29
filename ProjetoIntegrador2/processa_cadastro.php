<?php
// Conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "projetointegrador2";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Função para inserir cliente
function inserirCliente($conn, $nome, $sobrenome, $cpf, $cidade, $bairro, $rua, $numero, $celular, $servico) {
    try {
        $stmt = $conn->prepare("INSERT INTO cliente (nome, sobrenome, cpf, cidade, bairro, rua, numero, celular, servico) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssss", $nome, $sobrenome, $cpf, $cidade, $bairro, $rua, $numero, $celular, $servico);
        $stmt->execute();
        $stmt->close();
        return true;
    } catch (Exception $e) {
        error_log("Erro ao inserir cliente: " . $e->getMessage());
        return false;
    }
}

// Recebe os dados do formulário e sanitiza
$nome = isset($_POST['nome']) ? filter_var($_POST['nome'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) : '';
$sobrenome = isset($_POST['sobrenome']) ? filter_var($_POST['sobrenome'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) : '';
$cpf = isset($_POST['cpf']) ? filter_var($_POST['cpf'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) : '';
$cidade = isset($_POST['cidade']) ? filter_var($_POST['cidade'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) : '';
$bairro = isset($_POST['bairro']) ? filter_var($_POST['bairro'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) : '';
$rua = isset($_POST['rua']) ? filter_var($_POST['rua'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) : '';
$numero = isset($_POST['numero']) ? filter_var($_POST['numero'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) : '';
$celular = isset($_POST['celular']) ? filter_var($_POST['celular'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) : '';
$servico = isset($_POST['servico']) ? filter_var($_POST['servico'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) : '';

// Verificar se o CPF já existe
$stmt = $conn->prepare("SELECT COUNT(*) FROM cliente WHERE cpf = ?");
$stmt->bind_param("s", $cpf);
$stmt->execute();
$stmt->bind_result($count);
$stmt->fetch();
$stmt->close();

// Validação básica
if (empty($nome) || empty($sobrenome) || empty($cpf) || empty($cidade) || empty($bairro) || empty($rua) || empty($numero) || empty($celular) || empty($servico)) {
    echo "Todos os campos são obrigatórios.";
    exit;
}

if ($count > 0) {
    echo "CPF já cadastrado.";
} else {
    if (inserirCliente($conn, $nome, $sobrenome, $cpf, $cidade, $bairro, $rua, $numero, $celular, $servico)) {
        // Redireciona após a inserção bem-sucedida
        header("Location: sucesso.php");
        exit; // Adicione exit após header para evitar execução adicional
    } else {
        echo "<p>Ocorreu um erro ao cadastrar o cliente. Por favor, tente novamente mais tarde.</p>";
    }
}

$conn->close();
?>
