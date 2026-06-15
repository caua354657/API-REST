<?php
    require_once __DIR__ . "/../../../../configuracoes/api_ativar.php";
    require_once __DIR__ . "/../../../../configuracoes/acoes.php";
    require_once __DIR__ . "/../../../../configuracoes/jwt.php";

    header('Content-Type: application/json; charset=utf-8');
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
                    if(isset($_POST['nome']) and isset($_POST['sn']))
                    { 
                        $nome = trim($_POST['nome']);
                        $controla_espacos = trim($_POST['sn']);
                    }
                    else 
                    {
                        $nome = null;
                        $controla_espacos = null;
                    }
                    
                    if(empty($nome) || empty($controla_espacos))
                    {
                        http_response_code(400);
                        echo json_encode([
                            "status" => 400,
                            'api_versao' => API_VERSION,
                            'data_hora_resposta' => date('d-m-Y H:i:s'),
                            "mensagem" => "Campos requiridos"],
                            JSON_PRETTY_PRINT
                        );
                        exit;
                    }

                    if(tipos::cadastro_tipo($nome, $controla_espacos) == true)
                    {
                        $informacoes = tipos::mostrar_tipo();
                        echo json_encode([
                            'status' => 200,
                            'api_versao' => API_VERSION,
                            'mensagem' => 'Cadastro realizado com sucesso',
                            'data_hora_resposta' => date('d-m-Y H:i:s'),
                            'informacoes' => $informacoes],
                            JSON_PRETTY_PRINT
                        );
                    }
                    else
                    {
                        http_response_code(500);
                        echo json_encode([
                            'status' => 500,
                            'api_versao' => API_VERSION,
                            'mensagem' => 'Erro ao Cadastrar',
                            'data_hora_resposta' => date('d-m-Y H:i:s'),
                            'erro_cadastro' => $conexao->error],
                            JSON_PRETTY_PRINT
                        );
                    }
                }
                else
                {
                    http_response_code(405);
                    echo json_encode([
                        'status' => 405,
                        'api_versao' => API_VERSION,
                        'mensagem' => 'Método não é POST',
                        'data_hora_resposta' => date('d-m-Y H:i:s')],
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