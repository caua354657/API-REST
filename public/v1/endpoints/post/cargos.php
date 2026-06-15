<?php
    require __DIR__ . "/../../../../configuracoes/api_ativar.php";
    require __DIR__ . "/../../../../configuracoes/acoes.php";  
    require __DIR__ . "/../../../../configuracoes/jwt.php";

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
                    $nome = $_POST['nome'] ?? null;

                    if(!empty($nome))
                    {
                        if(cargos::cadastro_cargo($nome) == true) //se essa funcao for verdadeira, ela executa e depois abaixo eu mostro o ultimo registro
                        {
                            $informacoes = cargos::mostrar_cargo();
                            echo json_encode([
                                'status' => 200,
                                'api_versao' => API_VERSION,
                                'mensagem' => 'Cargo cadastrado com sucesso',
                                'data_hora_resposta' => date('d-m-Y H:i:s'),
                                "informacoes" => $informacoes], 
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
                                JSON_PRETTY_PRINT
                            );
                        }
                    }
                    else
                    {
                        http_response_code(400);
                        echo json_encode([
                                "status" => 400,
                                'api_versao' => API_VERSION,
                                'data_hora_resposta' => date('d-m-Y H:i:s'),
                                "mensagem" => "Campo 'nome' requirido"],
                                JSON_PRETTY_PRINT
                            );
                            exit;
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
                        JSON_PRETTY_PRINT
                    );
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