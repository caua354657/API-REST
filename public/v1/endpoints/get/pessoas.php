<?php
    require_once __DIR__ . "/../../../../configuracoes/api_ativar.php";
    require_once __DIR__ . "/../../../../configuracoes/acoes.php";
    require_once __DIR__ . "/../../../../configuracoes/jwt.php";

    header('Content-Type: application/json; charset=utf-8');
    header("Cache-Control: " . API_CACHE_CONTROL);

    if(API_IS_ACTIVE)
    {
        if($_SERVER['REQUEST_METHOD'] == 'GET')
        {
            $autenticacao = json_web_token::validar_token();
            $payload = $autenticacao['payload'] ?? null;
            $id_empresa = $payload->id_empresa ?? null; //id_empresa do usuario logado
            $categoria = $payload->categoria ?? null;

            if($autenticacao['status'] !== 200) 
            {
                http_response_code($autenticacao['status']);
                echo json_encode([
                    'status' => $autenticacao['status'],
                    'api_versao' => API_VERSION,
                    'mensagem' => $autenticacao['mensagem'],
                    'data_hora_resposta' => date('d-m-Y H:i:s')], 
                    JSON_PRETTY_PRINT);
                exit;
            }

            if($categoria == 'administrador')
            {
                    $informacoes = pessoas::pessoas_todas();
                    echo json_encode([
                                'status' => 200,
                                'api_versao' => API_VERSION,
                                'mensagem' => 'API Ligada', 
                                'data_hora_resposta' => date('d-m-Y H:i:s'),
                                'informacoes' => $informacoes],
                                JSON_PRETTY_PRINT); 
            }
            else
            {
                $sql = "select * from pessoas where empresa_id = '$id_empresa'";
                $dados = $conexao->query($sql);

                if($dados->num_rows > 0)
                {
                    $informacoes = pessoas::pessoas_usuario($id_empresa);
                    echo json_encode([
                                'status' => 200,
                                'api_versao' => API_VERSION,
                                'mensagem' => 'API Ligada', 
                                'data_hora_resposta' => date('d-m-Y H:i:s'),
                                'informacoes' => $informacoes],
                                JSON_PRETTY_PRINT);
                }   
                else
                {
                    echo json_encode([
                                'status' => 200, 
                                'api_versao' => API_VERSION,
                                'mensagem' => 'API Ligada',
                                'resultado' => 'Nenhuma pessoa pertencente na mesma empresa', 
                                'data_hora_resposta' => date('d-m-Y H:i:s')],
                                JSON_PRETTY_PRINT);
                }
            }
        }
        else
        {
            http_response_code(405);
            echo json_encode([
                            "status" => 405,
                            'api_versao' => API_VERSION,
                            'data_hora_resposta' => date('d-m-Y H:i:s'),
                            "mensagem" => "Método não é GET"],
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