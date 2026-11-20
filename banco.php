<?php
// Abra este arquivo uma vez pelo navegador para criar as tabelas e o primeiro acesso.
$host = "localhost";
$usuario = "root";
$senha = "";
try {
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $usuario, $senha);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS brecho_reyana CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE brecho_reyana");

    $pdo->exec("CREATE TABLE IF NOT EXISTS usuarios (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(150) NOT NULL UNIQUE,
        senha VARCHAR(255) NOT NULL
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS produtos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(120) NOT NULL,
        categoria VARCHAR(60) NOT NULL,
        tamanho VARCHAR(20) NOT NULL,
        estado VARCHAR(60) NOT NULL,
        descricao TEXT,
        preco DECIMAL(10,2) NOT NULL,
        estoque INT NOT NULL DEFAULT 1,
        imagem VARCHAR(500),
        criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS vendas (
        id INT AUTO_INCREMENT PRIMARY KEY,
        produto_id INT NOT NULL,
        quantidade INT NOT NULL,
        valor_total DECIMAL(10,2) NOT NULL,
        forma_pagamento VARCHAR(60),
        data_venda TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (produto_id) REFERENCES produtos(id)
    )");

    $verifica = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
    $verifica->execute(["admin@brecho.com"]);
    if (!$verifica->fetch()) {
        $hash = password_hash("123456", PASSWORD_DEFAULT);
        $insere = $pdo->prepare("INSERT INTO usuarios (email, senha) VALUES (?, ?)");
        $insere->execute(["admin@brecho.com", $hash]);
    }

    $total = $pdo->query("SELECT COUNT(*) FROM produtos")->fetchColumn();
    if ($total == 0) {
        $exemplos = [
            ["Camisa vintage","Camisas","M","Ótimo","Peça de segunda mão em ótimo estado.",49.90,1,"https://images.unsplash.com/photo-1598033129183-c4f50c736f10?auto=format&fit=crop&w=900&q=80"],
            ["Jaqueta clássica","Jaquetas","G","Muito bom","Uma peça especial para compor seu visual.",89.90,1,"https://images.unsplash.com/photo-1544923246-77307dd654cb?auto=format&fit=crop&w=900&q=80"],
            ["Vestido delicado","Vestidos","P","Ótimo","Moda circular com charme e personalidade.",69.90,1,"https://images.unsplash.com/photo-1595777457583-95e059d581b8?auto=format&fit=crop&w=900&q=80"],
            ["Bolsa retrô","Acessórios","Único","Bom","Acessório de segunda mão cheio de estilo.",39.90,1,"https://images.unsplash.com/photo-1584917865442-de89df76afd3?auto=format&fit=crop&w=900&q=80"]
        ];
        $sql = "INSERT INTO produtos (nome,categoria,tamanho,estado,descricao,preco,estoque,imagem) VALUES (?,?,?,?,?,?,?,?)";
        $stmt = $pdo->prepare($sql);
        foreach ($exemplos as $item) $stmt->execute($item);
    }
    echo "<h1>Instalação concluída!</h1>";
    echo "<p>Login: <b>admin@brecho.com</b></p><p>Senha: <b>123456</b></p>";
    echo "<p><a href='login.php'>Ir para o login</a></p>";
    echo "<p>Por segurança, apague o arquivo instalar.php depois de instalar.</p>";
} catch (PDOException $erro) {
    echo "Não foi possível instalar. Verifique se o MySQL está ligado no XAMPP.";
}
