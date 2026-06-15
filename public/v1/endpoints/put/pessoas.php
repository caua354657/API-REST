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
                        $nome = $input['nome'] ?? null;
                        $cpf = $input['cpf'] ?? null;
                        $rg = $input['rg'] ?? null;
                        $telefone = $input['telefone'] ?? null;
                        $foto = $_FILES['foto']['name'] ?? "Nenhuma Foto";
                        $empresa_id = $input['empresa_id'] ?? null;
                        $cargo_id = $input['cargo_id'] ?? null;
                        $ingresso = $input['ingresso'] ?? null;
                        $enviado_catraca = $input['enviado_catraca'] ?? null;
                        $importado = $input['importado'] ?? null;
                        $impresso = $input['impresso'] ?? null;

                        $sql = "select * from pessoas where id = '$id'";
                        $result = $conexao->query($sql);
                        $row = $result->fetch_assoc();

                        $sql_empresa = "select * from empresas where id = '$empresa_id'";
                        $resultado = $conexao->query($sql_empresa);
                        $line = $resultado->fetch_assoc();

                        $sql_cargo = "select * from cargos where id = '$cargo_id'";
                        $dados = $conexao->query($sql_cargo);
                        $linha = $dados->fetch_assoc();
                        
                        if($result->num_rows > 0)
                        {
                            if($resultado->num_rows > 0)
                            {
                                if($dados->num_rows > 0)
                                {
                                    if(pessoas::alterar_pessoa($id, $nome, $cpf, $rg, $telefone, $foto, $empresa_id, $cargo_id, $ingresso, $enviado_catraca, $importado, $impresso) == true)
                                    {         
                                            $informacoes = pessoas::mostrar_alterado_pessoa($id);
                                            echo json_encode([
                                                        'status' => 200, 
                                                        'api_versao' => API_VERSION,
                                                        'mensagem' => 'Pessoa alterada com sucesso', 
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
                                                        "situacao_cadastro" => "Erro ao alterar",
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
                                                        "mensagem" => "Cargo não encontrado",
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
                            http_response_code(404);
                            echo json_encode([
                                                'status' => 404, 
                                                'api_versao' => API_VERSION,
                                                'mensagem' => 'Pessoa não encontrada', 
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
                                            "mensagem" => "ID_Pessoa indefinido",
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
        {       http_response_code(401);
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