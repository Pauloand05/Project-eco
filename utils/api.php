<?php 
function mudarSenha(string $nome, string $email, string $token){
    $command = getcwd() . "/utils/send_email.exe --operacao 1 --name " . escapeshellarg($nome) . " --email " . escapeshellarg($email) . " --token " . escapeshellarg($token);
    $output = shell_exec($command);

    if ($output === null) {
        return [
            "success" => false, 
            "error" => "O email informado não pode ser enviado"
        ];
    } else {
        return [ 
            "success" => true, 
            "message" => $output
        ];
    }
    return [
        "success" => false, 
        "error" => "Os campos (email, nome, token) devem ser fornecidos."
    ];
} 

function alterarSenha(string $nome, string $email){
    $command = getcwd() . "/utils/send_email.exe --operacao 2 --name " . escapeshellarg($nome) . " --email " . escapeshellarg($email);

    $output = shell_exec($command);

    if ($output === null) {
        return [
            "success" => false, 
            "error" => "O email informado não pode ser enviado"
        ];
    } else {
        return [
            "success" => true, 
            "message" => $output
        ];
    }
    return [
        "success" => false, 
        "error" => "Os campos (email, nome) devem ser fornecidos."
    ];
} 

function consultaCep(string $cep){
    $url = "https://viacep.com.br/ws/{$cep}/json/";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    $response_decode_json = json_decode($response, true);
    if (isset($response_decode_json['erro'])) {
        return ["success" => false, "error" => "O CEP informado não foi encontrado."];
    } else {
        return ["success" => true, "cep" => $response_decode_json];
    }
}

function uploadImagem($tmp_file) {

    $novoDiretorio = "C:/xampp/htdocs/imagem"; 

    if (!is_dir($novoDiretorio)) {
        mkdir($novoDiretorio, 0777, true); 
    }

    
    if ($_FILES['anexo']['error'] !== UPLOAD_ERR_OK) {
        return ["success" => false, "error" => "Erro: Ocorreu um erro durante o upload do arquivo."];
    }

    
    $fileInfo = pathinfo($_FILES['anexo']['name']);
    $extensao = strtolower($fileInfo['extension']);
    
    $nomeArquivoUnico = uniqid() . '.' . $extensao;

    $caminhoDestino = rtrim($novoDiretorio, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $nomeArquivoUnico;

    
    $extensoesPermitidas = ['jpg', 'jpeg', 'png', 'gif'];

    
    if (!in_array($extensao, $extensoesPermitidas)) {
        return ["success" => false, "error" => "Apenas imagens JPEG, PNG e GIF são permitidas."];
    }

    
    if (move_uploaded_file($tmp_file, $caminhoDestino)) {
        return ["success" => true, "file_path" => $caminhoDestino];
    } else {
        return ["success" => false, "error" => "Erro: Não foi possível mover o arquivo para o diretório de destino."];
    }
}

function deleteFile($filePath) {
    
    if (file_exists($filePath)) {
        
        if (unlink($filePath)) {
            return ['success' => true, 'message' => "Arquivo removido da base de dados com sucesso!!"];
        } else {
            return ["success" => false, 'error' => "Não foi possível removido o arquivo da base de dados"];
        }
    } else {
        return ["success" => false, 'error' => "O caminho especificado não há nenhum arquivo"];;
    }
}


?>