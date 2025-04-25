<?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $codigo = $_POST['codigo']; // Identificador único do admin

        // Excluir admin do banco de dados
        $sql = "DELETE FROM eco.admin WHERE codigo = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $codigo);
        
        if ($stmt->execute()) {
            echo "Admin excluído com sucesso!";
        } else {
            echo "Erro ao excluir admin!";
        }
    }
?>
