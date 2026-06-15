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
                if($_SERVER['REQUEST_METHOD'] == "POST")
                {
                    $nome = trim($_POST['nome'] ?? null);
                    $cpf = trim($_POST['cpf'] ?? null);
                    $rg = trim($_POST['rg'] ?? null);
                    $telefone = trim($_POST['telefone'] ?? null);
                    $empresa_id = intval(trim($_POST['empresa_id'] ?? null));
                    $cargo_id = intval(trim($_POST['cargo_id'] ?? null));
                    $ingresso = trim($_POST['ingresso'] ?? null);
                    $enviado_catraca = trim($_POST['enviado_catraca'] ?? null);
                    $importado = trim($_POST['importado'] ?? null);
                    $impresso = trim($_POST['impresso'] ?? null);

                    //verificar cpf já existente e rg
                    $select = "select * from pessoas where cpf = '$cpf' or rg = '$rg'";
                    $dados = $conexao->query($select);
                    $linha = $dados->fetch_assoc();

                    if($dados->num_rows > 0)
                    {
                        http_response_code(409);
                        echo json_encode([
                                "status" => 409,
                                'api_versao' => API_VERSION,
                                'data_hora_resposta' => date('d-m-Y H:i:s'),
                                "mensagem" => "CPF ou RG Existente. Tente outro"],
                                JSON_PRETTY_PRINT
                            );
                            exit;
                    }

                    $pasta = __DIR__ . "/../../../../foto/";
                    if(!is_dir($pasta))
                        mkdir($pasta);

                    $foto = $_FILES['foto']['name'] ?? null;

                    if(isset($_FILES['foto']) && !empty($_FILES['foto']['name']))
                    {
                        $foto = uniqid() . "-" . $_FILES['foto']['name'];
                        $uploadfoto = $pasta . $foto;
                    }
                    else
                        $foto = "Nenhuma Foto";

                    $array = [$nome, $cpf, $rg, $telefone, $empresa_id, $cargo_id, $ingresso, $enviado_catraca, $importado, $impresso];

                    foreach($array as $valor)
                    {
                        if(empty($valor))
                        {
                            http_response_code(400);
                            echo json_encode([
                                "status" => 400,
                                'api_versao' => API_VERSION,
                                'data_hora_resposta' => date('d-m-Y H:i:s'),
                                "mensagem" => "Todos os campos requeridos, exceto foto"],
                                JSON_PRETTY_PRINT
                            );
                            exit;
                        }
                    }

                    if(!empty($_FILES['foto']['name']))
                    {
                        if(move_uploaded_file($_FILES['foto']['tmp_name'], $uploadfoto))
                        {
                            if(pessoas::cadastro_pessoas($nome, $cpf, $rg, $telefone, $foto, $empresa_id, $cargo_id, $ingresso, $enviado_catraca, $importado, $impresso))
                            {
                                $informacoes = pessoas::mostrar_pessoas();
                                echo json_encode([
                                    'status' => 200,
                                    'api_versao' => API_VERSION,
                                    'mensagem' => 'Pessoa cadastrada com sucesso',
                                    'data_hora_resposta' => date('d-m-Y H:i:s'),
                                    'informacoes' => $informacoes
                                ], JSON_PRETTY_PRINT);
                            }
                            else
                            {
                                http_response_code(500);
                                echo json_encode([
                                    "status" => 500,
                                    'api_versao' => API_VERSION,
                                    "data_hora_resposta" => date('d-m-Y H:i:s'),
                                    "situacao_cadastro" => "Erro ao cadastrar",
                                    "erro" => $conexao->error
                                ], JSON_PRETTY_PRINT);
                            }
                        }
                    }    
                    else
                    {
                        if(pessoas::cadastro_pessoas($nome, $cpf, $rg, $telefone, $foto, $empresa_id, $cargo_id, $ingresso, $enviado_catraca, $importado, $impresso))
                        {
                                $informacoes = pessoas::mostrar_pessoas();
                                echo json_encode([
                                    'status' => 200,
                                    'api_versao' => API_VERSION,
                                    'mensagem' => 'Pessoa cadastrada com sucesso',
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
                                    "data_hora_resposta" => date('d-m-Y H:i:s'),
                                    "situacao_cadastro" => "Erro ao cadastrar",
                                    "erro" => $conexao->error], 
                                    JSON_PRETTY_PRINT);
                        }
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