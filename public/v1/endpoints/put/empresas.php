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

                        $sql = "select * from empresas where id = '$id'"; //para verificar se essa empresa existe no BD
                        $resultado = $conexao->query($sql);
                        $linha = $resultado->fetch_assoc();

                        $input = json_decode(file_get_contents("php://input"), true); 
                        $nome_fantasia = $input['nome_fantasia'] ?? null;
                        $razao_social = $input['razao_social'] ?? null;
                        $tipo = $input['tipo'] ?? null;
                        $cnpj = $input['cnpj'] ?? null;
                        $importado = $input['importado'] ?? null;

                        $sql_tipo = "select * from tipo_cadastro where id = '$tipo'";
                        $dados = $conexao->query($sql_tipo);
                        $row = $dados->fetch_assoc();

                        if($resultado->num_rows > 0)
                        {
                            if($dados->num_rows > 0)
                            {
                                if(empresas::alterar_empresa($id, $nome_fantasia, $razao_social, $tipo, $cnpj, $importado) == true)
                                {         
                                        $informacoes = empresas::mostrar_alterado_empresa($id);
                                        echo json_encode([
                                                    'status' => 200, 
                                                    'api_versao' => API_VERSION,
                                                    'mensagem' => 'Empresa alterada com sucesso', 
                                                    'data_hora_resposta' => date('d-m-Y H:i:s'),
                                                    'informacoes' => $informacoes],
                                                    JSON_PRETTY_PRINT
                                        );       
                                }
                                else
                                {
                                        http_response_code(500);
                                        echo json_encode([
                                                "status" => 500,
                                                "api_versao" => API_VERSION,
                                                "data_hora_resposta" => date('d-m-Y H:i:s'),
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
                                            "status" => 404,
                                            'api_versao' => API_VERSION,
                                            "mensagem" => "Tipo não encontrado",
                                            'data_hora_resposta' => date('d-m-Y H:i:s'),],
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
                                                'mensagem' => 'Empresa não encontrada', 
                                                'data_hora_resposta' => date('d-m-Y H:i:s')],
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
                                            "mensagem" => "ID_empresa indefinido",
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