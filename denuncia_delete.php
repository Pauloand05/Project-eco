<?php
session_start();
include 'conexao.php'; 
include_once "utils/api.php";

function deleteDenunciaEEndereco($conn, $id, $endereco_cep, $anexo) {
    $message = "";
    try {
        
        $sql_verifica_denuncias = "SELECT COUNT(*) as num_denuncias FROM denuncia WHERE endereco_cep = ?";
        $stmt = $conn->prepare($sql_verifica_denuncias);
        $stmt->bind_param("s", $endereco_cep);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $num_denuncias = $row['num_denuncias'];
        $stmt->close();
    
        $resultado_de_delete_arquivo = deleteFile($anexo);

        if ($num_denuncias >= 2) {
            
            $delete_sql = "DELETE FROM denuncia WHERE id = ?";
            $delete_stmt = $conn->prepare($delete_sql);
            $delete_stmt->bind_param("i", $id);
            
            if ($delete_stmt->execute() && $resultado_de_delete_arquivo['success']) {
                $message = $resultado_de_delete_arquivo['message'];
            } else {
                throw new Exception("Erro ao excluir a denúncia ou o arquivo.");
            }
        } else {
           
            $delete_sql = "DELETE FROM denuncia WHERE id = ?";
            $delete_stmt = $conn->prepare($delete_sql);
            $delete_stmt->bind_param("i", $id);
            
            if ($delete_stmt->execute() && $resultado_de_delete_arquivo['success']) {
            
                $stmt_check = $conn->prepare("SELECT COUNT(*) as remaining_denuncias FROM denuncia WHERE endereco_cep = ?");
                $stmt_check->bind_param("s", $endereco_cep);
                $stmt_check->execute();
                $row = $stmt_check->get_result()->fetch_assoc();
                $remaining_denuncias = $row['remaining_denuncias'];
                $stmt_check->close();

                if ($remaining_denuncias === 0) {
                    $delete_cep_sql = "DELETE FROM endereco WHERE cep = ?";
                    $delete_cep_stmt = $conn->prepare($delete_cep_sql);
                    $delete_cep_stmt->bind_param("s", $endereco_cep);
                    
                    if (!$delete_cep_stmt->execute()) {
                        throw new Exception("Erro ao excluir o CEP da tabela 'endereco'.");
                    }

                    $delete_cep_stmt->close();
                }

                $message = $resultado_de_delete_arquivo['message'];
            } else {
                throw new Exception("Erro ao excluir a denúncia ou o arquivo.");
            }
        }

        // Exibir mensagem de sucesso
        echo "<div id='msg-box' style='padding: 10px; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 5px; margin-bottom: 10px;'>
                $message
             </div>";
        echo "<button onclick='history.back()' style='padding: 5px 10px; background-color: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer;'>
                Voltar
            </button>";

    } catch (Exception $e) {
        // Em caso de erro, exibir mensagem
        echo "Erro: " . $e->getMessage();
    }
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); 
    exit();
}

if (isset($_POST['id']) && is_numeric($_POST['id'])) {
    $id = $_POST['id'];

    $sql = "SELECT id, status, usuario_cpf, anexo, endereco_cep FROM `eco`.`denuncia` WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        if ($row['status'] == 'pendente') {
           
            if ($_SESSION['user_id'] == $row['usuario_cpf']) {
               deleteDenunciaEEndereco($conn, $id, $row['endereco_cep'], $row['anexo']);
                
            } else {
                echo "Você não tem permissão para excluir essa denúncia.";
            }
        } else {
            echo "Não é possível excluir a denúncia, pois está em análise ou já foi concluída.";
        }
    } else {
        echo "Denúncia não encontrada.";
    }

    $stmt->close();
} else {
    echo "ID inválido.";
}

$conn->close();
?>