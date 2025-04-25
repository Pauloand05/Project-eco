<?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $codigo = $_POST['codigo']; // Identificador único do admin
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $telefone = $_POST['telefone'];
        $empresa_cnpj = $_POST['empresa_cnpj'];

        // Atualizar as informações no banco de dados
        $sql = "UPDATE eco.admin 
                SET nome = ?, email = ?, telefone = ?, empresa_cnpj = ? 
                WHERE codigo = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $nome, $email, $telefone, $empresa_cnpj, $codigo);
        
        if ($stmt->execute()) {
            echo "Admin atualizado com sucesso!";
        } else {
            echo "Erro ao atualizar admin!";
        }
    }
?>