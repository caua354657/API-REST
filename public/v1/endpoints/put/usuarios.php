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
                if($_SERVER['REQUEST_METHOD'] == 'PUT')
                {
                    if(isset($_GET['id']))
                    {
                        $id = intval($_GET['id'] ?? null);

                        $input = json_decode(file_get_contents("php://input"), true);
                        $usuario = $input['usuario'] ?? null;
                        $nome = $input['nome'] ?? null;
                        $senha = $input['senha'] ?? null;
                        $categoria = $input['categoria'] ?? null;
                        $id_empresa = intval($input['id_empresa'] ?? null);

                        $sql = "select * from usuarios where id = '$id'"; // para verificar se existe o id informado pelo usuário
                        $result = $conexao->query($sql);
                        $linha = $result->fetch_assoc();

                        $sql_empresa_id = "select * from empresas where id = $id_empresa";
                        $dados = $conexao->query($sql_empresa_id);
                        $row = $dados->fetch_assoc();
                        
                        if($result->num_rows > 0) //se haver um id ou mais de usuario, vai executar
                        {
                            if($dados->num_rows > 0) //se haver um id_empresa ou mais de empresa, vai executar
                            {
                                if(usuarios::alterar_usuario($id, $usuario, $nome, $senha, $categoria, $id_empresa) == true)
                                {
                                            $informacoes = usuarios::mostrar_alterado_usuario($id);
                                            echo json_encode([
                                                        'status' => 200, 
                                                        'api_versao' => API_VERSION,
                                                        'mensagem' => 'Usuário alterado com sucesso', 
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
                                            JSON_PRETTY_PRINT
                                        );
                                }
                            }
                            else
                            {
                                http_response_code(404);
                                echo json_encode([
                                        'status' => 404,
                                        'api_versao' => API_VERSION,
                                        'mensagem' => 'ID_empresa não encontrado',
                                        'data_hora_resposta' => date('d-m-Y H:i:s')], 
                                        JSON_PRETTY_PRINT);
                            }
                        }
                        else
                        {
                            http_response_code(404);
                            echo json_encode([
                                        'status' => 404,
                                        'api_versao' => API_VERSION,
                                        'mensagem' => 'ID Usuário não encontrado',
                                        'data_hora_resposta' => date('d-m-Y H:i:s')], 
                                        JSON_PRETTY_PRINT);
                        }
                    }
                    else
                    {
                        http_response_code(400);
                        echo json_encode([
                                        "status" => 400,
                                        'api_versao' => API_VERSION,
                                        "mensagem" => "ID_usuário indefinido",
                                        'data_hora_resposta' => date('d-m-Y H:i:s'),],
                                        JSON_PRETTY_PRINT
                                    );
                    }
                }
                else
                {
                        http_response_code(405);
                        echo json_encode([
                            "status" => 405,
                            'api_versao' => API_VERSION,
                            'data_hora_resposta' => date('d-m-Y H:i:s'),
                            "mensagem" => "Método não é PUT"],
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