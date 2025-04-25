<?php
session_start();
include 'conexao.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); 
    exit();
}

function selectDenuncia($conn, $id_usuario) {
    $sql_verifica_denuncia = "SELECT id, status, endereco_cep FROM denuncia WHERE usuario_cpf = ?";
    $stmt = $conn->prepare($sql_verifica_denuncia);
    $stmt->bind_param("s", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        
        while ($row = $result->fetch_assoc()) {

            if ($row['status'] == "em analise") {
                return false; 
            }
        }
        return deleteDenuncia($conn, $id_usuario);
    }
    return true; 
}

function deleteDenuncia($conn, $user_id) {
   
    $conn->begin_transaction();
    
    try {
    
        $sqlEnderecos = "SELECT DISTINCT endereco_cep, anexo FROM denuncia WHERE usuario_cpf = ?";
        $stmtEnderecos = $conn->prepare($sqlEnderecos);
        $stmtEnderecos->bind_param("s", $user_id);
        $stmtEnderecos->execute();
        $resultEnderecos = $stmtEnderecos->get_result();

        $sql = "DELETE FROM denuncia WHERE usuario_cpf = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Erro ao preparar a consulta para deletar as denúncias: " . $conn->error);
        }
        $stmt->bind_param("s", $user_id);
        $stmt->execute();

    
        while ($row = $resultEnderecos->fetch_assoc()) {
            $endereco_cep = $row['endereco_cep'];
            $anexo = $row['anexo'];

            
            if (!empty($anexo) && file_exists($anexo)) {
                unlink($anexo); 
            }

            
            $sqlCheck = "SELECT COUNT(*) AS total FROM denuncia WHERE endereco_cep = ?";
            $stmtCheck = $conn->prepare($sqlCheck);
            $stmtCheck->bind_param("s", $endereco_cep);
            $stmtCheck->execute();
            $resultCheck = $stmtCheck->get_result();
            $rowCheck = $resultCheck->fetch_assoc();

            
            if ($rowCheck['total'] == 0) {
                $sqlDeleteEndereco = "DELETE FROM endereco WHERE cep = ?";
                $stmtDeleteEndereco = $conn->prepare($sqlDeleteEndereco);
                $stmtDeleteEndereco->bind_param("s", $endereco_cep);
                $stmtDeleteEndereco->execute();
            }
        }

        
        $conn->commit();
        return true;

    } catch (Exception $e) {
        
        $conn->rollback();
        echo "Erro ao deletar a denúncia, o anexo e o endereço: " . $e->getMessage();
        return false;
    }
}

function deletarAvaliacoes($conn, $user_id) {
    $sql = "DELETE FROM avaliacoes WHERE usuario_cpf = ?";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        echo "<p>Erro na preparação da consulta: " . $conn->error . "</p>";
        return false;
    }

    $stmt->bind_param("s", $user_id);
    
    if ($stmt->execute()) {
        return true;
    }
    return false;
}

function deletarUsuario($conn, $user_id) {
    $sql = "DELETE FROM usuario WHERE cpf = ?";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        echo "<p>Erro na preparação da consulta: " . $conn->error . "</p>";
        return false;
    }

    $stmt->bind_param("s", $user_id);
    
    if ($stmt->execute()) {
        return true;
    }
    return false;
}

$user_id = $_SESSION['user_id'];
$resultado_select_denuncia = selectDenuncia($conn, $user_id);

$message = "Sua conta não pode ser deletada, porque há denúncias com status iguais a: 'em análise'";

if (!$resultado_select_denuncia) {
    echo "<script>
            alert('$message');
            window.location.href = 'perfil.php';
          </script>";
} else {
    deletarAvaliacoes($conn, $user_id);
    $deletou_usuario = deletarUsuario($conn, $user_id);

    if ($deletou_usuario) {
        $message = "Conta deletada com sucesso!";
        echo "<script>
                alert('$message');
                window.location.href = 'index.php';
              </script>"; 
    } else {
        echo "<script>
                alert('Erro ao deletar a conta.');
                window.location.href = 'perfil.php';
              </script>"; 
    }
}

$conn->close();
?>
