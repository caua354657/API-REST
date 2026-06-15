<?php
    require_once __DIR__ . "/../../../../configuracoes/api_ativar.php";
    require_once __DIR__ . "/../../../../configuracoes/acoes.php";
    require_once __DIR__ . "/../../../../configuracoes/jwt.php";

    header("Content-Type: application/json; charset=utf-8");
    header("Cache-Control: " . API_CACHE_CONTROL);

    if(API_IS_ACTIVE)
    {
        $autenticacao = json_web_token::validar_token();
        $payload = $autenticacao['payload'] ?? null;

        if($payload == true)
        {
            if($payload->categoria == 'administrador')
            {
                if($_SERVER["REQUEST_METHOD"] == "POST") 
                {
                    $nome = trim($_POST['nome'] ?? null);
                    $email = trim(strip_tags($_POST['email'] ?? null));
                    $senha = ($_POST['senha'] ?? null) ? password_hash($_POST['senha'], PASSWORD_DEFAULT) : null;
                    $categoria = trim($_POST['categoria'] ?? null);
                    $id_empresa = trim($_POST['empresa'] ?? null);

                    $sql = "select * from usuarios where usuario = '$email'";
                    $dados = $conexao->query($sql);
                    $linha = $dados->fetch_assoc();

                    if($dados->num_rows > 0)
                    {
                        http_response_code(409);
                        echo json_encode([
                                "status" => 409,
                                'api_versao' => API_VERSION,
                                'data_hora_resposta' => date('d-m-Y H:i:s'),
                                "mensagem" => "Email Existente. Tente outro."],
                                JSON_PRETTY_PRINT);
                        exit;
                    }

                    $array = [$nome, $email, $senha, $categoria, $id_empresa];

                    foreach($array as $valor)
                    {
                        if(empty($valor))
                        {
                            http_response_code(400);
                            echo json_encode([
                                "status" => 400,
                                'api_versao' => API_VERSION,
                                'data_hora_resposta' => date('d-m-Y H:i:s'),
                                "mensagem" => "Todos os campos são requeridos"], 
                                JSON_PRETTY_PRINT);
                            exit;
                        }
                    }

                    if(usuarios::cadastro_usuarios($nome, $email, $senha, $categoria, $id_empresa)) //classe usuario e a funcao nela
                    {
                        $informacoes = usuarios::mostrar_usuarios();
                        echo json_encode([
                            'status' => 200,
                            'api_versao' => API_VERSION,
                            'mensagem' => 'Usuário cadastrado com sucesso',
                            'data_hora_resposta' => date('d-m-Y H:i:s'),
                            'informacoes' => $informacoes], 
                            JSON_PRETTY_PRINT);
                    }
                    else 
                    {
                        http_response_code(500);
                        echo json_encode([
                            "status" => 500,
                            'api_versao' => API_VERSION,
                            'data_hora_resposta' => date('d-m-Y H:i:s'),
                            "situacao_cadastro" => "Erro ao cadastrar",
                            "erro" => $conexao->error], 
                            JSON_PRETTY_PRINT);
                    }
                }
                else
                {
                    http_response_code(405);
                    echo json_encode([
                        "status" => 405,
                        'api_versao' => API_VERSION,
                        'data_hora_resposta' => date('d-m-Y H:i:s'),
                        "mensagem" => "Método não é POST"], 
                        JSON_PRETTY_PRINT);
                }
            }
            else
            {
                http_response_code(403);
                echo json_encode([
                    'status' => 403,
                    'api_versao' => API_VERSION,
                    'data_hora_resposta' => date('d-m-Y H:i:s'),
                    'mensagem' => 'Acesso negado: usuário não é administrador'],
                    JSON_PRETTY_PRINT
                );
            }
        }
        else
        {
            http_response_code(401);
            echo json_encode([
                    'status' => 401,
                    'api_versao' => API_VERSION,
                    'data_hora_resposta' => date('d-m-Y H:i:s'),
                    'mensagem' => 'Usuário não autenticado. Faça login para continuar'],
                    JSON_PRETTY_PRINT
                );
        }
    }
    else
    {
        http_response_code(400);
        echo json_encode([
            'status' => 400,
            'api_versao' => API_VERSION,
            'mensagem' => 'API Desligada',
            'data_hora_resposta' => date('d-m-Y H:i:s')], 
            JSON_PRETTY_PRINT);
    }


    $conexao->close();