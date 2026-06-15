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
                if($_SERVER['REQUEST_METHOD'] == "POST")
                {
                    $fantasia = trim($_POST['fantasia'] ?? null);
                    $razao = trim($_POST['razao'] ?? null);
                    $cnpj = trim($_POST['cnpj'] ?? null);
                    $tipo_cadastro = trim($_POST['tipo'] ?? null);
                
                    if(!is_numeric($tipo_cadastro)) //se não for número o tipo_cadastro
                    {
                        $sql = "select id from tipo_cadastro where nome = '$tipo_cadastro'"; //busca o id desse tipo
                        $dados = $conexao->query($sql);

                        if($dados->num_rows == 0) 
                        {
                            http_response_code(400);
                            echo json_encode([
                                "status" => 400,
                                'api_versao' => API_VERSION,
                                "mensagem" => "Tipo de cadastro inválido"
                            ]);
                            exit;
                        }

                        $tipo_cadastro = intval($dados->fetch_assoc()['id']);
                    }
                    else
                    {
                        $tipo_cadastro = intval($tipo_cadastro);
                    }

                    //verificar cnpj já existente            
                    $select = "select * from empresas where cnpj = '$cnpj'";
                    $dados = $conexao->query($select);
                    $linha = $dados->fetch_assoc();

                    if($dados->num_rows > 0)
                    {
                        http_response_code(409);
                        echo json_encode([
                                "status" => 409,
                                'api_versao' => API_VERSION,
                                'data_hora_resposta' => date('d-m-Y H:i:s'),
                                "mensagem" => "CNPJ Existente. Tente outro"],
                                JSON_PRETTY_PRINT);
                        exit;
                    }

                    $array = [$fantasia, $razao, $cnpj, $tipo_cadastro];

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
                                JSON_PRETTY_PRINT
                            );
                            exit;
                        }
                    }

                    if(empresas::cadastro_empresas($fantasia, $razao, $cnpj, $tipo_cadastro) == true) //class + function
                    {
                        $informacoes = empresas::mostrar_empresa();
                        echo json_encode([
                                'status' => 200,
                                'api_versao' => API_VERSION,
                                'mensagem' => 'Empresa cadastrada com sucesso',
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