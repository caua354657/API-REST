<?php
    require_once __DIR__ . "/../../../../configuracoes/api_ativar.php";
    require_once __DIR__ . "/../../../../configuracoes/acoes.php";

    header('Content-Type: application/json; charset=utf-8');
    header("Cache-Control: " . API_CACHE_CONTROL);

    if(API_IS_ACTIVE)
    {
        if($_SERVER['REQUEST_METHOD'] == 'GET')
        {
            $informacoes = cargos::cargos_cadastrados();
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